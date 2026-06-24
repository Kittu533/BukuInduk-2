<div class="sidebar">

    <!-- ================= NAVBAR ================= -->
    <nav class="navbar-custom">

        <div class="nav-right">
            <span class="nama-sekolah">
                SMA NEGERI 3 CILACAP
            </span>
            <img src="{{ asset('img/logosma.png') }}" class="logo">
        </div>

    </nav>

    <!-- ================= PROFILE ================= -->
    <div class="profile text-center mb-3">
        <img src="{{ asset('img/admin.png') }}" alt="Admin">
        <h6 class="mt-2">{{ session('nama_admin') ?? 'Super Admin' }}</h6>
        <p class="text-muted mb-0" style="font-size:13px;">Administrator</p>
    </div>

    <!-- ================= DASHBOARD ================= -->
    <div class="menu-title">DASHBOARD</div>

    <a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>

    <!-- ================= MASTER DATA ================= -->
    <div class="menu-title mt-3">MASTER DATA</div>

    <a href="/admin/siswa" class="{{ request()->is('admin/siswa*') || request()->is('siswa/*') || request()->is('bukuinduk/*') ? 'active' : '' }}">
        <i class="bi bi-people-fill me-2"></i> Data Siswa
    </a>

    <a href="/admin/alumni" class="{{ request()->is('admin/alumni*') ? 'active' : '' }}">
        <i class="bi bi-mortarboard-fill me-2"></i> Data Alumni
    </a>

    <a href="/admin/kelasaktif" class="{{ request()->is('admin/kelasaktif*') ? 'active' : '' }}">
        <i class="bi bi-building me-2"></i> Data Kelas
    </a>

    <a href="/admin/guru" class="{{ request()->is('admin/guru*') ? 'active' : '' }}">
        <i class="bi bi-person-badge-fill me-2"></i> Data Guru
    </a>

    <a href="/admin/mapel" class="{{ request()->is('admin/mapel*') ? 'active' : '' }}">
        <i class="bi bi-book-fill me-2"></i> Mata Pelajaran
    </a>

    <a href="/admin/ekskul" class="{{ request()->is('admin/ekskul*') ? 'active' : '' }}">
        <i class="bi bi-trophy-fill me-2"></i> Ekstrakurikuler
    </a>

    <!-- ================= AKADEMIK ================= -->
    <div class="menu-title mt-3">AKADEMIK</div>

    <a href="/admin/tahun-ajaran" class="{{ request()->is('admin/tahun-ajaran*') ? 'active' : '' }}">
        <i class="bi bi-calendar-range-fill me-2"></i> Tahun Ajaran
    </a>

    <a href="/admin/semester" class="{{ request()->is('admin/semester*') ? 'active' : '' }}">
        <i class="bi bi-calendar2-week-fill me-2"></i> Semester
    </a>

    <a href="/admin/jadwal-mengajar" class="{{ request()->is('admin/jadwal-mengajar*') ? 'active' : '' }}">
        <i class="bi bi-clock-fill me-2"></i> Jadwal Mengajar
    </a>

    <a href="/admin/kehadiran" class="{{ request()->is('admin/kehadiran*') ? 'active' : '' }}">
        <i class="bi bi-clipboard-check-fill me-2"></i> Kehadiran
    </a>

    <a href="/admin/nilai_akademik" class="{{ request()->is('admin/nilai_akademik*') ? 'active' : '' }}">
        <i class="bi bi-journal-check me-2"></i> Nilai Akademik
    </a>

    <a href="/admin/nilai_ekskul" class="{{ request()->is('admin/nilai_ekskul*') ? 'active' : '' }}">
        <i class="bi bi-award-fill me-2"></i> Nilai Ekstrakurikuler
    </a>

    <a href="/admin/rekap" class="{{ request()->is('admin/rekap*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text-fill me-2"></i> Rekap Nilai
    </a>

    <a href="/admin/kenaikan" class="{{ request()->is('admin/kenaikan*') ? 'active' : '' }}">
        <i class="bi bi-arrow-up-circle-fill me-2"></i> Kenaikan/Kelulusan
    </a>

    <!-- ================= MANAJEMEN ================= -->
    <div class="menu-title mt-3">MANAJEMEN</div>

    <a href="/admin/kelola-akun" class="{{ request()->is('admin/kelola-akun*') ? 'active' : '' }}">
        <i class="bi bi-person-gear me-2"></i> Kelola Akun
    </a>

    <a href="/admin/profil-sekolah" class="{{ request()->is('admin/profil-sekolah*') ? 'active' : '' }}">
        <i class="bi bi-building-gear me-2"></i> Profil Sekolah
    </a>

    <!-- ================= LOGOUT ================= -->
    <div class="logout">
        <a href="/logout">
            <i class="bi bi-box-arrow-right me-2 text-danger"></i>
            <span class="text-danger">Keluar</span>
        </a>
    </div>

</div>
