@extends('layouts.ortu-app')

@section('content')

<!-- SAPAAN -->
<div class="row mb-4 mt-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4"
             style="background:#eef2ff; border-radius:14px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-people-fill text-primary me-3"
                   style="font-size:30px;"></i>
                <div>
                    <h4 class="fw-bold mb-1" id="sapaan"></h4>
                    <p class="mb-1 text-dark">
                        Orang Tua dari :
                        <span class="fw-bold">
                            {{ $siswa->nama_lengkap }}
                        </span>
                    </p>
                    <p class="text-muted mb-0">

                        NIS :
                        {{ $siswa->nis }}

                        |
                        <span id="tanggal"></span>

                        |
                        <span id="jam"></span>
                    </p>

                    <div class="mt-2">

                        @if($statusSiswa == 'Aktif')

                            <span class="badge bg-success">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                Siswa Aktif
                            </span>

                        @else

                            <span class="badge bg-danger">
                                <i class="bi bi-x-circle-fill me-1"></i>
                                Siswa Tidak Aktif
                            </span>

                        @endif

                        @if($kelasAktif)

                            <span class="badge bg-primary ms-2">
                                Tahun Ajaran {{ $kelasAktif->tahun }}
                            </span>

                            <span class="badge bg-secondary ms-1">
                                Semester {{ $kelasAktif->nama_semester }}
                            </span>

                            <span class="badge bg-info ms-1">
                                Kelas {{ $kelasAktif->nama_kelas }}
                            </span>

                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MENU CEPAT --}}
<div class="row g-4 mt-2">

    {{-- KEHADIRAN --}}
    <div class="col-md-3">

        <a href="{{ url('orangtua/kehadiran') }}"
           class="text-decoration-none">

            <div class="card border-0 shadow-sm p-4 text-center menu-card">

                <i class="bi bi-calendar-check-fill
                          text-success fs-1"></i>

                <h5 class="mt-3 text-dark">
                    Kehadiran
                </h5>

            </div>

        </a>

    </div>

    {{-- NILAI --}}
    <div class="col-md-3">

        <a href="{{ url('orangtua/nilai') }}"
           class="text-decoration-none">

            <div class="card border-0 shadow-sm p-4 text-center menu-card">

                <i class="bi bi-bar-chart-fill
                          text-primary fs-1"></i>

                <h5 class="mt-3 text-dark">
                    Nilai Siswa
                </h5>

            </div>

        </a>

    </div>

    {{-- REKAP --}}
    <div class="col-md-3">

        <a href="{{ url('orangtua/rekap') }}"
           class="text-decoration-none">

            <div class="card border-0 shadow-sm p-4 text-center menu-card">

                <i class="bi bi-journal-text
                          text-warning fs-1"></i>

                <h5 class="mt-3 text-dark">
                    Rekap Nilai
                </h5>

            </div>

        </a>

    </div>

    {{-- JADWAL --}}
    <div class="col-md-3">

        <a href="{{ url('orangtua/jadwal') }}"
           class="text-decoration-none">

            <div class="card border-0 shadow-sm p-4 text-center menu-card">

                <i class="bi bi-calendar-week-fill
                          text-danger fs-1"></i>

                <h5 class="mt-3 text-dark">
                    Jadwal Pelajaran
                </h5>

            </div>

        </a>

    </div>

</div>

@endsection

@push('scripts')

<script>

document.addEventListener("DOMContentLoaded", function(){

    function updateJam(){

        const now = new Date();

        let jam = now.getHours();
        let menit = now.getMinutes();
        let detik = now.getSeconds();

        const jamAsli = jam;

        jam = jam < 10 ? '0'+jam : jam;
        menit = menit < 10 ? '0'+menit : menit;
        detik = detik < 10 ? '0'+detik : detik;

        document.getElementById("jam").innerHTML =
        jam + ":" + menit + ":" + detik;

        const hari = [
            'Minggu',
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu'
        ];

        const bulan = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        document.getElementById("tanggal").innerHTML =
        hari[now.getDay()] + ", " +
        now.getDate() + " " +
        bulan[now.getMonth()] + " " +
        now.getFullYear();

        let sapaan = "";

        if(jamAsli < 12){

            sapaan = "Selamat Pagi";

        }else if(jamAsli < 15){

            sapaan = "Selamat Siang";

        }else if(jamAsli < 18){

            sapaan = "Selamat Sore";

        }else{

            sapaan = "Selamat Malam";

        }

        document.getElementById("sapaan").innerHTML =
        sapaan + ", Bapak/Ibu";

    }

    updateJam();

    setInterval(updateJam,1000);

});

</script>

@endpush