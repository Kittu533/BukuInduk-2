<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * AkunBaruMail — Email pemberitahuan akun baru
 *
 * Dikirim saat admin membuat akun user baru.
 * Berisi username & password sementara untuk login pertama.
 *
 * @legacy Fitur baru (30 Mei 2026)
 */
class AkunBaruMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $nama;
    public string $username;
    public string $password;
    public string $role;
    public string $loginUrl;

    public function __construct(string $nama, string $username, string $password, string $role)
    {
        $this->nama = $nama;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->loginUrl = url('/');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎓 Selamat Datang di Buku Induk SMA Negeri 3 Cilacap',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.akun_baru',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
