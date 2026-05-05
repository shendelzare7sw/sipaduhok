<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Pembayaran;
use App\Models\TenagaPendidik;
use App\Models\ForumReply;
use App\Events\NotificationCreated;

class NotificationService
{
    /**
     * Create a notification
     */
    public function create($userId, $tipe, $judul, $pesan, $link = null, $data = null)
    {
        // Normalize link to a relative path so it stays valid across environments
        // (prevents storing http://sipaduhok.test/... in production DB)
        if ($link && filter_var($link, FILTER_VALIDATE_URL)) {
            $path  = parse_url($link, PHP_URL_PATH) ?? '/';
            $query = parse_url($link, PHP_URL_QUERY);
            $link  = $path . ($query ? '?' . $query : '');
        }

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

        // Determine if this is latihan or ujian based on tipe_ujian
        $isLatihan = $ujian->tipe_ujian === 'latihan';
        $tipeLabel = $isLatihan ? 'Latihan' : 'Ujian';

        foreach ($siswaList as $siswa) {
            if ($siswa->user_id) {
                $this->create(
                    $siswa->user_id,
                    Notification::TIPE_UJIAN,
                    $tipeLabel . ' Baru: ' . $ujian->judul_ujian,
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
     * Notify all siswa in a kelas about a new forum from guru
     */
    public function notifyForumNew($forumDiskusi)
    {
        $siswaList = Siswa::where('kelas_id', $forumDiskusi->kelas_id)
            ->whereNotNull('user_id')
            ->get();

        foreach ($siswaList as $siswa) {
            $this->create(
                $siswa->user_id,
                Notification::TIPE_FORUM,
                'Diskusi Baru dari Guru',
                $forumDiskusi->judul,
                route('siswa.lms.mapel.forum.show', [$forumDiskusi->mata_pelajaran_id, $forumDiskusi->id]),
                ['forum_id' => $forumDiskusi->id]
            );
        }
    }

    /**
     * Notify the right party when a forum reply is posted.
     *
     * - Guru replies (nested)     → notify only the student being replied to.
     * - Guru replies (top-level)  → notify all students who have participated.
     * - Siswa replies (anything)  → notify only the guru (forum creator),
     *                               with the guru-side link. No other students.
     */
    public function notifyForumReply($forumReply)
    {
        $forumDiskusi = $forumReply->forumDiskusi;
        $replierIsTeacher = TenagaPendidik::where('user_id', $forumReply->user_id)->exists();

        if ($replierIsTeacher) {
            // ── Guru membalas ──────────────────────────────────────────────
            if ($forumReply->parent_id) {
                // Balasan ke reply tertentu → notifikasi author reply itu jika siswa
                $parent = $forumReply->parent;
                if ($parent && $parent->user_id !== $forumReply->user_id) {
                    $parentIsTeacher = TenagaPendidik::where('user_id', $parent->user_id)->exists();
                    if (!$parentIsTeacher) {
                        $this->create(
                            $parent->user_id,
                            Notification::TIPE_FORUM,
                            'Guru Menjawab Balasan Anda',
                            'Di diskusi "' . $forumDiskusi->judul . '"',
                            route('siswa.lms.mapel.forum.show', [$forumDiskusi->mata_pelajaran_id, $forumDiskusi->id]),
                            ['forum_id' => $forumDiskusi->id]
                        );
                    }
                }
            } else {
                // Balasan top-level → notifikasi semua siswa yang pernah ikut thread ini
                $participantIds = ForumReply::where('forum_diskusi_id', $forumDiskusi->id)
                    ->where('id', '!=', $forumReply->id)
                    ->where('user_id', '!=', $forumReply->user_id)
                    ->pluck('user_id')
                    ->unique();

                foreach ($participantIds as $userId) {
                    $isTeacher = TenagaPendidik::where('user_id', $userId)->exists();
                    if (!$isTeacher) {
                        $this->create(
                            $userId,
                            Notification::TIPE_FORUM,
                            'Guru Membalas di Diskusi',
                            'Di diskusi "' . $forumDiskusi->judul . '"',
                            route('siswa.lms.mapel.forum.show', [$forumDiskusi->mata_pelajaran_id, $forumDiskusi->id]),
                            ['forum_id' => $forumDiskusi->id]
                        );
                    }
                }
            }
        } else {
            // ── Siswa membalas ─────────────────────────────────────────────
            // Hanya notifikasi guru (pemilik forum) dengan link sisi guru.
            // Siswa lain TIDAK dinotifikasi agar tidak bercampur.
            if ($forumDiskusi->user_id !== $forumReply->user_id) {
                $this->create(
                    $forumDiskusi->user_id,
                    Notification::TIPE_FORUM,
                    'Siswa Membalas Diskusi',
                    ($forumReply->user->name ?? 'Siswa') . ' di "' . $forumDiskusi->judul . '"',
                    route('guru.lms.forum.show', [$forumDiskusi->kelas_id, $forumDiskusi->mata_pelajaran_id, $forumDiskusi->id]),
                    ['forum_id' => $forumDiskusi->id]
                );
            }
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
     * Notify wali kelas about new izin request from orang tua
     */
    public function notifyIzinBaru($presensi)
    {
        $siswa = $presensi->siswa;
        if (!$siswa || !$siswa->kelas_id)
            return;

        // Get wali kelas for this class
        $waliKelasAssignment = \App\Models\WaliKelasAssignment::where('kelas_id', $siswa->kelas_id)
            ->with('tenagaPendidik.user')
            ->first();

        if ($waliKelasAssignment && $waliKelasAssignment->tenagaPendidik && $waliKelasAssignment->tenagaPendidik->user_id) {
            $this->create(
                $waliKelasAssignment->tenagaPendidik->user_id,
                Notification::TIPE_IZIN,
                'Pengajuan Izin: ' . $siswa->nama_lengkap,
                'Izin ' . ucfirst($presensi->status) . ' untuk tanggal ' . $presensi->tanggal->format('d M Y'),
                route('wali.presensi.validasi-izin'),
                ['presensi_id' => $presensi->id, 'siswa_id' => $siswa->id]
            );
        }
    }

    /**
     * Notify orang tua about izin status update
     */
    public function notifyIzinStatus($presensi)
    {
        $siswa = $presensi->siswa;
        if (!$siswa)
            return;

        // Notify all parents of this student
        $parents = $siswa->orangTua;
        foreach ($parents as $parent) {
            if ($parent->user_id) {
                $statusText = $presensi->status_validasi === 'disetujui' ? 'Disetujui' : 'Ditolak';
                $this->create(
                    $parent->user_id,
                    Notification::TIPE_IZIN,
                    'Status Izin: ' . $statusText,
                    'Pengajuan izin ' . $siswa->nama_lengkap . ' telah ' . strtolower($statusText),
                    route('orang-tua.presensi.riwayat-izin', $siswa->id),
                    ['presensi_id' => $presensi->id, 'status' => $presensi->status_validasi]
                );
            }
        }
    }

    /**
     * Notify users about catatan from Ketua/Waka/Admin
     */
    public function notifyCatatan($catatan)
    {
        $pengirim = $catatan->pengirim;
        $pengirimName = $pengirim ? $pengirim->name : 'Pimpinan';

        // Determine target users based on catatan type
        if ($catatan->tipe_penerima === 'semua') {
            // Send to all active users except the sender
            $targetUsers = User::where('is_active', true)
                ->where('id', '!=', $catatan->pengirim_id)
                ->get();
            foreach ($targetUsers as $user) {
                $this->create(
                    $user->id,
                    Notification::TIPE_CATATAN,
                    'Catatan dari ' . $pengirimName,
                    $catatan->judul,
                    route('notifications.index'),
                    ['catatan_id' => $catatan->id, 'prioritas' => $catatan->prioritas]
                );
            }
        } elseif ($catatan->tipe_penerima === 'role') {
            // Send to all users with specific role, except the sender
            $targetUsers = User::where('role', $catatan->role_penerima)
                ->where('id', '!=', $catatan->pengirim_id)
                ->get();
            foreach ($targetUsers as $user) {
                $this->create(
                    $user->id,
                    Notification::TIPE_CATATAN,
                    'Catatan dari ' . $pengirimName,
                    $catatan->judul,
                    route('notifications.index'),
                    ['catatan_id' => $catatan->id, 'prioritas' => $catatan->prioritas]
                );
            }
        } elseif ($catatan->penerima_id) {
            // Send to specific user
            $this->create(
                $catatan->penerima_id,
                Notification::TIPE_CATATAN,
                'Catatan dari ' . $pengirimName,
                $catatan->judul,
                route('notifications.index'),
                ['catatan_id' => $catatan->id, 'prioritas' => $catatan->prioritas]
            );
        }
    }

    /**
     * Notify bendahara/admin about new payment needing validation
     */
    public function notifyPembayaranBaru($pembayaran)
    {
        $siswa = $pembayaran->siswa;
        $siswaName = $siswa ? $siswa->nama_lengkap : 'Siswa';

        // Notify all bendahara and admin
        $targetUsers = User::whereIn('role', ['bendahara', 'admin'])->get();
        foreach ($targetUsers as $user) {
            $route = $user->role === 'admin' 
                ? route('admin.keuangan.pembayaran.show', $pembayaran->id)
                : route('bendahara.pembayaran.show', $pembayaran->id);

            $this->create(
                $user->id,
                Notification::TIPE_PEMBAYARAN,
                'Pembayaran Baru: ' . $siswaName,
                'Rp ' . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . ' (' . ucfirst($pembayaran->metode_pembayaran) . ')',
                $route,
                ['pembayaran_id' => $pembayaran->id, 'siswa_id' => $pembayaran->siswa_id]
            );
        }
    }

    /**
     * Notify orang tua about payment validation
     */
    public function notifyPembayaranValidasi($pembayaran)
    {
        $siswa = $pembayaran->siswa;
        if (!$siswa)
            return;

        // Notify all parents of this student
        $parents = $siswa->orangTua;
        foreach ($parents as $parent) {
            if ($parent->user_id) {
                $this->create(
                    $parent->user_id,
                    Notification::TIPE_PEMBAYARAN,
                    'Pembayaran Berhasil Divalidasi',
                    'Pembayaran Rp ' . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . ' telah divalidasi',
                    route('orang-tua.tagihan.anak', $siswa->id),
                    ['pembayaran_id' => $pembayaran->id]
                );
            }
        }
    }

    /**
     * Notify orang tua about new tagihan
     */
    public function notifyTagihanBaru($tagihan)
    {
        $siswa = $tagihan->siswa;
        if (!$siswa)
            return;

        // Notify all parents of this student
        $parents = $siswa->orangTua;
        foreach ($parents as $parent) {
            if ($parent->user_id) {
                $this->create(
                    $parent->user_id,
                    Notification::TIPE_PEMBAYARAN,
                    'Tagihan Baru: ' . $tagihan->jenis_tagihan,
                    'Rp ' . number_format($tagihan->jumlah, 0, ',', '.') . ' - ' . $siswa->nama_lengkap,
                    route('orang-tua.tagihan.anak', $siswa->id),
                    ['tagihan_id' => $tagihan->id, 'siswa_id' => $siswa->id]
                );
            }
        }
    }

    /**
     * Notify siswa and orang tua about rapor terbit
     */
    public function notifyRaporTerbit($rapor)
    {
        $siswa = $rapor->siswa;
        if (!$siswa)
            return;

        $semesterText = $rapor->jenis_rapor === 'uts' ? 'Tengah Semester' : 'Akhir Semester';

        // Notify siswa - DISABLED: Siswa tidak berhak akses rapor
        // if ($siswa->user_id) {
        //     $this->create(
        //         $siswa->user_id,
        //         Notification::TIPE_RAPOR,
        //         'Rapor ' . $semesterText . ' Tersedia',
        //         'Rapor semester ' . $rapor->semester . ' sudah bisa dilihat',
        //         route('siswa.sia.rapor.index'),
        //         ['rapor_id' => $rapor->id]
        //     );
        // }

        // Notify orang tua
        $parents = $siswa->orangTua;
        foreach ($parents as $parent) {
            if ($parent->user_id) {
                $this->create(
                    $parent->user_id,
                    Notification::TIPE_RAPOR,
                    'Rapor ' . $siswa->nama_lengkap . ' Tersedia',
                    'Rapor ' . $semesterText . ' sudah bisa dilihat',
                    route('orang-tua.rapor.anak', $siswa->id),
                    ['rapor_id' => $rapor->id, 'siswa_id' => $siswa->id]
                );
            }
        }
    }

    /**
     * Notify guru about tugas submitted by siswa
     */
    public function notifyTugasDikumpulkan($tugasSiswa)
    {
        $tugas = $tugasSiswa->tugas;
        $siswa = $tugasSiswa->siswa;
        if (!$tugas || !$siswa)
            return;

        // Get guru for this mapel and kelas
        $guruPengajarList = \App\Models\GuruPengajarKelas::where('kelas_id', $tugas->kelas_id)
            ->where('mata_pelajaran_id', $tugas->mata_pelajaran_id)
            ->with('tenagaPendidik.user')
            ->get();

        foreach ($guruPengajarList as $gpk) {
            if ($gpk->tenagaPendidik && $gpk->tenagaPendidik->user_id) {
                $this->create(
                    $gpk->tenagaPendidik->user_id,
                    Notification::TIPE_TUGAS,
                    'Tugas Dikumpulkan: ' . $siswa->nama_lengkap,
                    $tugas->judul_tugas,
                    route('guru.lms.tugas.koreksi', [$tugas->kelas_id, $tugas->mata_pelajaran_id, $tugas->id]),
                    ['tugas_id' => $tugas->id, 'tugas_siswa_id' => $tugasSiswa->id]
                );
            }
        }
    }

    /**
     * Notify guru about ujian completed by siswa
     */
    public function notifyUjianSelesai($ujianSiswa)
    {
        $ujian = $ujianSiswa->ujian;
        $siswa = $ujianSiswa->siswa;
        if (!$ujian || !$siswa)
            return;

        // Determine if this is latihan or ujian based on tipe_ujian
        $isLatihan = $ujian->tipe_ujian === 'latihan';
        $tipeLabel = $isLatihan ? 'Latihan' : 'Ujian';

        // Get appropriate route based on type
        $routeName = $isLatihan ? 'guru.lms.latihan.hasil' : 'guru.lms.ujian.hasil';

        // Get guru for this mapel and kelas
        $guruPengajarList = \App\Models\GuruPengajarKelas::where('kelas_id', $ujian->kelas_id)
            ->where('mata_pelajaran_id', $ujian->mata_pelajaran_id)
            ->with('tenagaPendidik.user')
            ->get();

        foreach ($guruPengajarList as $gpk) {
            if ($gpk->tenagaPendidik && $gpk->tenagaPendidik->user_id) {
                $this->create(
                    $gpk->tenagaPendidik->user_id,
                    Notification::TIPE_UJIAN,
                    $tipeLabel . ' Selesai: ' . $siswa->nama_lengkap,
                    $ujian->judul_ujian . ' - Nilai: ' . $ujianSiswa->nilai,
                    route($routeName, [$ujian->kelas_id, $ujian->mata_pelajaran_id, $ujian->id]),
                    ['ujian_id' => $ujian->id, 'ujian_siswa_id' => $ujianSiswa->id, 'tipe' => $ujian->tipe_ujian]
                );
            }
        }
    }

    /**
     * Notify about pengumuman from kalender akademik
     */
    public function notifyPengumuman($kalenderAkademik, $targetRole = null)
    {
        $query = User::query();

        if ($targetRole) {
            $query->where('role', $targetRole);
        } else {
            // Default: notify all siswa
            $query->where('role', 'siswa');
        }

        $targetUsers = $query->get();
        foreach ($targetUsers as $user) {
            $this->create(
                $user->id,
                Notification::TIPE_PENGUMUMAN,
                'Pengumuman: ' . $kalenderAkademik->judul,
                'Tanggal: ' . $kalenderAkademik->tanggal_mulai->format('d M Y'),
                route('siswa.lms.kalender'),
                ['kalender_id' => $kalenderAkademik->id]
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
     * Get recent UNREAD notifications for user (bell dropdown)
     */
    public function getRecent($userId, $limit = 5)
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
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

    /**
     * Notify Admin & Bendahara about new bulk payment
     */
    public function notifyNewPayment($pembayaranIds, $user)
    {
        $count = count($pembayaranIds);
        $pembayaran = \App\Models\Pembayaran::find($pembayaranIds[0]);

        if (!$pembayaran) return;

        $amount = \App\Models\Pembayaran::whereIn('id', $pembayaranIds)->sum('jumlah_bayar');
        $siswaName = $pembayaran->siswa->nama_lengkap ?? 'Siswa';

        // Notify Bendahara & Admin
        $targets = User::whereIn('role', ['admin', 'bendahara'])->get();

        foreach ($targets as $target) {
            $route = $target->role === 'admin'
                ? route('admin.keuangan.pembayaran.index')
                : route('bendahara.pembayaran.index');

            $this->create(
                $target->id,
                Notification::TIPE_PEMBAYARAN,
                'Pembayaran Baru (' . $count . ' Item)',
                $user->name . ' membayar Rp ' . number_format($amount, 0, ',', '.') . ' untuk ' . $siswaName,
                $route, // Link to appropriate index based on role
                ['siswa_id' => $pembayaran->siswa_id]
            );
        }
    }

    /**
     * Notify orang tua about payment rejection
     */
    public function notifyPembayaranDitolak($pembayaran, $alasan = null)
    {
        $siswa = $pembayaran->siswa;
        if (!$siswa)
            return;

        // Notify all parents of this student
        $parents = $siswa->orangTua;
        foreach ($parents as $parent) {
            if ($parent->user_id) {
                $pesan = 'Pembayaran Rp ' . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . ' ditolak';
                if ($alasan) {
                    $pesan .= '. Alasan: ' . $alasan;
                }

                $this->create(
                    $parent->user_id,
                    Notification::TIPE_PEMBAYARAN,
                    'Pembayaran Ditolak',
                    $pesan,
                    route('orang-tua.tagihan.anak', $siswa->id),
                    ['pembayaran_id' => $pembayaran->id, 'alasan' => $alasan]
                );
            }
        }
    }

    /**
     * Notify orang tua about ujian access validation
     */
    public function notifyValidasiAksesUjian($siswa, $status = 'disetujui')
    {
        if (!$siswa)
            return;

        $statusText = $status === 'disetujui' ? 'Diizinkan' : 'Dibatalkan';
        $pesan = $status === 'disetujui'
            ? $siswa->nama_lengkap . ' sudah dapat mengikuti ujian'
            : 'Akses ujian ' . $siswa->nama_lengkap . ' telah dibatalkan';

        // Notify all parents of this student
        $parents = $siswa->orangTua;
        foreach ($parents as $parent) {
            if ($parent->user_id) {
                $this->create(
                    $parent->user_id,
                    Notification::TIPE_PEMBAYARAN,
                    'Akses Ujian ' . $statusText,
                    $pesan,
                    route('orang-tua.tagihan.anak', $siswa->id),
                    ['siswa_id' => $siswa->id, 'status' => $status, 'tipe' => 'ujian']
                );
            }
        }

        // Notify siswa
        if ($siswa->user_id) {
            $this->create(
                $siswa->user_id,
                Notification::TIPE_PEMBAYARAN,
                'Akses Ujian ' . $statusText,
                $pesan,
                route('siswa.lms.dashboard'),
                ['status' => $status, 'tipe' => 'ujian']
            );
        }
    }

    /**
     * Notify orang tua about rapor access validation
     */
    public function notifyValidasiAksesRapor($siswa, $status = 'disetujui')
    {
        if (!$siswa)
            return;

        $statusText = $status === 'disetujui' ? 'Diizinkan' : 'Dibatalkan';
        $pesan = $status === 'disetujui'
            ? $siswa->nama_lengkap . ' sudah dapat mengakses rapor'
            : 'Akses rapor ' . $siswa->nama_lengkap . ' telah dibatalkan';

        // Notify all parents of this student
        $parents = $siswa->orangTua;
        foreach ($parents as $parent) {
            if ($parent->user_id) {
                $this->create(
                    $parent->user_id,
                    Notification::TIPE_RAPOR,
                    'Akses Rapor ' . $statusText,
                    $pesan,
                    route('orang-tua.rapor.anak', $siswa->id),
                    ['siswa_id' => $siswa->id, 'status' => $status, 'tipe' => 'rapor']
                );
            }
        }
    }

    /**
     * Notify orang tua about bulk tagihan created
     */
    public function notifyTagihanBulk($siswaIds, $jenisTagihan, $jumlah)
    {
        $siswaList = Siswa::whereIn('id', $siswaIds)->with('orangTua')->get();

        foreach ($siswaList as $siswa) {
            $parents = $siswa->orangTua;
            foreach ($parents as $parent) {
                if ($parent->user_id) {
                    $this->create(
                        $parent->user_id,
                        Notification::TIPE_PEMBAYARAN,
                        'Tagihan Baru: ' . $jenisTagihan,
                        'Rp ' . number_format($jumlah, 0, ',', '.') . ' - ' . $siswa->nama_lengkap,
                        route('orang-tua.tagihan.anak', $siswa->id),
                        ['siswa_id' => $siswa->id, 'jenis' => $jenisTagihan, 'jumlah' => $jumlah]
                    );
                }
            }
        }
    }

    /**
     * Notify guru when assigned as wali kelas
     */
    public function notifyWaliKelasAssignment($waliKelasAssignment)
    {
        $tenagaPendidik = $waliKelasAssignment->tenagaPendidik;
        $kelas = $waliKelasAssignment->kelas;

        if (!$tenagaPendidik || !$tenagaPendidik->user_id || !$kelas)
            return;

        $this->create(
            $tenagaPendidik->user_id,
            Notification::TIPE_KELAS,
            'Penugasan Wali Kelas',
            'Anda ditugaskan sebagai Wali Kelas ' . $kelas->nama_kelas,
            route('wali.dashboard'),
            ['kelas_id' => $kelas->id, 'wali_kelas_assignment_id' => $waliKelasAssignment->id]
        );
    }

    /**
     * Notify siswa and orang tua when assigned to a class
     */
    public function notifyPlottingSiswa($siswa)
    {
        if (!$siswa || !$siswa->kelas)
            return;

        $kelas = $siswa->kelas;

        // Notify siswa
        if ($siswa->user_id) {
            $this->create(
                $siswa->user_id,
                Notification::TIPE_KELAS,
                'Penempatan Kelas',
                'Anda telah ditempatkan di kelas ' . $kelas->nama_kelas,
                route('siswa.sia.dashboard'),
                ['kelas_id' => $kelas->id]
            );
        }

        // Notify orang tua
        $parents = $siswa->orangTua;
        foreach ($parents as $parent) {
            if ($parent->user_id) {
                $this->create(
                    $parent->user_id,
                    Notification::TIPE_KELAS,
                    'Penempatan Kelas: ' . $siswa->nama_lengkap,
                    $siswa->nama_lengkap . ' telah ditempatkan di kelas ' . $kelas->nama_kelas,
                    route('orang-tua.dashboard'),
                    ['siswa_id' => $siswa->id, 'kelas_id' => $kelas->id]
                );
            }
        }
    }

    /**
     * Notify users about new pengumuman
     */
    public function notifyPengumumanBaru($pengumuman)
    {
        $targetRoles = $pengumuman->target_role ? explode(',', $pengumuman->target_role) : ['siswa', 'guru', 'orang_tua'];

        $targetUsers = User::whereIn('role', $targetRoles)->get();
        foreach ($targetUsers as $user) {
            // Role-specific routes
            $route = match($user->role) {
                'siswa' => route('siswa.lms.kalender'),
                'guru' => route('guru.dashboard'),
                'orang_tua' => route('orang-tua.dashboard'),
                default => route('notifications.index'),
            };

            $this->create(
                $user->id,
                Notification::TIPE_PENGUMUMAN,
                'Pengumuman: ' . $pengumuman->judul,
                substr(strip_tags($pengumuman->isi), 0, 100) . '...',
                $route,
                ['pengumuman_id' => $pengumuman->id]
            );
        }
    }

    /**
     * Notify users about new berita
     */
    public function notifyBeritaBaru($berita)
    {
        // Only notify featured/important news
        if (!($berita->is_featured ?? false))
            return;

        $targetRoles = ['siswa', 'guru', 'orang_tua'];
        $targetUsers = User::whereIn('role', $targetRoles)->get();

        foreach ($targetUsers as $user) {
            // Role-specific routes
            $route = match($user->role) {
                'siswa' => route('siswa.sia.dashboard'),
                'guru' => route('guru.dashboard'),
                'orang_tua' => route('orang-tua.dashboard'),
                default => route('notifications.index'),
            };

            $this->create(
                $user->id,
                Notification::TIPE_PENGUMUMAN,
                'Berita Terbaru: ' . $berita->judul,
                substr(strip_tags($berita->konten), 0, 100) . '...',
                $route,
                ['berita_id' => $berita->id]
            );
        }
    }

    /**
     * Send welcome notification to new user
     */
    public function notifyWelcome($user)
    {
        if (!$user)
            return;

        $roleLabel = match($user->role) {
            'admin' => 'Administrator',
            'ketua_pkbm' => 'Ketua PKBM',
            'waka' => 'Wakil Kepala',
            'bendahara' => 'Bendahara',
            'sekretaris' => 'Sekretaris',
            'wali_kelas' => 'Wali Kelas',
            'guru' => 'Guru',
            'orang_tua' => 'Orang Tua',
            'siswa' => 'Siswa',
            default => 'Pengguna',
        };

        // Role-specific routes
        $route = match($user->role) {
            'admin' => route('admin.dashboard'),
            'ketua_pkbm' => route('ketua.dashboard'),
            'waka' => route('waka.dashboard'),
            'bendahara' => route('bendahara.dashboard'),
            'sekretaris' => route('sekretaris.dashboard'),
            'wali_kelas' => route('wali.dashboard'),
            'guru' => route('guru.dashboard'),
            'orang_tua' => route('orang-tua.dashboard'),
            'siswa' => route('siswa.sia.dashboard'),
            default => route('notifications.index'),
        };

        $this->create(
            $user->id,
            Notification::TIPE_SISTEM,
            'Selamat Datang!',
            'Selamat datang di SIPADUHOK sebagai ' . $roleLabel . '. Silakan lengkapi profil Anda dan mulai menggunakan sistem.',
            $route
        );
    }

    /**
     * Notify about promotion/kenaikan kelas pengajuan
     */
    public function notifyPromotionPengajuan($promotion)
    {
        // Notify bendahara & admin about new promotion submission
        $targets = User::whereIn('role', ['admin', 'bendahara'])->get();

        foreach ($targets as $target) {
            $route = $target->role === 'admin'
                ? route('admin.keuangan.promotion.validation.index')
                : route('bendahara.promotion.validation.index');

            $this->create(
                $target->id,
                Notification::TIPE_KENAIKAN,
                'Pengajuan Kenaikan Kelas',
                'Pengajuan kenaikan kelas untuk ' . ($promotion->kelas->nama_kelas ?? 'kelas'),
                $route,
                ['promotion_id' => $promotion->id, 'kelas_id' => $promotion->kelas_id]
            );
        }
    }

    /**
     * Notify Ketua PKBM when admin/bendahara submits promotion dispensasi
     */
    public function notifyPromotionDispensasiDiajukan($count, $pengaju)
    {
        $ketuaUsers = User::where('role', 'ketua_pkbm')->get();

        foreach ($ketuaUsers as $ketua) {
            $this->create(
                $ketua->id,
                Notification::TIPE_KENAIKAN,
                'Dispensasi Naik Kelas Baru',
                $pengaju->name . ' mengajukan dispensasi naik kelas untuk ' . $count . ' siswa. Menunggu keputusan Anda.',
                route('ketua.promotion.approval.index'),
                ['count' => $count, 'pengaju_id' => $pengaju->id]
            );
        }
    }

    /**
     * Notify pengaju (admin/bendahara) when Ketua decides on promotion dispensasi
     */
    public function notifyPromotionDispensasiKeputusan($ids, $status, $ketuaName)
    {
        $records = \Illuminate\Support\Facades\DB::table('izin_naik_kelas_khusus')
            ->whereIn('id', $ids)
            ->get();

        $pengajuIds = $records->pluck('diajukan_oleh')->unique();
        $count = count($ids);
        $statusLabel = $status === 'DISETUJUI' ? 'menyetujui' : 'menolak';

        foreach ($pengajuIds as $pengajuId) {
            $pengaju = User::find($pengajuId);
            if (!$pengaju) continue;

            $route = match($pengaju->role) {
                'admin' => route('admin.keuangan.promotion.validation.history'),
                'bendahara' => route('bendahara.promotion.validation.history'),
                default => route('notifications.index'),
            };

            $this->create(
                $pengaju->id,
                Notification::TIPE_KENAIKAN,
                'Keputusan Dispensasi Naik Kelas',
                'Ketua PKBM ' . $ketuaName . ' ' . $statusLabel . ' ' . $count . ' pengajuan dispensasi naik kelas.',
                $route,
                ['status' => $status, 'count' => $count]
            );
        }
    }

    // ===================================================================
    // RAPOR & UJIAN FLOW NOTIFICATIONS
    // ===================================================================

    /**
     * Notify Ketua PKBM when Wali Kelas submits rapor for validation.
     */
    public function notifyRaporDikirimKeKetua($siswa, $pengirim = null)
    {
        if (!$siswa) return;

        $pengirimName = $pengirim ? $pengirim->name : 'Wali Kelas';

        // Notify all Ketua PKBM users
        $ketuaUsers = User::where('role', 'ketua_pkbm')->get();
        foreach ($ketuaUsers as $ketua) {
            $this->create(
                $ketua->id,
                Notification::TIPE_RAPOR,
                'Rapor Menunggu Validasi',
                $pengirimName . ' mengirim rapor ' . $siswa->nama_lengkap . ' (' . ($siswa->kelas->nama_kelas ?? '-') . ') untuk divalidasi',
                route('ketua.validasi-rapor.index'),
                ['siswa_id' => $siswa->id]
            );
        }
    }

    /**
     * Notify Ketua PKBM when multiple rapor sent at once (bulk).
     */
    public function notifyRaporBulkDikirimKeKetua($count, $kelasName, $pengirim = null)
    {
        if ($count <= 0) return;

        $pengirimName = $pengirim ? $pengirim->name : 'Wali Kelas';

        $ketuaUsers = User::where('role', 'ketua_pkbm')->get();
        foreach ($ketuaUsers as $ketua) {
            $this->create(
                $ketua->id,
                Notification::TIPE_RAPOR,
                'Rapor Menunggu Validasi (' . $count . ' Siswa)',
                $pengirimName . ' mengirim ' . $count . ' rapor dari kelas ' . $kelasName . ' untuk divalidasi',
                route('ketua.validasi-rapor.index'),
                ['count' => $count, 'kelas' => $kelasName]
            );
        }
    }

    /**
     * Notify Wali Kelas & Bendahara when Ketua approves rapor.
     */
    public function notifyKetuaApproveRapor($siswa)
    {
        if (!$siswa) return;

        // Notify Wali Kelas of the student's class
        $waliKelasAssignment = \App\Models\WaliKelasAssignment::where('kelas_id', $siswa->kelas_id)
            ->with('tenagaPendidik.user')
            ->first();

        if ($waliKelasAssignment && $waliKelasAssignment->tenagaPendidik && $waliKelasAssignment->tenagaPendidik->user_id) {
            $this->create(
                $waliKelasAssignment->tenagaPendidik->user_id,
                Notification::TIPE_RAPOR,
                'Rapor Disetujui Ketua',
                'Rapor ' . $siswa->nama_lengkap . ' telah disetujui oleh Ketua PKBM. Menunggu validasi Bendahara.',
                route('wali.rapor.index'),
                ['siswa_id' => $siswa->id]
            );
        }

        // Notify Bendahara & Admin
        $targets = User::whereIn('role', ['bendahara', 'admin'])->get();
        foreach ($targets as $target) {
            $route = $target->role === 'admin'
                ? route('admin.keuangan.validasi-akses.index')
                : route('bendahara.validasi-akses.index');

            $this->create(
                $target->id,
                Notification::TIPE_RAPOR,
                'Rapor Siap Divalidasi',
                'Rapor ' . $siswa->nama_lengkap . ' (' . ($siswa->kelas->nama_kelas ?? '-') . ') sudah di-approve Ketua PKBM. Silakan validasi akses rapor.',
                $route,
                ['siswa_id' => $siswa->id]
            );
        }
    }

    /**
     * Notify Wali Kelas when Ketua rejects/cancels rapor validation.
     */
    public function notifyKetuaBatalkanRapor($siswa)
    {
        if (!$siswa) return;

        $waliKelasAssignment = \App\Models\WaliKelasAssignment::where('kelas_id', $siswa->kelas_id)
            ->with('tenagaPendidik.user')
            ->first();

        if ($waliKelasAssignment && $waliKelasAssignment->tenagaPendidik && $waliKelasAssignment->tenagaPendidik->user_id) {
            $this->create(
                $waliKelasAssignment->tenagaPendidik->user_id,
                Notification::TIPE_RAPOR,
                'Validasi Rapor Dibatalkan',
                'Ketua PKBM membatalkan validasi rapor ' . $siswa->nama_lengkap . '. Validasi bendahara juga di-reset.',
                route('wali.rapor.index'),
                ['siswa_id' => $siswa->id]
            );
        }
    }

    /**
     * Notify Wali Kelas when Ketua requests revision on rapor.
     */
    public function notifyKetuaMintaRevisi($siswa, $catatan)
    {
        if (!$siswa) return;

        $waliKelasAssignment = \App\Models\WaliKelasAssignment::where('kelas_id', $siswa->kelas_id)
            ->with('tenagaPendidik.user')
            ->first();

        if ($waliKelasAssignment && $waliKelasAssignment->tenagaPendidik && $waliKelasAssignment->tenagaPendidik->user_id) {
            $this->create(
                $waliKelasAssignment->tenagaPendidik->user_id,
                Notification::TIPE_RAPOR,
                'Rapor Perlu Revisi',
                'Ketua PKBM meminta revisi rapor ' . $siswa->nama_lengkap . ': ' . \Str::limit($catatan, 80),
                route('wali.rapor.index'),
                ['siswa_id' => $siswa->id]
            );
        }
    }

    /**
     * Notify Wali Kelas when Orang Tua requests rapor download.
     */
    public function notifyRequestDownloadRapor($downloadRequest)
    {
        if (!$downloadRequest) return;

        $siswa = $downloadRequest->siswa;
        $parentName = $downloadRequest->user->name ?? 'Orang Tua';

        if (!$siswa) return;

        $waliKelasAssignment = \App\Models\WaliKelasAssignment::where('kelas_id', $siswa->kelas_id)
            ->with('tenagaPendidik.user')
            ->first();

        if ($waliKelasAssignment && $waliKelasAssignment->tenagaPendidik && $waliKelasAssignment->tenagaPendidik->user_id) {
            $this->create(
                $waliKelasAssignment->tenagaPendidik->user_id,
                Notification::TIPE_RAPOR,
                'Request Download Rapor',
                $parentName . ' mengajukan download rapor ' . $siswa->nama_lengkap,
                route('wali.rapor.request-download.index'),
                ['request_id' => $downloadRequest->id, 'siswa_id' => $siswa->id]
            );
        }
    }

    /**
     * Notify Orang Tua when Wali Kelas approves/rejects download request.
     */
    public function notifyKeputusanDownloadRapor($downloadRequest)
    {
        if (!$downloadRequest || !$downloadRequest->user_id) return;

        $siswa = $downloadRequest->siswa;
        $siswaName = $siswa ? $siswa->nama_lengkap : 'anak';
        $isApproved = $downloadRequest->status === 'disetujui';

        $judul = $isApproved ? 'Download Rapor Disetujui' : 'Download Rapor Ditolak';
        $pesan = $isApproved
            ? 'Permintaan download rapor ' . $siswaName . ' telah disetujui. Link berlaku 24 jam.'
            : 'Permintaan download rapor ' . $siswaName . ' telah ditolak.';

        $link = $siswa ? route('orang-tua.rapor.anak', $siswa->id) : route('orang-tua.dashboard');

        $this->create(
            $downloadRequest->user_id,
            Notification::TIPE_RAPOR,
            $judul,
            $pesan,
            $link,
            ['request_id' => $downloadRequest->id, 'status' => $downloadRequest->status]
        );
    }

    /**
     * Notify Ketua PKBM when Bendahara/Admin submits dispensasi request.
     */
    public function notifyDispensasiDiajukan($count, $tipe, $pengaju)
    {
        if ($count <= 0) return;

        $pengajuName = $pengaju ? $pengaju->name : 'Bendahara';
        $tipeLabel = $tipe === 'ujian' ? 'Ujian' : 'Rapor';

        $ketuaUsers = User::where('role', 'ketua_pkbm')->get();
        foreach ($ketuaUsers as $ketua) {
            $this->create(
                $ketua->id,
                Notification::TIPE_RAPOR,
                'Dispensasi ' . $tipeLabel . ' Baru',
                $pengajuName . ' mengajukan dispensasi ' . strtolower($tipeLabel) . ' untuk ' . $count . ' siswa. Menunggu keputusan Anda.',
                route('ketua.dispensasi.index'),
                ['count' => $count, 'tipe' => $tipe]
            );
        }
    }

    /**
     * Notify pengaju (Bendahara/Admin) when Ketua approves/rejects dispensasi.
     */
    public function notifyKeputusanDispensasi($pengajuanList, $status, $catatan = null)
    {
        if ($pengajuanList->isEmpty()) return;

        $isApproved = $status === 'disetujui';
        $statusText = $isApproved ? 'Disetujui' : 'Ditolak';

        // Group by pengaju to avoid duplicate notifications
        $grouped = $pengajuanList->groupBy('diajukan_oleh');

        foreach ($grouped as $pengajuId => $items) {
            $pengaju = User::find($pengajuId);
            if (!$pengaju) continue;

            $tipe = $items->first()->tipe;
            $tipeLabel = $tipe === 'ujian' ? 'Ujian' : 'Rapor';
            $count = $items->count();

            $route = match($pengaju->role) {
                'admin' => route('admin.keuangan.validasi-akses.index'),
                'bendahara' => route('bendahara.validasi-akses.index'),
                default => route('notifications.index'),
            };

            $pesan = 'Dispensasi ' . strtolower($tipeLabel) . ' untuk ' . $count . ' siswa telah ' . strtolower($statusText) . ' oleh Ketua PKBM.';
            if ($catatan) {
                $pesan .= ' Catatan: ' . \Str::limit($catatan, 80);
            }

            $this->create(
                $pengajuId,
                Notification::TIPE_RAPOR,
                'Dispensasi ' . $tipeLabel . ' ' . $statusText,
                $pesan,
                $route,
                ['count' => $count, 'status' => $status, 'tipe' => $tipe]
            );
        }
    }

    /**
     * Notify siswa about ujian nilai (separate from tugas nilai)
     */
    public function notifyUjianNilaiUpdate($ujianSiswa)
    {
        $ujian = $ujianSiswa->ujian;
        $siswa = $ujianSiswa->siswa;
        if (!$ujian || !$siswa || !$siswa->user_id)
            return;

        $isLatihan = $ujian->tipe_ujian === 'latihan';
        $tipeLabel = $isLatihan ? 'Latihan' : 'Ujian';

        $this->create(
            $siswa->user_id,
            Notification::TIPE_NILAI,
            'Nilai ' . $tipeLabel . ' Sudah Keluar',
            $ujian->judul_ujian . ' - Nilai: ' . $ujianSiswa->nilai,
            route('siswa.lms.mapel.ujian.show', [$ujian->mata_pelajaran_id, $ujian->id]),
            ['ujian_id' => $ujian->id, 'nilai' => $ujianSiswa->nilai, 'tipe' => $ujian->tipe_ujian]
        );
    }
    
    /**
     * Notify guru about a monitoring catatan from Kepsek/Wakepsek/Admin.
     */
    public function notifyCatatanMonitoring(\App\Models\CatatanMonitoring $catatan)
    {
        $catatan->loadMissing(['guru.user', 'pengirim']);

        $guruUser = $catatan->guru?->user;
        if (!$guruUser) {
            return;
        }

        $pengirimName = $catatan->pengirim?->name ?? 'Pimpinan';
        $kontenLabel = $catatan->kontenLabel();
        $judulKonten = $catatan->kontenJudul();

        $this->create(
            $guruUser->id,
            Notification::TIPE_CATATAN,
            'Catatan Monitoring dari ' . $pengirimName,
            $kontenLabel . ' "' . \Illuminate\Support\Str::limit($judulKonten, 60) . '": ' . \Illuminate\Support\Str::limit($catatan->isi_catatan, 100),
            route('guru.lms.catatan-monitoring.show', $catatan->id),
            [
                'catatan_monitoring_id' => $catatan->id,
                'konten_type' => $catatan->konten_type,
                'konten_id' => $catatan->konten_id,
            ]
        );
    }

    /**
     * Notify Admin about new Recovery Ticket that needs intervention
     */
    public function notifyAdminTicketPemulihan($ticket)
    {
        $admins = User::where('role', 'admin')->get();
        $userName = $ticket->user->name ?? 'User';
        
        foreach ($admins as $admin) {
            $this->create(
                $admin->id,
                Notification::TIPE_RECOVERY,
                'Tiket Pemulihan: ' . $userName,
                'User membutuhkan bantuan pemulihan akun.',
                route('admin.recovery-tickets.index'),
                ['ticket_id' => $ticket->id]
            );
        }
    }

    /**
     * Notify User about their ticket resolution (if they get access and login later)
     */
    public function notifyUserTicketResolved($ticket)
    {
        if ($ticket->user_id) {
            $this->create(
                $ticket->user_id,
                Notification::TIPE_RECOVERY,
                'Pemulihan Berhasil',
                'Status tiket pemulihan Anda telah diselesaikan oleh Admin.',
                null,
                ['ticket_id' => $ticket->id]
            );
        }
    }
}
