<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * ResetPasswordMail — Email reset password
 *
 * Dikirim saat user mengajukan lupa password.
 * Berisi password sementara yang baru.
 *
 * @legacy Fitur baru (30 Mei 2026)
 */
class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $nama;
    public string $username;
    public string $passwordBaru;
    public string $loginUrl;

    public function __construct(string $nama, string $username, string $passwordBaru)
    {
        $this->nama = $nama;
        $this->username = $username;
        $this->passwordBaru = $passwordBaru;
        $this->loginUrl = url('/');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Password Akun Buku Induk Berhasil Direset',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset_password',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
