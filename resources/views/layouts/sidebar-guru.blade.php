<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Guru Panel</title>

<link rel="stylesheet"
      href="{{ asset('css/admin.css') }}">

<link rel="stylesheet"
      href="{{ asset('css/guru/sidebar-guru.css') }}">

<link rel="stylesheet"
      href="{{ asset('css/guru/navbar-guru.css') }}">
@yield('css')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
      rel="stylesheet">

</head>
<body>

@include('layouts.sidebar-guru-menu')

<div class="main-content">

    @yield('content')

</div>

@stack('scripts')

</body>
</html>