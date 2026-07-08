<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pengaturan KKM (Dinamis per Tahun Ajaran)
        Schema::create('pengaturan_kkm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->enum('jenjang', ['PAUD', 'SD', 'SMP', 'SMA']);
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->integer('nilai_kkm')->default(70);
            $table->timestamps();
        });

        // 2. Pengaturan Naik Kelas (Tanggal & Threshold)
        Schema::create('pengaturan_naik_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->date('tanggal_pengambilan_rapor');
            $table->date('tanggal_eksekusi')->nullable();
            $table->integer('persentase_minimal_tuntas')->default(70);
            $table->timestamps();
        });

        // 3. Izin Naik Kelas Khusus (Dispensasi Bendahara)
        Schema::create('izin_naik_kelas_khusus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->foreignId('diajukan_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->text('alasan_pengajuan')->nullable();
            $table->decimal('total_tunggakan', 15, 2)->default(0);
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->enum('status', ['MENUNGGU', 'DISETUJUI', 'DITOLAK'])->default('MENUNGGU');
            $table->text('catatan_ketua')->nullable();
            $table->timestamps();
        });

        // 4. Status/Riwayat Kenaikan Kelas Siswa
        Schema::create('status_naik_kelas_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->string('kelas_asal')->nullable();
            $table->string('kelas_tujuan')->nullable();
            $table->foreignId('original_kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->enum('status_pembayaran', ['LUNAS', 'BELUM_LUNAS']);
            $table->decimal('persentase_nilai_tuntas', 5, 2)->default(0);
            $table->integer('jumlah_mapel_tuntas')->default(0);
            $table->integer('total_mapel')->default(0);
            $table->enum('status_kelulusan', [
                'NAIK_KELAS',
                'LULUS',
                'TIDAK_NAIK_KELAS',
                'NAIK_KELAS_TUNGGAKAN',
                'LULUS_TUNGGAKAN',
            ]);
            $table->boolean('izin_khusus_ketua')->default(false);
            $table->date('tanggal_eksekusi')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamp('rolled_back_at')->nullable();
            $table->foreignId('rolled_back_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_naik_kelas_siswa');
        Schema::dropIfExists('izin_naik_kelas_khusus');
        Schema::dropIfExists('pengaturan_naik_kelas');
        Schema::dropIfExists('pengaturan_kkm');
    }
};
