@php
    $isGuruMapel = (bool) session('is_guru_mapel');
    $isWali      = (bool) session('is_wali');
    $isPembina   = (bool) session('is_pembina');

    $totalRole =
        ($isGuruMapel ? 1 : 0) +
        ($isWali ? 1 : 0) +
        ($isPembina ? 1 : 0);

    $hasMultiRole = $totalRole > 1;

    if (request()->is('guru*')) {
        $modeAktif = 'guru';
    } elseif (request()->is('wali*')) {
        $modeAktif = 'wali';
    } elseif (request()->is('pembina*')) {
        $modeAktif = 'pembina';
    } else {
        $modeAktif = 'guru';
    }

    $roles = [];

    if ($isGuruMapel) $roles[] = 'Guru Mapel';

    if ($isWali) {
        $roles[] = 'Wali Kelas ' . ($kelasWaliGlobal->nama_kelas ?? '');
    }

    if ($isPembina) {
        $roles[] = 'Pembina Ekskul';
    }
@endphp

<div class="sidebar">

    <nav class="navbar-custom">
        <div class="nav-right">
            <span class="nama-sekolah">SMA NEGERI 3 CILACAP</span>
            <img src="{{ asset('img/logosma.png') }}" class="logo">
        </div>
    </nav>

    <div class="profile text-center mb-3">
        <img src="{{ asset('img/admin.png') }}" alt="Guru">

        <h6 class="mt-2">
            {{ session('nama_guru') }}
        </h6>

        <p class="text-muted mb-0" style="font-size:14px;">
            {{ implode(' • ', $roles) }}
        </p>
    </div>

    @if($hasMultiRole)

        <div class="menu-title">
            PILIH MODE
        </div>

        @if($isGuruMapel)
            <a href="/guru/jadwal"
               class="{{ $modeAktif === 'guru' ? 'active' : '' }}">
                <i class="bi bi-book me-2"></i>
                Guru Mapel
            </a>
        @endif

        @if($isWali)
            <a href="/wali/siswa"
               class="{{ $modeAktif === 'wali' ? 'active' : '' }}">
                <i class="bi bi-house-door me-2"></i>
                Wali Kelas
            </a>
        @endif

        @if($isPembina)
            <a href="/pembina/siswa"
               class="{{ $modeAktif === 'pembina' ? 'active' : '' }}">
                <i class="bi bi-award-fill me-2"></i>
                Pembina Ekskul
            </a>
        @endif

    @endif

    <div class="menu-title">
        DASHBOARD
    </div>

    <a href="/guru/dashboard"
       class="{{ request()->is('guru/dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2 me-2"></i>
        Dashboard
    </a>

    @if($modeAktif === 'guru')

        <div class="menu-title mt-3">
            MENU GURU MAPEL
        </div>

        <a href="/guru/jadwal"
           class="{{ request()->is('guru/jadwal*') ? 'active' : '' }}">
            <i class="bi bi-clock-fill me-2"></i>
            Jadwal Mengajar
        </a>

        <a href="/guru/nilai"
           class="{{ request()->is('guru/nilai*') ? 'active' : '' }}">
            <i class="bi bi-journal-check me-2"></i>
            Input Nilai
        </a>

        <a href="/guru/absensi"
           class="{{ request()->is('guru/absensi*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check-fill me-2"></i>
            Input Absensi
        </a>

    @elseif($modeAktif === 'wali')

        <div class="menu-title mt-3">
            MENU WALI KELAS
        </div>

        <a href="/wali/siswa"
           class="{{ request()->is('wali/siswa*') ? 'active' : '' }}">
            <i class="bi bi-people me-2"></i>
            Data Siswa
        </a>

        <a href="/wali/kehadiran"
           class="{{ request()->is('wali/kehadiran*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check me-2"></i>
            Pantau Kehadiran
        </a>

        <a href="/wali/nilai"
           class="{{ request()->is('wali/nilai*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart me-2"></i>
            Pantau Nilai
        </a>

        <a href="/wali/ekskul"
           class="{{ request()->is('wali/ekskul*') ? 'active' : '' }}">
            <i class="bi bi-star me-2"></i>
            Nilai Ekstrakurikuler
        </a>

        <a href="/wali/rekap"
           class="{{ request()->is('wali/rekap*') ? 'active' : '' }}">
            <i class="bi bi-printer me-2"></i>
            Rekap Rapor
        </a>

    @elseif($modeAktif === 'pembina')

        <div class="menu-title mt-3">
            MENU PEMBINA EKSKUL
        </div>

        <a href="/pembina/siswa"
           class="{{ request()->is('pembina/siswa*') ? 'active' : '' }}">
            <i class="bi bi-people me-2"></i>
            Data Siswa
        </a>

        <a href="/pembina/nilai"
           class="{{ request()->is('pembina/nilai*') ? 'active' : '' }}">
            <i class="bi bi-award me-2"></i>
            Nilai Ekstrakurikuler
        </a>

        <a href="{{ url('pembina/riwayat-nilai') }}"
        class="{{ request()->is('pembina/riwayat*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            Riwayat Nilai
        </a>
    @endif

    <div class="menu-title mt-3">
        AKUN
    </div>

    <a href="/guru/profil"
       class="{{ request()->is('guru/profil*') ? 'active' : '' }}">
        <i class="bi bi-person-circle me-2"></i>
        Profil
    </a>

    <div class="logout">
        <a href="/logout">
            <i class="bi bi-box-arrow-right me-2 text-danger"></i>
            <span class="text-danger">Keluar</span>
        </a>
    </div>

</div>