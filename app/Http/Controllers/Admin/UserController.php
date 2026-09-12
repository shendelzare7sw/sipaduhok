<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\StudentParent;
use App\Models\TenagaPendidik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $tenagaPendidik = TenagaPendidik::with('user')->latest()->take(10)->get();
        $siswa = Siswa::with('user', 'kelas')->latest()->take(10)->get();
        $orangTua = User::where('role', 'orang_tua')
            ->with(['studentParents.siswa.kelas'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'totalTenagaPendidik' => TenagaPendidik::count(),
            'totalSiswa' => Siswa::count(),
            'totalUserAktif' => User::where('is_active', true)->count(),
            'totalUserNonAktif' => User::where('is_active', false)->count(),
        ];

        return view('admin.users.index', compact('tenagaPendidik', 'siswa', 'orangTua', 'stats'));
    }

    private function userValidationMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi agar data bisa disimpan.',
            'required_with' => ':attribute wajib diisi jika :values sudah dipilih.',
            'exclude_unless' => ':attribute wajib diisi sesuai opsi yang dipilih.',
            'email' => ':attribute harus berupa alamat email yang valid.',
            'unique' => ':attribute sudah digunakan. Gunakan data lain.',
            'min' => ':attribute minimal :min karakter.',
            'max' => ':attribute maksimal :max karakter.',
            'in' => ':attribute tidak sesuai pilihan yang tersedia.',
            'exists' => ':attribute tidak ditemukan pada data sistem.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'boolean' => ':attribute harus bernilai aktif/nonaktif yang valid.',
            'confirmed' => 'Konfirmasi password harus sama dengan password.',
            'array' => ':attribute harus berupa pilihan data yang valid.',
            'string' => ':attribute harus berupa teks.',
            'nullable' => ':attribute boleh dikosongkan.',
        ];
    }

    private function userValidationAttributes(): array
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'personal_email' => 'Email pemulihan',
            'password' => 'Password',
            'password_confirmation' => 'Konfirmasi password',
            'role' => 'Role/Jabatan',
            'cabang_id' => 'Cabang penempatan',
            'nama_lengkap' => 'Nama lengkap',
            'name' => 'Nama lengkap wali siswa',
            'nip' => 'NIP',
            'jenis_kelamin' => 'Jenis kelamin',
            'telepon' => 'No. telepon/WA',
            'phone' => 'No. telepon/WA',
            'tempat_lahir' => 'Tempat lahir',
            'tanggal_lahir' => 'Tanggal lahir',
            'pendidikan_terakhir' => 'Pendidikan terakhir',
            'alamat' => 'Alamat lengkap',
            'address' => 'Alamat',
            'kelas_id' => 'Kelas',
            'nisn' => 'NISN',
            'nis' => 'NIS',
            'tanggal_masuk' => 'Tanggal masuk',
            'agama' => 'Agama',
            'status' => 'Status siswa',
            'is_active' => 'Status akun',
            'nama_ayah' => 'Nama ayah',
            'nama_ibu' => 'Nama ibu',
            'telepon_orangtua' => 'No. telepon wali siswa',
            'parent_option' => 'Opsi akun wali siswa',
            'parent_id' => 'Wali siswa yang dipilih',
            'existing_relationship' => 'Hubungan wali siswa',
            'existing_relationship_lainnya' => 'Hubungan keluarga lainnya',
            'parent_name' => 'Nama lengkap wali siswa baru',
            'parent_username' => 'Username wali siswa baru',
            'parent_email' => 'Email wali siswa baru',
            'parent_password' => 'Password wali siswa baru',
            'parent_phone' => 'No. telepon wali siswa baru',
            'new_relationship' => 'Hubungan wali siswa baru',
            'new_relationship_lainnya' => 'Hubungan keluarga lainnya',
            'remove_parents' => 'Data wali siswa yang dihapus',
            'add_parent_option' => 'Opsi tambah wali siswa',
            'add_existing_parent_id' => 'Wali siswa yang akan dihubungkan',
            'add_existing_relationship' => 'Hubungan wali siswa yang dihubungkan',
            'add_existing_relationship_lainnya' => 'Hubungan keluarga lainnya',
            'add_new_parent_name' => 'Nama lengkap wali siswa baru',
            'add_new_parent_username' => 'Username wali siswa baru',
            'add_new_parent_email' => 'Email wali siswa baru',
            'add_new_parent_password' => 'Password wali siswa baru',
            'add_new_parent_phone' => 'No. telepon wali siswa baru',
            'add_new_relationship' => 'Hubungan wali siswa baru',
            'add_new_relationship_lainnya' => 'Hubungan keluarga lainnya',
            'siswa_ids' => 'Siswa yang dipilih',
            'siswa_ids.*' => 'Siswa yang dipilih',
            'hubungan_keluarga' => 'Hubungan keluarga',
            'hubungan_keluarga_lainnya' => 'Hubungan keluarga lainnya',
            'relationships' => 'Hubungan keluarga',
            'relationships.*' => 'Hubungan keluarga',
            'relationships_lainnya' => 'Hubungan keluarga lainnya',
            'relationships_lainnya.*' => 'Hubungan keluarga lainnya',
        ];
    }
    // --- TENAGA PENDIDIK ---

    public function tenagaPendidik(Request $request)
    {
        // Available roles for filter
        $roles = [
            'ketua_pkbm' => 'Ketua PKBM',
            'wakil_kepala_sekolah' => 'Wakil Kepala Sekolah',
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            'wali_kelas' => 'Wali Kelas',
            'guru_pengajar' => 'Guru Pengajar',
        ];

        // Query Users directly instead of TenagaPendidik to ensure we get all users with these roles
        // even if they haven't set up their TenagaPendidik profile yet.
        $query = User::whereIn('role', array_keys($roles))->with('tenagaPendidik');

        // Handle search parameter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('tenagaPendidik', function ($q2) use ($search) {
                        $q2->where('nip', 'like', "%{$search}%")
                            ->orWhere('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }

        // Handle role filter
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        // Order by name
        $tenagaPendidik = $query->orderBy('name')->paginate(15);

        return view('admin.users.tenaga-pendidik', compact('tenagaPendidik', 'roles'));
    }

    public function printTenagaPendidik(Request $request)
    {
        // Available roles for filter
        $roles = [
            'ketua_pkbm' => 'Ketua PKBM',
            'wakil_kepala_sekolah' => 'Wakil Kepala Sekolah',
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            'wali_kelas' => 'Wali Kelas',
            'guru_pengajar' => 'Guru Pengajar',
        ];

        $query = User::whereIn('role', array_keys($roles))->with('tenagaPendidik');

        // Handle search parameter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('tenagaPendidik', function ($q2) use ($search) {
                        $q2->where('nip', 'like', "%{$search}%")
                            ->orWhere('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }

        // Handle role filter
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        // Get all data without pagination
        $tenagaPendidik = $query->orderBy('name')->get();

        return view('admin.users.print.tenaga-pendidik', compact('tenagaPendidik', 'roles', 'request'));
    }

    public function createTenagaPendidik()
    {
        $cabangList = Cabang::where('is_active', true)->get();
        $roles = ['ketua_pkbm', 'wakil_kepala_sekolah', 'sekretaris', 'bendahara', 'wali_kelas', 'guru_pengajar'];

        return view('admin.users.tenaga-pendidik-create', compact('cabangList', 'roles'));
    }

    public function storeTenagaPendidik(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'personal_email' => 'nullable|email|max:255',
            'username' => 'required|string|unique:users,username|max:50',
            'password' => 'required|string|min:8',
            'role' => 'required|in:ketua_pkbm,wakil_kepala_sekolah,sekretaris,bendahara,wali_kelas,guru_pengajar',
            'cabang_id' => 'required|exists:cabang,id',
            'nip' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
            'pendidikan_terakhir' => 'required|string|max:100',
        ], $this->userValidationMessages(), $this->userValidationAttributes());

        $user = User::create([
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'personal_email' => $validated['personal_email'] ?? null,
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'role_id' => Role::where('name', $validated['role'])->value('id'),
            'cabang_id' => $validated['cabang_id'],
            'phone' => $validated['telepon'],
            'is_active' => true,
        ]);

        TenagaPendidik::create([
            'user_id' => $user->id,
            'nip' => $validated['nip'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'alamat' => $validated['alamat'],
            'telepon' => $validated['telepon'],
            'email' => $validated['email'],
            'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
        ]);

        return redirect_to_previous('admin.users.tenaga-pendidik')->with('success', 'Tenaga Pendidik berhasil ditambahkan!');
    }

    public function editTenagaPendidik(int $id)
    {
        // Prioritize finding by user_id first to avoid ID collisions
        $tenagaPendidik = TenagaPendidik::with('user')->where('user_id', $id)->first();

        if (! $tenagaPendidik) {
            // Fallback: Check if it's a direct ID, or if it's a User ID without a profile yet
            $tenagaPendidik = TenagaPendidik::with('user')->find($id);

            if (! $tenagaPendidik) {
                // Check if User exists but profile is missing
                $user = User::find($id);
                if ($user && in_array($user->role, ['ketua_pkbm', 'wakil_kepala_sekolah', 'sekretaris', 'bendahara', 'wali_kelas', 'guru_pengajar'])) {
                    // Initialize an empty TenagaPendidik object with the user relationship for the view
                    $tenagaPendidik = new TenagaPendidik;
                    $tenagaPendidik->user_id = $user->id;
                    $tenagaPendidik->nama_lengkap = $user->name;
                    $tenagaPendidik->email = $user->email;
                    $tenagaPendidik->telepon = $user->phone; // Try to use user phone as default
                    $tenagaPendidik->setRelation('user', $user);
                } else {
                    abort(404);
                }
            }
        }

        $cabangList = Cabang::where('is_active', true)->get();
        $roles = ['ketua_pkbm', 'wakil_kepala_sekolah', 'sekretaris', 'bendahara', 'wali_kelas', 'guru_pengajar'];

        return view('admin.users.tenaga-pendidik-edit', compact('tenagaPendidik', 'cabangList', 'roles'));
    }

    public function updateTenagaPendidik(Request $request, int $id)
    {
        // Prioritize finding by user_id first
        $tenagaPendidik = TenagaPendidik::where('user_id', $id)->first();

        if (! $tenagaPendidik) {
            $tenagaPendidik = TenagaPendidik::where('id', $id)->first();

            // If still not found, check if it's a User ID we are trying to update (create profile for)
            if (! $tenagaPendidik) {
                $user = User::find($id);
                if (! $user) {
                    abort(404);
                }
                // Create new instance but don't save yet
                $tenagaPendidik = new TenagaPendidik;
                $tenagaPendidik->user_id = $user->id;
            }
        }

        $userId = $tenagaPendidik->user_id;

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'personal_email' => 'nullable|email|max:255',
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($userId)],
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:ketua_pkbm,wakil_kepala_sekolah,sekretaris,bendahara,wali_kelas,guru_pengajar',
            'cabang_id' => 'required|exists:cabang,id',
            'nip' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
            'pendidikan_terakhir' => 'required|string|max:100',
            'is_active' => 'required|boolean',
        ], $this->userValidationMessages(), $this->userValidationAttributes());

        $userData = [
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'personal_email' => $validated['personal_email'] ?? null,
            'username' => $validated['username'],
            'role' => $validated['role'],
            'role_id' => Role::where('name', $validated['role'])->value('id'),
            'cabang_id' => $validated['cabang_id'],
            'is_active' => $validated['is_active'],
            'phone' => $validated['telepon'],
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // Update User
        // If we only have a fresh TenagaPendidik model, getting ->user might be tricky if not set
        $user = User::findOrFail($userId);

        if ($user->isGuruPengajar() && $validated['role'] !== 'guru_pengajar' && $tenagaPendidik->exists) {
            $blockers = $this->tenagaPendidikBlockers($tenagaPendidik->id);

            if ($blockers !== []) {
                return back()->withInput()->withErrors([
                    'role' => 'Role Guru Pengajar belum dapat diubah karena masih terhubung ke '.implode(', ', $blockers).'. Pindahkan atau kosongkan tanggung jawab tersebut terlebih dahulu.',
                ]);
            }
        }

        $user->update($userData);

        // Update or Create TenagaPendidik
        $tenagaPendidikData = [
            'user_id' => $user->id,
            'nip' => $validated['nip'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'alamat' => $validated['alamat'],
            'telepon' => $validated['telepon'],
            'email' => $validated['email'], // redundant but keeping if schema has it
            'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
        ];

        if ($tenagaPendidik->exists) {
            $tenagaPendidik->update($tenagaPendidikData);
        } else {
            TenagaPendidik::create($tenagaPendidikData);
        }

        return redirect_to_previous('admin.users.tenaga-pendidik')->with('success', 'Tenaga Pendidik berhasil diupdate!');
    }

    public function showTenagaPendidik(int $id)
    {
        $tenagaPendidik = TenagaPendidik::with(['user.cabang'])->where('user_id', $id)->first();
        if (! $tenagaPendidik) {
            $tenagaPendidik = TenagaPendidik::with(['user.cabang'])->find($id);
        }

        if (! $tenagaPendidik) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        return view('admin.users.tenaga-pendidik-show', compact('tenagaPendidik'));
    }

    /**
     * Jejak data yang menghalangi penghapusan seorang tenaga pendidik (guru/wali).
     * Kosong = aman dihapus. Dipakai guard deleteTenagaPendidik & bulkDeleteTenagaPendidik.
     * Menghapus guru ber-jejak akan cascade menghapus nilai siswa & rapor_nilai.
     */
    private function tenagaPendidikBlockers(int $tid): array
    {
        $b = [];
        if (($n = \App\Models\Nilai::where('guru_id', $tid)->count()) > 0) {
            $b[] = "{$n} nilai siswa";
        }
        if (($n = \App\Models\Ujian::where('guru_id', $tid)->count()) > 0) {
            $b[] = "{$n} ujian/latihan";
        }
        if (($n = \App\Models\Materi::where('guru_id', $tid)->count()) > 0) {
            $b[] = "{$n} materi";
        }
        if (($n = \App\Models\Tugas::where('guru_id', $tid)->count()) > 0) {
            $b[] = "{$n} tugas";
        }
        if (($n = \App\Models\GuruPengajarKelas::where('tenaga_pendidik_id', $tid)->count()) > 0) {
            $b[] = "{$n} penugasan mengajar";
        }
        if (($n = \App\Models\JadwalPelajaran::where('guru_id', $tid)->count()) > 0) {
            $b[] = "{$n} jadwal mengajar";
        }
        if (($n = \App\Models\WaliKelasAssignment::where('tenaga_pendidik_id', $tid)->count()) > 0) {
            $b[] = "{$n} penugasan wali kelas";
        }
        // Tiga tabel di bawah ini juga ON DELETE CASCADE ke tenaga_pendidik, tapi dulu
        // TIDAK ikut dicek - jadi guru yang cuma punya data di sini (mis. baru sempat
        // bikin kelas virtual, belum input nilai) bisa lolos dihapus dan datanya lenyap
        // diam-diam. Ditutup supaya tidak ada penghapusan yang menghancurkan data
        // tanpa peringatan.
        if (($n = \App\Models\LmsMeeting::where('guru_id', $tid)->count()) > 0) {
            $b[] = "{$n} kelas virtual";
        }
        if (Schema::hasTable('pertemuans')) {
            if (($n = \Illuminate\Support\Facades\DB::table('pertemuans')->where('guru_id', $tid)->count()) > 0) {
                $b[] = "{$n} pertemuan";
            }
        }
        if (($n = \Illuminate\Support\Facades\DB::table('catatan_monitoring')->where('guru_id', $tid)->count()) > 0) {
            $b[] = "{$n} catatan monitoring";
        }

        return $b;
    }

    /**
     * Jejak data yang menghalangi penghapusan seorang siswa.
     * Kosong = aman dihapus. Dipakai guard deleteSiswa & bulkDeleteSiswa.
     * Menghapus siswa ber-jejak akan cascade menghapus riwayat akademik & keuangan.
     */
    private function siswaBlockers(int $sid): array
    {
        $b = [];
        if (($n = \App\Models\Pembayaran::where('siswa_id', $sid)->count()) > 0) {
            $b[] = "{$n} pembayaran";
        }
        if (($n = \App\Models\Tagihan::where('siswa_id', $sid)->count()) > 0) {
            $b[] = "{$n} tagihan";
        }
        if (($n = \App\Models\Rapor::where('siswa_id', $sid)->count()) > 0) {
            $b[] = "{$n} rapor";
        }
        if (($n = \App\Models\UjianSiswa::where('siswa_id', $sid)->count()) > 0) {
            $b[] = "{$n} riwayat ujian";
        }
        if (($n = \App\Models\Presensi::where('siswa_id', $sid)->count()) > 0) {
            $b[] = "{$n} presensi";
        }
        if (($n = \App\Models\Nilai::where('siswa_id', $sid)->count()) > 0) {
            $b[] = "{$n} nilai";
        }

        return $b;
    }

    /**
     * Peran yang akunnya TIDAK BOLEH dihapus lewat menu manapun.
     * Admin = pemegang kunci sistem; Ketua PKBM = pejabat tertinggi yang jadi
     * penyetuju rapor/dispensasi (pengajuan_rapor_ketua memakai FK NO ACTION,
     * jadi menghapusnya bisa membuat pengajuan menggantung / gagal query).
     */
    private const ROLE_TIDAK_BISA_DIHAPUS = ['admin', 'ketua_pkbm'];

    private function tolakHapusRoleDilindungi(?User $user): ?string
    {
        if ($user && in_array($user->role, self::ROLE_TIDAK_BISA_DIHAPUS, true)) {
            $label = $user->role === 'admin' ? 'Admin' : 'Ketua PKBM';

            return "Akun {$label} tidak dapat dihapus demi keamanan sistem. "
                .'Kalau akun ini sudah tidak dipakai, NONAKTIFKAN saja (ubah status menjadi Nonaktif).';
        }

        return null;
    }

    public function deleteTenagaPendidik(int $id)
    {
        // Try to find the profile
        $tenagaPendidik = TenagaPendidik::where('id', $id)->orWhere('user_id', $id)->first();

        // Proteksi peran kunci - dicek SEBELUM apapun, di kedua cabang alur.
        $calonUser = $tenagaPendidik?->user ?? User::find($id);
        if ($pesan = $this->tolakHapusRoleDilindungi($calonUser)) {
            return redirect_to_previous('admin.users.tenaga-pendidik')->with('error', $pesan);
        }

        if ($tenagaPendidik) {
            // GUARD INTEGRITAS: jangan hard-delete guru yang masih punya jejak akademik.
            // FK cascade akan ikut memusnahkan nilai siswa, ujian_siswa/jawaban, materi,
            // tugas, rapor_nilai (rapor jadi rusak), dan penugasan. Nonaktifkan akun saja.
            $blockers = $this->tenagaPendidikBlockers($tenagaPendidik->id);

            if (! empty($blockers)) {
                return redirect_to_previous('admin.users.tenaga-pendidik')->with('error',
                    'Tenaga pendidik ini TIDAK DAPAT DIHAPUS karena masih terhubung ke data ('.implode(', ', $blockers).'). '
                    .'Berbeda dengan menghapus siswa yang hanya memusnahkan datanya sendiri, menghapus guru akan ikut '
                    .'memusnahkan NILAI, UJIAN, dan TUGAS milik BANYAK SISWA LAIN secara permanen. '
                    .'Kalau guru ini sudah tidak mengajar: NONAKTIFKAN akunnya, lalu tugaskan guru pengganti di kelas & '
                    .'mata pelajaran terkait - guru baru otomatis bisa melanjutkan materi, tugas, dan ujian yang sudah ada.');
            }

            $user = $tenagaPendidik->user;
            $tenagaPendidik->delete();
            if ($user) {
                $this->bersihkanSesiUser($user->id);
                $user->delete();
            }
        } else {
            // If profile not found, maybe we are trying to delete a User by ID directly
            $user = User::find($id);
            if ($user && in_array($user->role, ['ketua_pkbm', 'wakil_kepala_sekolah', 'sekretaris', 'bendahara', 'wali_kelas', 'guru_pengajar'])) {
                $this->bersihkanSesiUser($user->id);
                $user->delete();
            } else {
                abort(404);
            }
        }

        $this->bersihkanNotifikasiYatim();

        return redirect_to_previous('admin.users.tenaga-pendidik')->with('success', 'Tenaga Pendidik berhasil dihapus!');
    }

    // --- SISWA ---

    public function siswa(Request $request)
    {
        // termasukNonaktif(): halaman pengelolaan admin HARUS tetap melihat siswa
        // berakun nonaktif - kalau ikut disembunyikan, admin tidak punya jalan
        // untuk mengaktifkannya kembali atau menghapusnya.
        $query = Siswa::termasukNonaktif()->with('user', 'kelas.tahunAjaran', 'cabang');

        // Handle search parameter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Handle other filters
        if ($request->has('jenjang') && $request->jenjang != '') {
            $query->whereHas('kelas', function ($q) use ($request) {
                $q->where('jenjang', $request->jenjang);
            });
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        } elseif ($request->has('kelas_nama') && $request->kelas_nama != '') {
            $query->whereHas('kelas', function ($q) use ($request) {
                $q->where('nama_kelas', $request->kelas_nama);
            });
        }
        if ($request->has('cabang_id') && $request->cabang_id != '') {
            $query->where('cabang_id', $request->cabang_id);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $siswa = $query->orderBy('nama_lengkap')->paginate(15);
        $kelasList = Kelas::with('cabang')->orderBy('jenjang')->orderBy('nama_kelas')->get()
            ->unique(fn ($k) => $k->cabang_id.'|'.$k->jenjang.'|'.$k->nama_kelas)
            ->values();
        $cabangList = Cabang::where('is_active', true)->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        return view('admin.users.siswa', compact('siswa', 'kelasList', 'cabangList', 'jenjangs'));
    }

    public function printSiswa(Request $request)
    {
        // Ikut termasukNonaktif() supaya isi cetakan sama persis dgn daftar di layar.
        $query = Siswa::termasukNonaktif()->with('user', 'kelas.tahunAjaran', 'cabang');

        // Handle search parameter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Handle other filters
        if ($request->has('jenjang') && $request->jenjang != '') {
            $query->whereHas('kelas', function ($q) use ($request) {
                $q->where('jenjang', $request->jenjang);
            });
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        } elseif ($request->has('kelas_nama') && $request->kelas_nama != '') {
            $query->whereHas('kelas', function ($q) use ($request) {
                $q->where('nama_kelas', $request->kelas_nama);
            });
        }
        if ($request->has('cabang_id') && $request->cabang_id != '') {
            $query->where('cabang_id', $request->cabang_id);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $siswa = $query->orderBy('nama_lengkap')->get();

        // Prepare filter info for display
        $filterInfo = [];
        if ($request->jenjang) {
            $filterInfo[] = 'Jenjang: '.$request->jenjang;
        }
        if ($request->filled('kelas_id')) {
            $filterInfo[] = 'Kelas: '.(Kelas::find($request->kelas_id)?->nama_kelas ?? '-');
        } elseif ($request->kelas_nama) {
            $filterInfo[] = 'Kelas: '.$request->kelas_nama;
        }
        if ($request->cabang_id) {
            $cabang = Cabang::find($request->cabang_id);
            if ($cabang) {
                $filterInfo[] = 'Cabang: '.$cabang->nama_cabang;
            }
        }
        if ($request->status) {
            $filterInfo[] = 'Status: '.ucfirst($request->status);
        }

        return view('admin.users.print.siswa', compact('siswa', 'filterInfo'));
    }

    public function createSiswa()
    {
        $cabangList = Cabang::where('is_active', true)->get();
        $tahunAjaranAktif = \App\Models\TahunAjaran::where('is_active', true)->first();

        $kelasList = Kelas::when($tahunAjaranAktif, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
            ->with(['cabang'])
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();
        $orangTuaList = User::where('role', 'orang_tua')
            ->with(['studentParents.siswa'])
            ->orderBy('name')
            ->get();

        return view('admin.users.siswa-create', compact('cabangList', 'kelasList', 'orangTuaList'));
    }

    public function storeSiswa(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'personal_email' => 'nullable|email|max:255',
            'username' => 'required|string|unique:users,username|max:50',
            'password' => 'required|string|min:8',
            'kelas_id' => 'required|exists:kelas,id',
            'nisn' => 'required|string|max:20',
            'nis' => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'telepon_orangtua' => 'nullable|string|max:20',
            'tanggal_masuk' => 'required|date',
            'agama' => 'required|string|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'parent_option' => 'nullable|in:existing,new,none',
            'parent_id' => 'exclude_unless:parent_option,existing|required|exists:users,id',
            'existing_relationship' => 'exclude_unless:parent_option,existing|required|string',
            'existing_relationship_lainnya' => 'required_if:existing_relationship,lainnya|nullable|string|max:100',

            'parent_name' => 'exclude_unless:parent_option,new|required|string|max:255',
            'parent_username' => 'exclude_unless:parent_option,new|required|string|unique:users,username|max:50',
            'parent_email' => 'exclude_unless:parent_option,new|required|email|unique:users,email',
            'parent_password' => 'exclude_unless:parent_option,new|required|string|min:8',
            'parent_phone' => 'nullable|string|max:20',
            'new_relationship' => 'exclude_unless:parent_option,new|required|string',
            'new_relationship_lainnya' => 'required_if:new_relationship,lainnya|nullable|string|max:100',

            'is_primary' => 'nullable|boolean',
            'can_access_academic' => 'nullable|boolean',
        ], $this->userValidationMessages(), $this->userValidationAttributes());

        // Get cabang from kelas relationship
        $kelas = Kelas::findOrFail($validated['kelas_id']);

        $user = User::create([
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'] ?? $validated['username'].'@siswa.sipaduhok.sch.id',
            'personal_email' => $validated['personal_email'] ?? null,
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => 'siswa',
            'cabang_id' => $kelas->cabang_id,
            'is_active' => true,
        ]);

        $siswa = Siswa::create([
            'user_id' => $user->id,
            'cabang_id' => $kelas->cabang_id,
            'kelas_id' => $validated['kelas_id'],
            'nisn' => $validated['nisn'],
            'nis' => $validated['nis'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'alamat' => $validated['alamat'],
            'nama_ayah' => $validated['nama_ayah'],
            'nama_ibu' => $validated['nama_ibu'],
            'telepon_orangtua' => $validated['telepon_orangtua'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'agama' => $validated['agama'],
            'status' => 'aktif',
        ]);

        // Handle parent assignment
        if ($request->parent_option === 'existing' && $request->parent_id) {
            // Determine the actual relationship value
            $existingRelationship = $request->existing_relationship;
            if ($existingRelationship === 'lainnya' && ! empty($request->existing_relationship_lainnya)) {
                $existingRelationship = $request->existing_relationship_lainnya;
            }

            // Link to existing parent
            StudentParent::create([
                'siswa_id' => $siswa->id,
                'parent_id' => $request->parent_id,
                'relationship' => $existingRelationship,
                'is_primary' => true,
                'is_financial_responsible' => true,
                'can_access_academic' => true,
            ]);
        } elseif ($request->parent_option === 'new') {
            // Determine the actual relationship value
            $newRelationship = $request->new_relationship;
            if ($newRelationship === 'lainnya' && ! empty($request->new_relationship_lainnya)) {
                $newRelationship = $request->new_relationship_lainnya;
            }

            // Create new parent account
            $parentUser = User::create([
                'name' => $request->parent_name,
                'email' => $request->parent_email,
                'username' => $request->parent_username,
                'password' => Hash::make($request->parent_password),
                'phone' => $request->parent_phone,
                'cabang_id' => $kelas->cabang_id,
                'role' => 'orang_tua',
                'is_active' => true,
            ]);

            // Link parent to student
            StudentParent::create([
                'siswa_id' => $siswa->id,
                'parent_id' => $parentUser->id,
                'relationship' => $newRelationship,
                'is_primary' => $request->has('is_primary'),
                'is_financial_responsible' => true,
                'can_access_academic' => $request->has('can_access_academic'),
            ]);
        }

        return redirect_to_previous('admin.users.siswa')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function editSiswa(int $id)
    {
        $siswa = Siswa::termasukNonaktif()->with(['user', 'studentParents.parent'])->findOrFail($id);
        $cabangList = Cabang::where('is_active', true)->get();
        $tahunAjaranAktif = \App\Models\TahunAjaran::where('is_active', true)->first();

        $kelasList = Kelas::when($tahunAjaranAktif, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
            ->with(['cabang'])
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();
        $orangTuaList = User::where('role', 'orang_tua')
            ->with(['studentParents.siswa'])
            ->orderBy('name')
            ->get();

        return view('admin.users.siswa-edit', compact('siswa', 'cabangList', 'kelasList', 'orangTuaList'));
    }

    public function updateSiswa(Request $request, int $id)
    {
        $siswa = Siswa::termasukNonaktif()->findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($siswa->user_id)],
            'personal_email' => 'nullable|email|max:255',
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($siswa->user_id)],
            'password' => 'nullable|string|min:8',
            'kelas_id' => 'required|exists:kelas,id',
            'nisn' => 'required|string|max:20',
            'nis' => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'telepon_orangtua' => 'nullable|string|max:20',
            'tanggal_masuk' => 'required|date',
            'agama' => 'required|string|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'status' => 'required|in:aktif,lulus,pindah,keluar',
            'is_active' => 'required|boolean',
            'remove_parents' => 'nullable|array',
            'add_parent_option' => 'nullable|in:existing,new',
            'add_existing_parent_id' => 'exclude_unless:add_parent_option,existing|required|exists:users,id',
            'add_existing_relationship' => 'exclude_unless:add_parent_option,existing|required|string',
            'add_existing_relationship_lainnya' => 'required_if:add_existing_relationship,lainnya|nullable|string|max:100',
            'add_new_parent_name' => 'exclude_unless:add_parent_option,new|required|string|max:255',
            'add_new_parent_username' => 'exclude_unless:add_parent_option,new|required|string|unique:users,username|max:50',
            'add_new_parent_email' => 'exclude_unless:add_parent_option,new|required|email|unique:users,email',
            'add_new_parent_password' => 'exclude_unless:add_parent_option,new|required|string|min:8',
            'add_new_relationship' => 'exclude_unless:add_parent_option,new|required|string',
            'add_new_relationship_lainnya' => 'required_if:add_new_relationship,lainnya|nullable|string|max:100',
        ], $this->userValidationMessages(), $this->userValidationAttributes());

        // Get cabang from kelas relationship
        $kelas = Kelas::findOrFail($validated['kelas_id']);

        // Logic Sync Advanced (Bi-directional):

        // 1. Deteksi Perubahan Status Siswa
        if ($validated['status'] !== $siswa->status) {
            if (in_array($validated['status'], ['pindah', 'keluar'])) {
                $validated['is_active'] = 0;
            } elseif ($validated['status'] === 'aktif') {
                $validated['is_active'] = 1;
            }
        } else {
            if ($validated['status'] !== 'aktif' && $validated['status'] !== 'lulus' && $validated['is_active'] == '1') {
                $validated['status'] = 'aktif';
            }
        }

        $userData = [
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'] ?? $siswa->user->email,
            'personal_email' => $validated['personal_email'] ?? null,
            'username' => $validated['username'],
            'cabang_id' => $kelas->cabang_id,
            'is_active' => $validated['is_active'],
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $siswa->user->update($userData);

        $siswa->update([
            'cabang_id' => $kelas->cabang_id,
            'kelas_id' => $validated['kelas_id'],
            'nisn' => $validated['nisn'],
            'nis' => $validated['nis'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'alamat' => $validated['alamat'],
            'nama_ayah' => $validated['nama_ayah'],
            'nama_ibu' => $validated['nama_ibu'],
            'telepon_orangtua' => $validated['telepon_orangtua'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'agama' => $validated['agama'],
            'status' => $validated['status'],
        ]);

        // Handle removing parent relationships
        if ($request->has('remove_parents')) {
            $removeIds = array_filter($request->remove_parents);
            if (! empty($removeIds)) {
                StudentParent::whereIn('id', $removeIds)->delete();
            }
        }

        // Handle adding existing parent
        if ($request->add_parent_option === 'existing' && $request->add_existing_parent_id) {
            // Check if relationship already exists
            $exists = StudentParent::where('siswa_id', $siswa->id)
                ->where('parent_id', $request->add_existing_parent_id)
                ->exists();

            if (! $exists) {
                // Determine the actual relationship value
                $addExistingRelationship = $request->add_existing_relationship;
                if ($addExistingRelationship === 'lainnya' && ! empty($request->add_existing_relationship_lainnya)) {
                    $addExistingRelationship = $request->add_existing_relationship_lainnya;
                }

                // Check for duplicate ayah_kandung or ibu_kandung
                if (in_array($addExistingRelationship, ['ayah_kandung', 'ibu_kandung'])) {
                    $duplicateRelation = StudentParent::where('siswa_id', $siswa->id)
                        ->where('relationship', $addExistingRelationship)
                        ->exists();

                    if ($duplicateRelation) {
                        $relationLabel = $addExistingRelationship === 'ayah_kandung' ? 'Ayah Kandung' : 'Ibu Kandung';

                        return back()->with('error', "Siswa sudah memiliki {$relationLabel}! Satu siswa hanya boleh memiliki 1 Ayah Kandung dan 1 Ibu Kandung.");
                    }
                }

                StudentParent::create([
                    'siswa_id' => $siswa->id,
                    'parent_id' => $request->add_existing_parent_id,
                    'relationship' => $addExistingRelationship,
                    'is_primary' => false,
                    'is_financial_responsible' => true,
                    'can_access_academic' => true,
                ]);
            }
        }

        // Handle adding new parent
        if ($request->add_parent_option === 'new' && $request->add_new_parent_name) {
            // Determine the actual relationship value
            $addNewRelationship = $request->add_new_relationship;
            if ($addNewRelationship === 'lainnya' && ! empty($request->add_new_relationship_lainnya)) {
                $addNewRelationship = $request->add_new_relationship_lainnya;
            }

            // Check for duplicate ayah_kandung or ibu_kandung
            if (in_array($addNewRelationship, ['ayah_kandung', 'ibu_kandung'])) {
                $duplicateRelation = StudentParent::where('siswa_id', $siswa->id)
                    ->where('relationship', $addNewRelationship)
                    ->exists();

                if ($duplicateRelation) {
                    $relationLabel = $addNewRelationship === 'ayah_kandung' ? 'Ayah Kandung' : 'Ibu Kandung';

                    return back()->with('error', "Siswa sudah memiliki {$relationLabel}! Satu siswa hanya boleh memiliki 1 Ayah Kandung dan 1 Ibu Kandung.");
                }
            }

            $parentUser = User::create([
                'name' => $request->add_new_parent_name,
                'email' => $request->add_new_parent_email,
                'username' => $request->add_new_parent_username,
                'password' => Hash::make($request->add_new_parent_password),
                'phone' => $request->add_new_parent_phone,
                'cabang_id' => $siswa->cabang_id,
                'role' => 'orang_tua',
                'is_active' => true,
            ]);

            StudentParent::create([
                'siswa_id' => $siswa->id,
                'parent_id' => $parentUser->id,
                'relationship' => $addNewRelationship,
                'is_primary' => false,
                'is_financial_responsible' => true,
                'can_access_academic' => true,
            ]);
        }

        return redirect_to_previous('admin.users.siswa')->with('success', 'Siswa berhasil diupdate!');
    }

    public function showSiswa(int $id)
    {
        $siswa = Siswa::termasukNonaktif()->with(['user', 'kelas', 'cabang', 'studentParents.parent'])->where('id', $id)->first();
        if (! $siswa) {
            $siswa = Siswa::termasukNonaktif()->with(['user', 'kelas', 'cabang', 'studentParents.parent'])->where('user_id', $id)->first();
        }

        if (! $siswa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        return view('admin.users.siswa-show', compact('siswa'));
    }

    public function deleteSiswa(Request $request, int $id)
    {
        $siswa = Siswa::termasukNonaktif()->findOrFail($id);

        $blockers = $this->siswaBlockers($siswa->id);

        // TAHAP 1 - siswa masih punya jejak akademik/keuangan: JANGAN langsung hapus.
        // Tampilkan peringatan berisi rincian data yang akan ikut musnah, lalu tawarkan
        // konfirmasi kedua. Tanpa flag konfirmasi_permanen, penghapusan tidak terjadi.
        if (! empty($blockers) && ! $request->boolean('konfirmasi_permanen')) {
            return redirect_to_previous('admin.users.siswa')
                ->with('error',
                    'Siswa ini tidak dapat langsung dihapus karena masih memiliki data terkait ('.implode(', ', $blockers).'). '
                    .'Menghapusnya akan menghilangkan RIWAYAT AKADEMIK & KEUANGAN secara permanen. '
                    .'Untuk menjaga data, NONAKTIFKAN akun / ubah status siswa (mis. Lulus atau Keluar), jangan dihapus.')
                ->with('hapus_siswa_konfirmasi', [
                    'id' => $siswa->id,
                    'nama' => $siswa->nama_lengkap,
                    'nis' => $siswa->nis,
                    'blockers' => $blockers,
                ]);
        }

        // TAHAP 2 - admin sudah menegaskan lewat konfirmasi kedua (atau memang tidak
        // ada data terkait sama sekali): hapus permanen sampai bersih.
        $nama = $siswa->nama_lengkap;
        $this->hapusSiswaPermanen($siswa);

        return redirect_to_previous('admin.users.siswa')->with('success',
            "Siswa \"{$nama}\" beserta SELURUH data terkaitnya telah dihapus permanen.");
    }

    /**
     * Hapus siswa sampai benar-benar bersih - tidak menyisakan record sampah
     * yang bisa jadi bug di menu manapun.
     *
     * Sebagian besar tabel anak sudah ON DELETE CASCADE ke siswa (nilai, presensi,
     * tagihan->pembayaran, rapor->rapor_nilai/rapor_kegiatan_ekstra, ujian_siswa->
     * jawaban_siswa/ujian_pengawasan_logs/ujian_siswa_soal_statuses, tugas_siswa,
     * student_parents, status_naik_kelas_siswa, izin_naik_kelas_khusus,
     * pengajuan_rapor_ketua, request_download_rapor). Yang TIDAK ikut otomatis dan
     * harus dibersihkan manual:
     *   - jadwal_pelajaran.siswa_ids : kolom JSON tanpa foreign key, jadi ID siswa
     *     yang sudah dihapus akan tertinggal di situ (sampah).
     *   - sessions.user_id : juga tanpa foreign key, sesi login milik user yang sudah
     *     dihapus akan menggantung (sampah + sesi yatim yang tidak seharusnya ada).
     *   - baris users milik siswa : menghapus user justru meng-cascade siswa, tapi
     *     urutannya dibalik supaya guard/relasi lain aman.
     */
    private function hapusSiswaPermanen(Siswa $siswa): void
    {
        $siswaId = $siswa->id;
        $user = $siswa->user;

        \Illuminate\Support\Facades\DB::transaction(function () use ($siswa, $siswaId, $user) {
            // Bersihkan ID siswa dari kolom JSON jadwal_pelajaran.siswa_ids (tanpa FK).
            \App\Models\JadwalPelajaran::whereNotNull('siswa_ids')
                ->get(['id', 'siswa_ids'])
                ->each(function ($jadwal) use ($siswaId) {
                    $ids = $jadwal->siswa_ids;
                    if (! is_array($ids) || ! in_array($siswaId, $ids)) {
                        return;
                    }
                    $jadwal->siswa_ids = array_values(array_filter(
                        $ids,
                        fn ($v) => (int) $v !== (int) $siswaId
                    ));
                    $jadwal->save();
                });

            // Hapus siswa lebih dulu (memicu cascade seluruh tabel anak), lalu akunnya.
            $siswa->delete();

            if ($user) {
                $this->bersihkanSesiUser($user->id);
                $user->delete();
            }

            $this->bersihkanNotifikasiYatim();
        });
    }

    /**
     * Buang notifikasi yang menunjuk record yang sudah tidak ada.
     *
     * Notifikasi MILIK user yang dihapus memang ikut cascade lewat
     * notifications.user_id. Yang tidak ikut: notifikasi ke user LAIN yang
     * membicarakan record milik user tadi. Contoh nyata yang ditemukan saat
     * menghapus akun siswa uji - tiga baris "testuser2 membayar Rp 100.000 untuk
     * <siswa>" nangkring di kotak Admin & Bendahara dengan data = {"siswa_id": 233},
     * menunjuk siswa yang sudah lenyap.
     *
     * Menghapus siswa juga meng-cascade ujian_siswa/tugas_siswa/presensi miliknya,
     * jadi notifikasi yang membawa ID tabel-tabel itu ikut jadi yatim. Karena itu
     * pembersihan dibuat generik (menyapu semua kunci referensi yang dipakai di
     * notifications.data) dan dipanggil di SEMUA jalur hapus akun - siswa, tenaga
     * pendidik, maupun wali siswa - bukan cuma siswa.
     */
    private function bersihkanNotifikasiYatim(): void
    {
        // kunci di notifications.data => tabel yang seharusnya memuat ID tersebut
        $referensi = [
            'siswa_id' => 'siswa',
            'ujian_siswa_id' => 'ujian_siswa',
            'tugas_siswa_id' => 'tugas_siswa',
            'presensi_id' => 'presensi',
            'catatan_id' => 'catatan',
            'catatan_monitoring_id' => 'catatan_monitoring',
            'materi_id' => 'materi',
            'tugas_id' => 'tugas',
            'ujian_id' => 'ujian',
            'forum_id' => 'forum_diskusi',
            'kelas_id' => 'kelas',
            'mapel_id' => 'mata_pelajaran',
            'ticket_id' => 'recovery_tickets',
            'wali_kelas_assignment_id' => 'wali_kelas_assignments',
        ];

        foreach ($referensi as $kunci => $tabel) {
            \Illuminate\Support\Facades\DB::table('notifications')
                ->whereNotNull('data->'.$kunci)
                ->whereNotExists(function ($q) use ($tabel, $kunci) {
                    $q->select(\Illuminate\Support\Facades\DB::raw(1))
                        ->from($tabel)
                        ->whereColumn(
                            $tabel.'.id',
                            \Illuminate\Support\Facades\DB::raw(
                                'CAST(JSON_UNQUOTE(JSON_EXTRACT(notifications.data, \'$."'.$kunci.'"\')) AS UNSIGNED)'
                            )
                        );
                })
                ->delete();
        }
    }

    /**
     * Hapus baris sessions milik user. Tabel sessions TIDAK punya foreign key ke
     * users, jadi tanpa ini sesi login milik akun yang sudah dihapus akan
     * menggantung selamanya - sampah, sekaligus sesi yatim yang tidak semestinya
     * masih ada. Dipakai di semua jalur penghapusan akun (siswa, tenaga pendidik,
     * wali siswa).
     */
    private function bersihkanSesiUser(int|array $userIds): void
    {
        \Illuminate\Support\Facades\DB::table('sessions')
            ->whereIn('user_id', (array) $userIds)
            ->delete();
    }

    // --- WALI SISWA ---

    public function orangTua(Request $request)
    {
        $query = User::where('role', 'orang_tua')
            ->with(['studentParents.siswa.kelas', 'studentParents.siswa.cabang']);

        // Handle search parameter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Handle filters
        if ($request->has('jenjang') && $request->jenjang != '') {
            $query->whereHas('studentParents.siswa.kelas', function ($q) use ($request) {
                $q->where('jenjang', $request->jenjang);
            });
        }

        if ($request->has('cabang_id') && $request->cabang_id != '') {
            $query->whereHas('studentParents.siswa', function ($q) use ($request) {
                $q->where('cabang_id', $request->cabang_id);
            });
        }

        if ($request->has('status') && $request->status != '') {
            $isActive = $request->status === 'aktif';
            $query->where('is_active', $isActive);
        }

        $orangTua = $query->orderBy('name')->paginate(15);
        $cabangList = Cabang::where('is_active', true)->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        return view('admin.users.wali-siswa', compact('orangTua', 'cabangList', 'jenjangs'));
    }

    public function printOrangTua(Request $request)
    {
        $query = User::where('role', 'orang_tua')
            ->with(['studentParents.siswa.kelas', 'studentParents.siswa.cabang']);

        // Handle search parameter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Handle filters
        if ($request->has('jenjang') && $request->jenjang != '') {
            $query->whereHas('studentParents.siswa.kelas', function ($q) use ($request) {
                $q->where('jenjang', $request->jenjang);
            });
        }

        if ($request->has('cabang_id') && $request->cabang_id != '') {
            $query->whereHas('studentParents.siswa', function ($q) use ($request) {
                $q->where('cabang_id', $request->cabang_id);
            });
        }

        if ($request->has('status') && $request->status != '') {
            $isActive = $request->status === 'aktif';
            $query->where('is_active', $isActive);
        }

        $orangTua = $query->orderBy('name')->get();

        // Prepare filter info
        $filterInfo = [];
        if ($request->jenjang) {
            $filterInfo[] = 'Jenjang Anak: '.$request->jenjang;
        }
        if ($request->cabang_id) {
            $cabang = Cabang::find($request->cabang_id);
            if ($cabang) {
                $filterInfo[] = 'Cabang: '.$cabang->nama_cabang;
            }
        }
        if ($request->status) {
            $filterInfo[] = 'Status: '.ucfirst($request->status);
        }

        return view('admin.users.print.wali-siswa', compact('orangTua', 'filterInfo'));
    }

    public function createOrangTua()
    {
        // Get all active siswa for optional linking
        $siswaList = Siswa::with(['kelas', 'cabang', 'user'])
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            })
            ->get()
            ->sortBy(function ($siswa) {
                return $siswa->user->name ?? $siswa->nama_lengkap;
            });

        // Get unique cabang list from siswa
        $cabangList = \App\Models\Cabang::orderBy('nama_cabang')->get();

        // Get unique kelas list from siswa
        $kelasList = \App\Models\Kelas::orderBy('jenjang')->orderBy('nama_kelas')->get();

        // Jenjang list
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        return view('admin.users.wali-siswa-create', compact('siswaList', 'cabangList', 'kelasList', 'jenjangs'));
    }

    public function storeOrangTua(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'personal_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
            'hubungan_keluarga' => 'required_with:siswa_ids|nullable|string|max:50',
            'hubungan_keluarga_lainnya' => 'required_if:hubungan_keluarga,lainnya|nullable|string|max:100',
        ], $this->userValidationMessages(), $this->userValidationAttributes());

        // Create parent user account
        $orangTua = User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'personal_email' => $validated['personal_email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'cabang_id' => 1,
            'role' => 'orang_tua',
            'is_active' => true,
        ]);

        // Link to students if selected
        if (! empty($validated['siswa_ids']) && ! empty($validated['hubungan_keluarga'])) {
            // Determine the actual relationship value
            $relationship = $validated['hubungan_keluarga'];
            if ($relationship === 'lainnya' && ! empty($validated['hubungan_keluarga_lainnya'])) {
                $relationship = $validated['hubungan_keluarga_lainnya'];
            }

            foreach ($validated['siswa_ids'] as $siswaId) {
                StudentParent::create([
                    'siswa_id' => $siswaId,
                    'parent_id' => $orangTua->id,
                    'relationship' => $relationship,
                    'is_primary' => false,
                    'is_financial_responsible' => true,
                    'can_access_academic' => true,
                ]);
            }
        }

        return redirect_to_previous('admin.users.wali-siswa')
            ->with('success', 'Akun wali siswa berhasil dibuat!');
    }

    public function toggleOrangTuaStatus(int $id)
    {
        $user = User::where('role', 'orang_tua')->findOrFail($id);
        $user->is_active = ! $user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect_to_previous('admin.users.wali-siswa')->with('success', "Akun wali siswa berhasil {$status}!");
    }

    public function showOrangTua(int $id)
    {
        $orangTua = User::where('role', 'orang_tua')
            ->with(['studentParents.siswa.kelas.cabang', 'studentParents.siswa.cabang'])
            ->findOrFail($id);

        return view('admin.users.wali-siswa-show', compact('orangTua'));
    }

    public function editOrangTua(int $id)
    {
        $orangTua = User::where('role', 'orang_tua')
            ->with(['studentParents.siswa.kelas'])
            ->findOrFail($id);

        return view('admin.users.wali-siswa-edit', compact('orangTua'));
    }

    public function updateOrangTua(Request $request, int $id)
    {
        $orangTua = User::where('role', 'orang_tua')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($orangTua->id),
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($orangTua->id),
            ],
            'personal_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'is_active' => 'required|boolean',
            'relationships' => 'nullable|array',
            'relationships.*' => 'nullable|string|max:100',
            'relationships_lainnya' => 'nullable|array',
            'relationships_lainnya.*' => 'nullable|string|max:100',
        ], $this->userValidationMessages(), $this->userValidationAttributes());

        // Update user data
        $orangTua->name = $validated['name'];
        $orangTua->username = $validated['username'];
        $orangTua->email = $validated['email'];
        $orangTua->personal_email = $validated['personal_email'] ?? null;
        $orangTua->phone = $validated['phone'];
        $orangTua->is_active = $validated['is_active'];

        // Update password only if provided
        if ($request->filled('password')) {
            $orangTua->password = Hash::make($validated['password']);
        }

        $orangTua->save();

        // Update relationships if provided
        if ($request->has('relationships')) {
            foreach ($request->relationships as $studentParentId => $relationship) {
                if (! empty($relationship)) {
                    // Determine the actual relationship value
                    $actualRelationship = $relationship;
                    if ($relationship === 'lainnya' && isset($request->relationships_lainnya[$studentParentId])) {
                        $actualRelationship = $request->relationships_lainnya[$studentParentId];
                    }

                    StudentParent::where('id', $studentParentId)
                        ->where('parent_id', $orangTua->id)
                        ->update(['relationship' => $actualRelationship]);
                }
            }
        }

        return redirect_to_previous('admin.users.wali-siswa')
            ->with('success', 'Data wali siswa berhasil diperbarui!');
    }

    public function deleteOrangTua(int $id)
    {
        $user = User::where('role', 'orang_tua')->findOrFail($id);
        $this->bersihkanSesiUser($user->id);
        $user->delete();
        $this->bersihkanNotifikasiYatim();

        return redirect_to_previous('admin.users.wali-siswa')->with('success', 'Akun wali siswa berhasil dihapus!');
    }

    // --- IMPORT SISWA ---

    public function importSiswaForm()
    {
        return view('admin.users.siswa-import');
    }

    public function importSiswa(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $import = new \App\Imports\SiswaImport;
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();

            $message = "Berhasil mengimport {$imported} siswa.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati.";
            }

            // Collect warnings
            $warnings = $import->getWarnings();

            return redirect()->route('admin.users.siswa')->with('success', $message)->with('import_warnings', $warnings);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport: '.$e->getMessage());
        }
    }

    public function downloadSiswaTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Templates\SiswaTemplate, 'template_siswa.xlsx');
    }

    // --- IMPORT TENAGA PENDIDIK ---

    public function importTenagaPendidikForm()
    {
        return view('admin.users.tenaga-pendidik-import');
    }

    public function importTenagaPendidik(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $import = new \App\Imports\TenagaPendidikImport;
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $warnings = $import->getWarnings();

            $message = "Berhasil mengimport {$imported} tenaga pendidik.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati.";
            }

            return redirect()->route('admin.users.tenaga-pendidik')->with('success', $message)->with('import_warnings', $warnings);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport: '.$e->getMessage());
        }
    }

    public function downloadTenagaPendidikTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Templates\TenagaPendidikTemplate, 'template_tenaga_pendidik.xlsx');
    }

    // --- IMPORT WALI SISWA ---

    public function importOrangTuaForm()
    {
        return view('admin.users.wali-siswa-import');
    }

    public function importOrangTua(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $import = new \App\Imports\OrangTuaImport;
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $warnings = $import->getWarnings();

            $message = "Berhasil mengimport {$imported} wali siswa.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati.";
            }

            return redirect()->route('admin.users.wali-siswa')->with('success', $message)->with('import_warnings', $warnings);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport: '.$e->getMessage());
        }
    }

    public function downloadOrangTuaTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Templates\OrangTuaTemplate, 'template_orang_tua.xlsx');
    }

    public function bulkDeleteTenagaPendidik(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih');
        }

        // IDs received are User IDs (from blade checkboxes).
        // GUARD INTEGRITAS: lewati guru yang masih punya jejak akademik (nilai/rapor cascade).
        $tenagaByUser = TenagaPendidik::whereIn('user_id', $ids)->get()->keyBy('user_id');
        $safeUserIds = [];
        $skipped = 0;
        foreach ($ids as $uid) {
            $tp = $tenagaByUser->get($uid);
            if ($tp && ! empty($this->tenagaPendidikBlockers($tp->id))) {
                $skipped++;

                continue;
            }
            $safeUserIds[] = $uid;
        }

        if (! empty($safeUserIds)) {
            TenagaPendidik::whereIn('user_id', $safeUserIds)->delete();
            $this->bersihkanSesiUser($safeUserIds);
            User::whereIn('id', $safeUserIds)->delete();
            $this->bersihkanNotifikasiYatim();
        }

        if ($skipped > 0) {
            return redirect()->back()->with('warning',
                count($safeUserIds).' data tenaga pendidik dihapus. '.$skipped.' dilewati karena masih memiliki '
                .'nilai/materi/tugas/ujian/penugasan — NONAKTIFKAN akunnya, jangan dihapus (mencegah nilai & rapor siswa hilang).');
        }

        return redirect()->back()->with('success', count($safeUserIds).' data tenaga pendidik berhasil dihapus');
    }

    public function bulkDeleteSiswa(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih');
        }

        // GUARD INTEGRITAS: lewati siswa yang masih punya jejak akademik/keuangan
        // (nilai/presensi/ujian/rapor/tagihan/pembayaran ikut cascade bila dihapus).
        // Hapus paksa 2 langkah sengaja TIDAK disediakan di aksi massal - terlalu
        // berisiko; untuk itu pakai tombol hapus per siswa yang ada konfirmasi kedua.
        // termasukNonaktif() wajib: siswa berakun nonaktif tetap tampil di daftar
        // (dan bisa dicentang), jadi harus bisa ditemukan di sini juga.
        $siswas = Siswa::termasukNonaktif()->whereIn('id', $ids)->get();
        $safeSiswa = [];
        $skipped = 0;
        foreach ($siswas as $siswa) {
            if (! empty($this->siswaBlockers($siswa->id))) {
                $skipped++;

                continue;
            }
            $safeSiswa[] = $siswa;
        }

        $safeSiswaIds = array_map(fn ($s) => $s->id, $safeSiswa);

        foreach ($safeSiswa as $siswa) {
            // Lewat helper yang sama dgn hapus tunggal supaya pembersihan
            // jadwal_pelajaran.siswa_ids (tanpa FK) ikut jalan di jalur massal.
            $this->hapusSiswaPermanen($siswa);
        }

        if ($skipped > 0) {
            return redirect()->back()->with('warning',
                count($safeSiswaIds).' data siswa dihapus. '.$skipped.' dilewati karena masih memiliki '
                .'riwayat akademik/keuangan — NONAKTIFKAN akun / ubah status (Lulus/Keluar), jangan dihapus.');
        }

        return redirect()->back()->with('success', count($safeSiswaIds).' data siswa berhasil dihapus');
    }

    public function bulkDeleteOrangTua(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih');
        }

        $this->bersihkanSesiUser(
            User::whereIn('id', $ids)->where('role', 'orang_tua')->pluck('id')->all()
        );
        User::whereIn('id', $ids)->where('role', 'orang_tua')->delete();
        $this->bersihkanNotifikasiYatim();

        return redirect()->back()->with('success', count($ids).' Data wali siswa berhasil dihapus');
    }
}
