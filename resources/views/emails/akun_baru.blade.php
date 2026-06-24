<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Baru Buku Induk</title>
</head>
<body style="margin:0; padding:0; background:#f4f6fb; font-family:'Segoe UI', Arial, sans-serif; color:#333;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb; padding:40px 20px;">
        <tr>
            <td align="center">

                <!-- ============= CARD ============= -->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 16px rgba(0,0,0,0.08);">

                    <!-- ============= HEADER ============= -->
                    <tr>
                        <td style="background:linear-gradient(135deg, #16a34a 0%, #15803d 100%); padding:32px 24px; text-align:center;">
                            <div style="display:inline-block; background:rgba(255,255,255,0.15); padding:14px 18px; border-radius:50%; margin-bottom:12px;">
                                <span style="font-size:32px;">🎓</span>
                            </div>
                            <h1 style="color:#ffffff; font-size:22px; font-weight:600; margin:0; letter-spacing:0.3px;">
                                Selamat Datang di Buku Induk
                            </h1>
                            <p style="color:rgba(255,255,255,0.9); font-size:13px; margin:6px 0 0;">
                                SMA Negeri 3 Cilacap
                            </p>
                        </td>
                    </tr>

                    <!-- ============= CONTENT ============= -->
                    <tr>
                        <td style="padding:32px 32px 8px;">
                            <p style="font-size:15px; margin:0 0 8px;">
                                Halo <strong>{{ $nama }}</strong>,
                            </p>
                            <p style="font-size:14px; line-height:1.6; color:#4b5563; margin:0;">
                                Akun Buku Induk Anda telah berhasil dibuat oleh admin sekolah dengan role
                                <strong style="color:#16a34a; text-transform:capitalize;">{{ $role }}</strong>.
                                Silakan gunakan kredensial berikut untuk login pertama kali:
                            </p>
                        </td>
                    </tr>

                    <!-- ============= CREDENTIALS BOX ============= -->
                    <tr>
                        <td style="padding:16px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:20px;">
                                <tr>
                                    <td style="padding-bottom:12px;">
                                        <div style="font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">
                                            Username
                                        </div>
                                        <div style="font-size:15px; color:#111827; font-weight:600; font-family:'Courier New', monospace;">
                                            {{ $username }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top:1px dashed #d1d5db; padding-top:12px;">
                                        <div style="font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">
                                            Password Sementara
                                        </div>
                                        <div style="font-size:18px; color:#16a34a; font-weight:700; font-family:'Courier New', monospace; letter-spacing:1.5px;">
                                            {{ $password }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ============= WARNING ============= -->
                    <tr>
                        <td style="padding:8px 32px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fef3c7; border-left:4px solid #f59e0b; border-radius:6px;">
                                <tr>
                                    <td style="padding:14px 16px;">
                                        <p style="margin:0; font-size:13px; color:#78350f; line-height:1.6;">
                                            <strong>⚠️ Penting</strong><br>
                                            Setelah login pertama, segera ubah password Anda melalui menu <strong>Profil → Ganti Password</strong> demi keamanan akun.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ============= CTA BUTTON ============= -->
                    <tr>
                        <td align="center" style="padding:8px 32px 32px;">
                            <a href="{{ $loginUrl }}"
                               style="display:inline-block; background:#16a34a; color:#ffffff; text-decoration:none; padding:14px 36px; border-radius:8px; font-size:15px; font-weight:600; box-shadow:0 2px 8px rgba(22,163,74,0.3);">
                                Login Sekarang →
                            </a>
                        </td>
                    </tr>

                    <!-- ============= INFO ============= -->
                    <tr>
                        <td style="padding:0 32px 32px;">
                            <p style="font-size:13px; color:#6b7280; line-height:1.6; margin:0; padding-top:16px; border-top:1px solid #e5e7eb;">
                                <strong>Butuh bantuan?</strong><br>
                                Jika mengalami kendala saat login, silakan hubungi admin sekolah untuk mendapatkan bantuan.
                            </p>
                        </td>
                    </tr>

                </table>

                <!-- ============= FOOTER ============= -->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; margin-top:16px;">
                    <tr>
                        <td style="text-align:center; padding:16px;">
                            <p style="font-size:12px; color:#9ca3af; margin:0; line-height:1.5;">
                                Email ini dikirim otomatis oleh sistem.<br>
                                <strong style="color:#6b7280;">Buku Induk SMA Negeri 3 Cilacap</strong><br>
                                Jl. Kalimantan No. 14, Cilacap Tengah, Jawa Tengah
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>
