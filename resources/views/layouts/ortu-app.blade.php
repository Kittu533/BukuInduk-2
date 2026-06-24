<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Orang Tua Panel</title>

    {{-- BOOTSTRAP --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- BOOTSTRAP ICON --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/orangtua/sidebar-ortu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orangtua/navbar-ortu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orangtua/nilai-ortu.css') }}">

    @yield('css')

</head>
<body>

<div class="wrapper">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar-ortu')

    {{-- NAVBAR --}}
    @include('layouts.navbar-ortu')

    <div class="main-content">
        @yield('content')
    </div>

</div>

{{-- JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</body>
</html>