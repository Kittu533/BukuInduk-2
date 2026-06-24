# Ringkasan Perubahan Fitur Akademik

## Tujuan
Dokumen ini merangkum perubahan fitur yang sudah diterapkan pada sistem Buku Induk Siswa, terutama untuk alur semester, kenaikan kelas, kelulusan, alumni, dan pengelolaan akun.

## Perubahan yang Sudah Diterapkan

### 1. Proses Semester dan Tahun Ajaran Dipisah
- Semester `Ganjil -> Genap` diproses dari menu **Semester**.
- Perpindahan `Genap -> Tahun Ajaran Baru` diproses dari menu **Kenaikan/Kelulusan**.
- Dengan pemisahan ini, sistem tidak lagi mencampur kenaikan semester dengan kenaikan kelas.

### 2. Kenaikan Kelas dan Kelulusan Sudah Memindahkan Data Nyata
- Siswa kelas `X` dipindahkan ke kelas `XI` pada tahun ajaran baru.
- Siswa kelas `XI` dipindahkan ke kelas `XII` pada tahun ajaran baru.
- Siswa kelas `XII` diubah menjadi `lulus` dan dimasukkan ke data **Alumni**.
- Data tidak hanya berubah status, tetapi juga berpindah ke `kelas_aktif` yang sesuai.

### 3. Tahun Ajaran Lama Menjadi Arsip
- Tahun ajaran lama tetap bisa dilihat dan difilter.
- Tahun ajaran arsip tidak bisa diaktifkan lagi.
- Riwayat data 3 tahun sebelumnya tetap bisa dibuka sebagai referensi.

### 4. Alumni Menyimpan Riwayat Akademik
- Halaman alumni sekarang tidak hanya menampilkan biodata.
- Alumni dapat dilihat lengkap beserta:
  - biodata,
  - riwayat kelas,
  - riwayat nilai akademik,
  - riwayat nilai ekstrakurikuler.
- Detail alumni dapat memilih riwayat kelas/semester lama untuk melihat nilai historis.

### 5. Akses Login Siswa yang Sudah Lulus Ditolak
- Akun `orangtua` yang tertaut ke siswa dengan status `lulus` atau `do` tidak bisa login lagi.
- Ini mencegah siswa yang sudah tidak aktif tetap mengakses sistem.

### 6. Kelola Akun Admin Sudah Bisa Ganti Password
- Admin dapat mengubah password akun sendiri dari menu **Kelola Akun**.
- Password baru akan disimpan dalam bentuk hash.
- Jika field password dikosongkan saat edit akun, password lama tidak berubah.

### 7. Validasi Flow Akademik Sudah Ditambahkan
- Jika semester aktif masih `Ganjil`, admin diarahkan memproses semester dulu.
- Setelah semester `Genap` selesai, baru boleh memproses tahun ajaran baru.
- Alert informasi flow sudah ditampilkan pada halaman Semester dan Kenaikan/Kelulusan.

### 8. Guard Kelas X Kosong Sudah Ditambahkan
- Sistem menandai rombel kelas X yang masih kosong sebagai **Prioritas intake baru**.
- Proses rollover berikutnya tidak boleh dilanjutkan jika kelas X belum diisi.
- Tujuannya agar setelah kelas XII lulus, kelas X benar-benar diisi siswa baru/pindahan sebelum siklus berikutnya.

### 9. Filter Alumni Sudah Ditambahkan
- Halaman alumni sudah memiliki filter berdasarkan:
  - angkatan,
  - tahun ajaran kelulusan,
  - pencarian nama / ID siswa / NIS / NISN.

### 10. Data Siswa Kelas Kosong Sudah Dilengkapi
- Untuk melengkapi data kelas aktif yang kosong, sudah dibuat backfill data siswa:
  - cohort `S26` untuk kelas `XI`,
  - cohort `S27` untuk kelas `X`.
- Setelah backfill, distribusi kelas aktif pada `2027/2028 Ganjil` sudah penuh:
  - `X E01 - X E11` terisi,
  - `XI F01 - XI F11` terisi,
  - `XII F01 - XII F11` terisi.

### 11. Jadwal Mengajar Sudah Diperbaiki
- Error pada halaman `/admin/jadwal-mengajar/create` karena method `create()` hilang sudah diperbaiki.
- Query jadwal mengajar juga dirapikan agar mengikuti konteks `tahun ajaran aktif` dan `semester aktif`.

## Flow Penggunaan Sistem

### A. Awal Tahun Ajaran
1. Tambahkan tahun ajaran baru jika belum ada.
2. Aktifkan tahun ajaran yang sedang berjalan.
3. Pastikan semester aktif adalah `Ganjil`.

### B. Saat Semester Ganjil Selesai
1. Buka menu **Semester**.
2. Klik **Proses Semester Baru**.
3. Sistem akan memindahkan data dari semester `Ganjil` ke `Genap` pada tahun ajaran yang sama.

### C. Saat Semester Genap / Akhir Tahun Selesai
1. Pastikan tahun ajaran berikutnya sudah tersedia.
2. Buka menu **Kenaikan/Kelulusan**.
3. Klik **Proses Tahun Ajaran Baru**.
4. Sistem akan menjalankan:
   - kelas X -> XI,
   - kelas XI -> XII,
   - kelas XII -> Alumni,
   - tahun ajaran lama -> Arsip,
   - tahun ajaran baru -> Aktif.

### D. Setelah Proses Tahun Ajaran Baru
1. Isi siswa baru atau pindahan ke kelas X.
2. Prioritaskan rombel yang ditandai **Prioritas intake baru**.
3. Cek halaman **Data Siswa**, **Kelas Aktif**, dan **Alumni** untuk memastikan hasil proses sudah sesuai.

### E. Akses Alumni
1. Buka menu **Data Alumni**.
2. Gunakan filter angkatan atau tahun ajaran kelulusan.
3. Masuk ke detail alumni untuk melihat riwayat nilai dan riwayat kelas.

### F. Reset Password Admin
1. Buka menu **Kelola Akun**.
2. Pilih akun admin yang ingin diubah.
3. Isi password baru.
4. Simpan perubahan.

## Catatan
- Data `S26` dan `S27` yang ditambahkan adalah data backfill untuk melengkapi struktur kelas aktif.
- Riwayat nilai lama tetap dipertahankan sebagai referensi.
- Tahun ajaran arsip hanya untuk pembacaan data historis, bukan untuk dipakai kembali sebagai tahun aktif.
