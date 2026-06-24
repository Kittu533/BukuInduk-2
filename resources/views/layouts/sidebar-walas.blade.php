<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Wali Kelas Panel</title>

    {{-- CSS UTAMA --}}
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/wali-kelas/walas-sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/wali-kelas/walas-navbar.css') }}">

    {{-- BOOTSTRAP --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    {{-- CSS TAMBAHAN --}}
    @yield('css')

</head>
<body>

<div class="wrapper">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar-walas-menu')

    {{-- NAVBAR --}}
    @include('layouts.navbar-walas')

    {{-- CONTENT --}}
    <div class="main-content">
        @yield('content')
    </div>

</div>

{{-- JS --}}
@stack('scripts')

</body>
</html>