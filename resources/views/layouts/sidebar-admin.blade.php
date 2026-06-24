<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel</title>

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-guru.css') }}">
<link rel="stylesheet" href="{{ asset('css/navbar-admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/ekskul.css') }}">
<link rel="stylesheet" href="{{ asset('css/jadwal-mengajar.css') }}">
<link rel="stylesheet" href="{{ asset('css/kelasaktif.css') }}">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<link rel="stylesheet" href="{{ asset('css/mapel.css') }}">
<link rel="stylesheet" href="{{ asset('css/semester.css') }}">
<link rel="stylesheet" href="{{ asset('css/siswa.css') }}">
<link rel="stylesheet" href="{{ asset('css/tahun_ajaran.css') }}">
<link rel="stylesheet" href="{{ asset('css/detail-nilai.css') }}">
<link rel="stylesheet" href="{{ asset('css/nilai-ekskul-detail.css') }}">
<link rel="stylesheet" href="{{ asset('css/kelola-akun.css') }}">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>

@include('layouts.sidebar')

<div class="main-content">
    <div class="container-fluid"> @yield('content')
    </div>
</div>

@stack('scripts')

</body>
</html>