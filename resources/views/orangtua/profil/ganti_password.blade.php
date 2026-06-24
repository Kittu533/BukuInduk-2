@extends('layouts.app-ortu')

@section('content')

<h3 class="page-title">GANTI PASSWORD</h3>

<div class="card">
    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ url('orangtua/profil/password') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Password Lama</label>
                <input type="password" name="password_lama" class="form-control" required>
                @error('password_lama')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password_baru" class="form-control" required>
                @error('password_baru')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_baru_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ url('orangtua/profil') }}" class="btn btn-secondary">Kembali</a>
        </form>

        <hr>

        <div class="mt-3">
            <h6>Atau Reset via Email</h6>
            <p class="text-muted small">
                Sistem akan generate password baru otomatis dan kirim ke email Anda.
            </p>
            <form action="{{ url('orangtua/profil/reset-email') }}" method="POST"
                  onsubmit="return confirm('Yakin ingin reset password? Password baru akan dikirim ke email Anda.')">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm">
                    📧 Kirim Password Baru ke Email
                </button>
            </form>
        </div>

    </div>
</div>

@endsection
