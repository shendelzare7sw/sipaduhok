import urllib.request
import zlib
import base64

plantuml_code = """@startuml
left to right direction
skinparam packageStyle rectangle
skinparam usecase {
  BackgroundColor LightBlue
  BorderColor DarkBlue
  ArrowColor Black
}
skinparam actor {
  BackgroundColor Gold
  BorderColor DarkGoldenRod
}

actor "Admin" as Admin
actor "Ketua PKBM" as Ketua
actor "Waka" as Waka
actor "Bendahara" as Bendahara
actor "Sekretaris" as Sekretaris
actor "Wali Kelas" as Wali
actor "Guru Pengajar" as Guru
actor "Orang Tua" as Ortu
actor "Siswa" as Siswa

package "SIPADUHOK" {
  usecase "Kelola Master Data & Konfigurasi" as UC_Master
  usecase "Monitoring Laporan Global" as UC_Monitoring
  
  usecase "Approval Dispensasi & Validasi Rapor" as UC_Approval
  
  usecase "Kelola Data Akademik & Promosi" as UC_Akademik
  
  usecase "Kelola Keuangan & Tagihan" as UC_Keuangan
  usecase "Validasi Akses Ujian/Rapor" as UC_ValidasiAkses
  
  usecase "Kelola Konten & Pengumuman" as UC_Konten
  
  usecase "Input Presensi & Generate Rapor" as UC_Wali
  usecase "Validasi Izin Presensi" as UC_Izin
  
  usecase "Kelola Pembelajaran LMS (Materi/Tugas)" as UC_LMS
  usecase "Input Nilai Siswa" as UC_Nilai
  
  usecase "Bayar Tagihan & Unduh Rapor" as UC_Ortu1
  usecase "Ajukan Izin Absen" as UC_Ortu2
  
  usecase "Akses LMS & Kerjakan Ujian" as UC_Siswa1
  usecase "Lihat Nilai & Presensi" as UC_Siswa2
}

Admin --> UC_Master
Admin --> UC_Monitoring
Admin --> UC_Akademik
Admin --> UC_Keuangan

Ketua --> UC_Approval
Ketua --> UC_Monitoring

Waka --> UC_Akademik
Waka --> UC_Monitoring

Bendahara --> UC_Keuangan
Bendahara --> UC_ValidasiAkses

Sekretaris --> UC_Konten

Wali --> UC_Wali
Wali --> UC_ValidasiAkses
Wali --> UC_Izin

Guru --> UC_LMS
Guru --> UC_Nilai

Ortu --> UC_Ortu1
Ortu --> UC_Ortu2

Siswa --> UC_Siswa1
Siswa --> UC_Siswa2
@enduml"""

# Encode plantuml string
def encode(text):
    zlibbed_str = zlib.compress(text.encode('utf-8'))
    compressed_string = zlibbed_str[2:-4]
    return base64.b64encode(compressed_string).translate(bytes.maketrans(b'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/', b'0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_')).decode('utf-8')

encoded = encode(plantuml_code)
url = f"http://www.plantuml.com/plantuml/png/{encoded}"

req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
try:
    with urllib.request.urlopen(req) as response:
        with open('c:\\laragon\\www\\sipaduhok\\docs\\flow\\use_case_diagram.png', 'wb') as f:
            f.write(response.read())
    print("Berhasil menggenerate file use_case_diagram.png dari PlantUML Server!")
except Exception as e:
    print(f"Error: {e}")
