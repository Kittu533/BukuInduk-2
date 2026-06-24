<div class="sidebar">

    <!-- PROFILE -->
    <div class="profile-box">

        <img src="{{ asset('img/pas_foto.jpeg') }}"
             alt="Orang Tua"
             class="profile-img">
        <div class="profile-info">
            <h6>
                {{ $siswa->nama_lengkap }}
            </h6>
            <p>
                NIS : {{ $siswa->nis }}
            </p>
            
        </div>
    </div>

    <!-- MENU -->
    <div class="menu-title">
        MENU
    </div>

    <!-- DASHBOARD -->
    <a href="/orangtua/dashboard"
       class="{{ request()->is('orangtua/dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2 me-2"></i>
        Dashboard
    </a>

    <!-- NILAI -->
    <a href="/orangtua/nilai"
       class="{{ request()->is('orangtua/nilai*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart me-2"></i>
        Nilai
    </a>

    <!-- ABSENSI -->
    <a href="/orangtua/absensi"
       class="{{ request()->is('orangtua/absensi*') ? 'active' : '' }}">
        <i class="bi bi-clipboard-check me-2"></i>
        Absensi
    </a>

    <!-- REKAP -->
    <a href="/orangtua/rekap"
       class="{{ request()->is('orangtua/rekap*') ? 'active' : '' }}">
        <i class="bi bi-journal-text me-2"></i>
        Rekap Nilai
    </a>

    <!-- JADWAL -->
    <a href="/orangtua/jadwal"
       class="{{ request()->is('orangtua/jadwal*') ? 'active' : '' }}">
        <i class="bi bi-calendar3 me-2"></i>
        Jadwal
    </a>

    <!-- AKUN -->
    <div class="menu-title mt-3">
        AKUN
    </div>

    <!-- PROFIL -->
    <a href="/orangtua/profil"
       class="{{ request()->is('orangtua/profil*') ? 'active' : '' }}">
        <i class="bi bi-person-circle me-2"></i>
        Profil
    </a>

    <!-- LOGOUT -->
    <div class="logout">
        <a href="/logout">
            <i class="bi bi-box-arrow-right me-2 text-danger"></i>
            <span class="text-danger">
                Keluar
            </span>
        </a>
    </div>

</div>