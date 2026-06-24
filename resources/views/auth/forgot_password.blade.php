<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password — Buku Induk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            max-width: 450px;
            width: 100%;
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .card-header {
            background: #2563eb;
            color: white;
            text-align: center;
            border-radius: 12px 12px 0 0 !important;
            padding: 25px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">🔑 Lupa Password</h4>
        </div>

        <div class="card-body p-4">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <p class="text-muted mb-3">
                Masukkan email yang terdaftar. Sistem akan mengirim password baru ke email Anda.
            </p>

            <form action="{{ url('/forgot-password') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="contoh@email.com" required value="{{ old('email') }}">
                </div>

                <button type="submit" class="btn btn-primary w-100">Kirim Password Baru</button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ url('/') }}" class="text-decoration-none">← Kembali ke Login</a>
            </div>

        </div>
    </div>
</body>
</html>
