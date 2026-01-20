<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Events\NotificationCreated;

class NotificationService
{
    /**
     * Create a notification
     */
    public function create($userId, $tipe, $judul, $pesan, $link = null, $data = null)
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'tipe' => $tipe,
            'judul' => $judul,
            'pesan' => $pesan,
            'link' => $link,
            'data' => $data,
            'icon' => Notification::getIcon($tipe),
            'color' => Notification::getColor($tipe),
        ]);

        // Broadcast for real-time (if event exists)
        if (class_exists(NotificationCreated::class)) {
            event(new NotificationCreated($notification));
        }

        return $notification;
    }

    /**
     * Notify siswa in a kelas about new materi
     */
    public function notifyMateriNew($materi)
    {
        $siswaList = Siswa::where('kelas_id', $materi->kelas_id)->get();

        foreach ($siswaList as $siswa) {
            if ($siswa->user_id) {
                $this->create(
                    $siswa->user_id,
                    Notification::TIPE_MATERI,
                    'Materi Baru: ' . $materi->judul_materi,
                    'Guru telah mengupload materi baru untuk ' . ($materi->mataPelajaran->nama_mapel ?? 'Mata Pelajaran'),
                    route('siswa.lms.mapel.materi', [$materi->mata_pelajaran_id, $materi->id]),
                    ['materi_id' => $materi->id, 'mapel_id' => $materi->mata_pelajaran_id]
                );
            }
        }
    }

    /**
     * Notify siswa in a kelas about new tugas
     */
    public function notifyTugasNew($tugas)
    {
        $siswaList = Siswa::where('kelas_id', $tugas->kelas_id)->get();
        $label = $tugas->jenis_tugas === 'latihan' ? 'Latihan' : 'Tugas';

        foreach ($siswaList as $siswa) {
            if ($siswa->user_id) {
                $this->create(
                    $siswa->user_id,
                    Notification::TIPE_TUGAS,
                    $label . ' Baru: ' . $tugas->judul_tugas,
                    'Deadline: ' . $tugas->tanggal_deadline->format('d M Y'),
                    route('siswa.lms.mapel.tugas.show', [$tugas->mata_pelajaran_id, $tugas->id]),
                    ['tugas_id' => $tugas->id, 'mapel_id' => $tugas->mata_pelajaran_id, 'jenis' => $tugas->jenis_tugas]
                );
            }
        }
    }

    /**
     * Notify siswa in a kelas about new ujian
     */
    public function notifyUjianNew($ujian)
    {
        $siswaList = Siswa::where('kelas_id', $ujian->kelas_id)->get();

        foreach ($siswaList as $siswa) {
            if ($siswa->user_id) {
                $this->create(
                    $siswa->user_id,
                    Notification::TIPE_UJIAN,
                    'Ujian Baru: ' . $ujian->judul_ujian,
                    'Jadwal: ' . $ujian->tanggal_mulai->format('d M Y, H:i'),
                    route('siswa.lms.mapel.ujian.show', [$ujian->mata_pelajaran_id, $ujian->id]),
                    ['ujian_id' => $ujian->id, 'mapel_id' => $ujian->mata_pelajaran_id, 'tipe' => $ujian->tipe_ujian]
                );
            }
        }
    }

    /**
     * Notify about deadline reminder (1 day before)
     */
    public function notifyDeadlineReminder($tugas)
    {
        $siswaList = Siswa::where('kelas_id', $tugas->kelas_id)
            ->whereDoesntHave('tugasSiswa', function ($q) use ($tugas) {
                $q->where('tugas_id', $tugas->id)
                    ->where('status', '!=', 'belum_dikerjakan');
            })->get();

        foreach ($siswaList as $siswa) {
            if ($siswa->user_id) {
                $this->create(
                    $siswa->user_id,
                    Notification::TIPE_DEADLINE,
                    'Pengingat Deadline',
                    $tugas->judul_tugas . ' akan berakhir besok!',
                    route('siswa.lms.mapel.tugas.show', [$tugas->mata_pelajaran_id, $tugas->id]),
                    ['tugas_id' => $tugas->id]
                );
            }
        }
    }

    /**
     * Notify guru about new forum question
     */
    public function notifyForumQuestion($forumDiskusi)
    {
        // Get guru for this mapel and kelas
        $guruPengajarList = \App\Models\GuruPengajarKelas::where('kelas_id', $forumDiskusi->kelas_id)
            ->where('mata_pelajaran_id', $forumDiskusi->mata_pelajaran_id)
            ->with('tenagaPendidik.user')
            ->get();

        foreach ($guruPengajarList as $gpk) {
            if ($gpk->tenagaPendidik && $gpk->tenagaPendidik->user_id) {
                $this->create(
                    $gpk->tenagaPendidik->user_id,
                    Notification::TIPE_FORUM,
                    'Pertanyaan Baru dari Siswa',
                    $forumDiskusi->judul,
                    route('guru.lms.kelas.forum.show', [$forumDiskusi->kelas_id, $forumDiskusi->id]),
                    ['forum_id' => $forumDiskusi->id, 'topik' => $forumDiskusi->topik]
                );
            }
        }
    }

    /**
     * Notify siswa about forum reply from teacher
     */
    public function notifyForumReply($forumReply)
    {
        $forumDiskusi = $forumReply->forumDiskusi;

        // Notify the original poster
        if ($forumDiskusi->user_id !== $forumReply->user_id) {
            $this->create(
                $forumDiskusi->user_id,
                Notification::TIPE_FORUM,
                'Balasan pada Diskusi Anda',
                'Guru telah menjawab pertanyaan Anda',
                route('siswa.lms.mapel.forum.show', [$forumDiskusi->mata_pelajaran_id, $forumDiskusi->id]),
                ['forum_id' => $forumDiskusi->id]
            );
        }
    }

    /**
     * Notify siswa about nilai update
     */
    public function notifyNilaiUpdate($tugasSiswa)
    {
        if ($tugasSiswa->siswa && $tugasSiswa->siswa->user_id) {
            $tugas = $tugasSiswa->tugas;
            $this->create(
                $tugasSiswa->siswa->user_id,
                Notification::TIPE_NILAI,
                'Nilai ' . ($tugas->getLabel() ?? 'Tugas') . ' Sudah Keluar',
                'Nilai: ' . $tugasSiswa->nilai,
                route('siswa.lms.mapel.tugas.show', [$tugas->mata_pelajaran_id, $tugas->id]),
                ['tugas_id' => $tugas->id, 'nilai' => $tugasSiswa->nilai]
            );
        }
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)->unread()->count();
    }

    /**
     * Get recent notifications for user
     */
    public function getRecent($userId, $limit = 5)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark all as read for user
     */
    public function markAllAsRead($userId)
    {
        Notification::where('user_id', $userId)
            ->unread()
            ->update(['read_at' => now()]);
    }
}
