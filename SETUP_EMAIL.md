# Setup Email — Buku Induk Siswa

Panduan lengkap setup fitur email di project ini.

---

## Apa Saja Fitur Email yang Ada?

| # | Fitur | Trigger | File |
|---|-------|---------|------|
| 1 | **Email Akun Baru** | Admin buat akun user baru | `AkunBaruMail` |
| 2 | **Lupa Password** | User klik "Lupa Password?" di halaman login | `ResetPasswordMail` |
| 3 | **Reset Password (Logged in)** | User logged in klik "Kirim Password Baru ke Email" di profil | `ResetPasswordMail` |

---

## Langkah 1 — Pilih Mail Driver

Buka file `.env`, cari section `MAIL_*` dan pilih salah satu setup:

### Opsi A: Development (driver `log`)

Email **tidak terkirim asli**, hanya dicatat di log file. Cocok untuk testing.

```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@bukuinduk.test"
MAIL_FROM_NAME="${APP_NAME}"
```

Cek email di: `storage/logs/laravel.log`

---

### Opsi B: Mailtrap.io (testing dengan UI)

Email tertangkap di Mailtrap inbox, **tidak terkirim ke email asli**. Bisa preview HTML email.

**Cara setup:**
1. Daftar gratis di [mailtrap.io](https://mailtrap.io)
2. Buat inbox baru
3. Pilih tab **Sandbox** → **Integrations** → pilih **Laravel 9+**
4. Copy kredensial ke `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@bukuinduk.sch.id"
MAIL_FROM_NAME="Buku Induk SMA Negeri 3 Cilacap"
```

---

### Opsi C: Gmail SMTP (Production)

Email **terkirim asli** ke email user. Cocok untuk production sekolah.

**Cara setup:**
1. Login ke akun Gmail sekolah (misal: `sma3cilacap@gmail.com`)
2. Aktifkan **2-Step Verification** di [myaccount.google.com/security](https://myaccount.google.com/security)
3. Buat **App Password**:
   - Buka [myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
   - App: pilih "Mail"
   - Device: pilih "Other (Custom name)" → ketik "Buku Induk"
   - Copy 16 karakter password yang muncul
4. Setup `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=sma3cilacap@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=sma3cilacap@gmail.com
MAIL_FROM_NAME="Buku Induk SMA Negeri 3 Cilacap"
```

> ⚠️ Gmail punya limit **500 email/hari**. Untuk volume lebih besar, pakai SES/Mailgun/Resend.

---

### Opsi D: Mailgun (Production, scale)

5.000 email/bulan gratis, cocok untuk volume sedang.

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.bukuinduk.sch.id
MAILGUN_SECRET=key-xxxxxxxxxxxxxxx
MAILGUN_ENDPOINT=api.mailgun.net
MAIL_FROM_ADDRESS=noreply@bukuinduk.sch.id
MAIL_FROM_NAME="Buku Induk SMA Negeri 3 Cilacap"
```

Install package:
```bash
composer require symfony/mailgun-mailer symfony/http-client
```

---

## Langkah 2 — Clear Config Cache

Setelah ubah `.env`, jalankan:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## Langkah 3 — Test Pengiriman Email

### Test via Tinker

```bash
php artisan tinker
```

Lalu:

```php
Mail::raw('Test email dari Buku Induk', function ($msg) {
    $msg->to('email_kamu@gmail.com')->subject('Test');
});
```

Kalau driver `log` → cek `storage/logs/laravel.log`
Kalau driver `smtp` → cek inbox kamu

---

### Test via Aplikasi

#### Test "Email Akun Baru":
1. Login sebagai `super_admin`
2. Masuk ke menu **Kelola Akun** → **Tambah**
3. Isi data baru, lalu Simpan
4. Cek email user baru tersebut (atau log)

#### Test "Lupa Password":
1. Logout dari aplikasi
2. Di halaman login, klik **"Lupa Password?"**
3. Input email user yang terdaftar
4. Cek inbox email tersebut

#### Test "Reset Password via Email" (Logged in):
1. Login sebagai user (misal: `ajeng.alisa`)
2. Buka **Profil → Ganti Password**
3. Klik button **"📧 Kirim Password Baru ke Email"**
4. Cek inbox

---

## Langkah 4 — Pastikan User Punya Email

User di database wajib punya email yang valid. Cek dengan:

```bash
php artisan tinker
```

```php
DB::table('users')->select('username', 'email')->get();
```

Kalau ada user yang `email`-nya null/kosong, fitur reset password gak akan jalan untuk user tersebut.

### Quick Fix: Isi Email Dummy untuk Testing

Kalau user belum punya email (data lama), pakai command ini:

```bash
php artisan users:seed-email
```

Format email: `{username}@bukuinduk.test`

Atau dengan domain custom:
```bash
php artisan users:seed-email --domain=sman3cilacap.sch.id
```

> ⚠️ Untuk production, isi email asli user satu per satu via menu **Kelola Akun** di admin.

---

## Troubleshooting

### Error: "Connection could not be established with host smtp.gmail.com"

- Cek firewall/antivirus tidak block port 587
- Pastikan `MAIL_PASSWORD` pakai App Password (bukan password Gmail biasa)
- Pastikan 2FA aktif di akun Gmail

### Error: "Failed to authenticate"

- Username/password salah
- Untuk Gmail, pastikan pakai **App Password** bukan password biasa
- Cek `.env` tidak ada whitespace di awal/akhir nilai

### Email tidak masuk inbox (Gmail)

- Cek folder **Spam**
- Pastikan `MAIL_FROM_ADDRESS` sama dengan email yang login SMTP
- Untuk production, setup **SPF/DKIM** record di DNS domain

### Driver `log` tapi tidak ada di log file

- Cek `storage/logs/laravel.log` permission writeable
- Cek `LOG_LEVEL=debug` di `.env`
- Restart server: `php artisan serve --port=8001`

---

## Production Checklist

Sebelum deploy ke production:

- [ ] Ganti `MAIL_MAILER=log` → `smtp`/`mailgun`/`ses`
- [ ] Setup `MAIL_FROM_ADDRESS` dengan domain sekolah
- [ ] Setup `MAIL_FROM_NAME` dengan nama lengkap aplikasi
- [ ] Setup SPF & DKIM record di DNS untuk anti-spam
- [ ] Test semua 3 fitur email (akun baru, lupa password, reset password)
- [ ] Pastikan user yang ada di DB sudah punya email valid
- [ ] Setup queue (`QUEUE_CONNECTION=database`) agar email tidak block request
- [ ] Run worker: `php artisan queue:work`

---

## Konfigurasi Mailable yang Sudah Ada

### `App\Mail\AkunBaruMail`
- **Subject:** "Akun Buku Induk Anda Telah Dibuat"
- **View:** `resources/views/emails/akun_baru.blade.php`
- **Trigger:** Saat admin membuat akun baru
- **Parameter:** nama, username, password, role

### `App\Mail\ResetPasswordMail`
- **Subject:** "Reset Password Akun Buku Induk"
- **View:** `resources/views/emails/reset_password.blade.php`
- **Trigger:** Lupa password / reset via email
- **Parameter:** nama, username, password baru

---

## Routes yang Terkait

| Method | URL | Controller | Fungsi |
|--------|-----|------------|--------|
| GET | `/forgot-password` | `ForgotPasswordController@showForm` | Form lupa password |
| POST | `/forgot-password` | `ForgotPasswordController@send` | Kirim password baru via email |
| POST | `/admin/profil/reset-email` | `ResetPasswordEmailController@reset` | Admin reset via email |
| POST | `/guru/profil/reset-email` | `ResetPasswordEmailController@reset` | Guru reset via email |
| POST | `/wali/profil/reset-email` | `ResetPasswordEmailController@reset` | Wali kelas reset via email |
| POST | `/orangtua/profil/reset-email` | `ResetPasswordEmailController@reset` | Ortu reset via email |
| POST | `/admin/kelola-akun` | `KelolaAkunController@store` | Buat akun + kirim email kredensial |

---

## Cara Customize Template Email

Edit file di:
- `resources/views/emails/akun_baru.blade.php` — template email akun baru
- `resources/views/emails/reset_password.blade.php` — template email reset password

Variable yang available di view:
- `{{ $nama }}` — nama user
- `{{ $username }}` — username login
- `{{ $password }}` atau `{{ $passwordBaru }}` — password
- `{{ $role }}` — role user (hanya di akun baru)
- `{{ $loginUrl }}` — URL login

Kamu bisa tambah logo sekolah, ganti warna, atau tambahkan info kontak admin.

---

## Setup Queue (Opsional, Recommended untuk Production)

Tanpa queue, request akan **menunggu** sampai email selesai dikirim (lambat). Dengan queue, email dikirim di background.

### 1. Setup queue driver di `.env`:
```env
QUEUE_CONNECTION=database
```

### 2. Migrate table jobs:
```bash
php artisan queue:table
php artisan migrate
```

### 3. Update Mailable untuk pakai queue:

Di `app/Mail/AkunBaruMail.php` dan `ResetPasswordMail.php`, sudah ada `use Queueable;` dan `use ShouldQueue` (tinggal aktifkan).

```php
class AkunBaruMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    // ...
}
```

### 4. Jalankan worker:
```bash
php artisan queue:work
```

Atau pakai supervisor untuk auto-restart di production.

---

## Tips Keamanan

1. **Jangan commit `.env`** ke Git — sudah di `.gitignore`
2. **Gunakan App Password** untuk Gmail, bukan password biasa
3. **Rotasi password SMTP** secara berkala
4. **Rate limiting** untuk endpoint `/forgot-password` agar tidak di-spam
5. **Validasi email** dengan `email|exists:users,email` (sudah diterapkan)
6. Untuk production, **enable HTTPS** agar password tidak ter-expose di network
