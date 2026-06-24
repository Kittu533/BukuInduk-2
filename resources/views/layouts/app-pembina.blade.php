<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pembina Ekskul Panel</title>

    {{-- BOOTSTRAP --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- BOOTSTRAP ICON --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/guru/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/guru/navbar.css') }}">

    @yield('css')

</head>
<body>

<div class="wrapper">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar-guru')

    <div class="main-wrapper">

        {{-- NAVBAR --}}
        @include('layouts.navbar-pembina')

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</body>
</html>