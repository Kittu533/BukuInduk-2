@extends('layouts.sidebar-guru')

@section('css')

<link rel="stylesheet" href="{{ asset('css/guru/dashboard.css') }}">

@endsection


@section('content')

<!-- =========================
     WELCOME
========================= -->

<!-- HEADER SAPAAN -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4" style="background:#eef2ff; border-radius:14px;">

            <div class="d-flex align-items-center">
                <i class="bi bi-sun-fill text-warning me-3" style="font-size:30px;"></i>

                <div>
                    <h4 class="fw-bold mb-1" id="sapaan"></h4>

                    <p class="text-muted mb-0">
                        <span id="tanggal"></span> |
                        <span id="jam"></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row g-3">
{{-- =========================
     GURU MAPEL
========================= --}}
@if($isGuruMapel)

<div class="col-md-6">
    <div class="dashboard-card">
        <div class="card-item">
            <h5>
                <i class="bi bi-book-fill me-2 text-primary"></i>
                Kelas Diampu
            </h5>

            <h2 class="text-primary">
                {{ $totalKelasDiampu }}
            </h2>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="dashboard-card">
        <div class="card-item">
            <h5>
                <i class="bi bi-people-fill me-2 text-success"></i>
                Jumlah Siswa Diampu
            </h5>

            <h2 class="text-success">
                {{ $totalSiswaDiampu }}
            </h2>
        </div>
    </div>
</div>

@endif

{{-- =========================
     WALI KELAS
========================= --}}
@if($isWali)

<div class="col-md-6">
    <div class="dashboard-card">
        <div class="card-item">
            <h5>
                <i class="bi bi-person-lines-fill me-2 text-info"></i>
                Jumlah Siswa Wali
            </h5>

            <h2 class="text-info">
                {{ $totalSiswaWali }}
            </h2>
        </div>
    </div>
</div>

@endif


{{-- =========================
     PEMBINA EKSKUL
========================= --}}
@if($isPembina)

<div class="col-md-6">
    <div class="dashboard-card">
        <div class="card-item">
            <h5>
                <i class="bi bi-award-fill me-2 text-warning"></i>
                Anggota Ekskul
            </h5>

            <h2 class="text-warning">
                {{ $totalAnggotaEkskul }}
            </h2>
        </div>
    </div>
</div>

@endif

</div>

{{-- =========================
REMINDER
========================= --}}

<div class="card mt-4 shadow-sm">

    <div class="card-header fw-bold">
        Reminder
    </div>

    <div class="card-body">

        @php
            $adaReminder =
                ($detailNilaiBelumLengkap->count() ?? 0) > 0 ||
                ($detailNilaiKosong->count() ?? 0) > 0;
        @endphp

        @if($adaReminder)

            <ul class="mb-0">

                {{-- GURU MAPEL --}}
                @foreach($detailNilaiBelumLengkap as $item)

                    <li class="mb-2">

                        <span class="text-danger fw-semibold">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Nilai belum lengkap
                        </span>

                        <br>

                        {{ $item->nama_mapel }}
                        -
                        {{ $item->nama_kelas }}

                        ({{ $item->total }} siswa)

                    </li>

                @endforeach


                {{-- WALI KELAS - BANYAK ALPA --}}
                @foreach($detailSiswaBolos as $item)

                    <li class="mb-2">

                        <span class="text-warning fw-semibold">
                            <i class="bi bi-person-fill-exclamation"></i>
                            Sering bolos
                        </span>

                        <br>

                        {{ $item->nama_lengkap }}
                        ({{ $item->total_alpa }} kali alpa)

                    </li>

                @endforeach


                {{-- WALI KELAS - NILAI KOSONG --}}
                @foreach($detailNilaiKosong as $item)

                    <li class="mb-2">

                        <span class="text-primary fw-semibold">
                            <i class="bi bi-journal-x"></i>
                            Nilai siswa belum lengkap
                        </span>

                        <br>

                        {{ $item->nama_lengkap }}

                    </li>

                @endforeach

            </ul>

        @else

            <div class="text-success fw-semibold">

                <i class="bi bi-check-circle-fill"></i>

                Semua data sudah lengkap.

            </div>

        @endif

    </div>

</div>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function(){

    const namaGuru = @json(session('nama_guru'));

    function updateJam(){

        const now = new Date();

        let jam = now.getHours();
        let menit = now.getMinutes();
        let detik = now.getSeconds();

        jam = jam < 10 ? '0'+jam : jam;
        menit = menit < 10 ? '0'+menit : menit;
        detik = detik < 10 ? '0'+detik : detik;

        document.getElementById("jam").innerHTML =
        jam + ":" + menit + ":" + detik;

        const hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli',
                       'Agustus','September','Oktober','November','Desember'];

        document.getElementById("tanggal").innerHTML =
        hari[now.getDay()] + ", " +
        now.getDate() + " " +
        bulan[now.getMonth()] + " " +
        now.getFullYear();

        let sapaan = "";

        if(jam < 12){
            sapaan = "Selamat Pagi";
        }else if(jam < 15){
            sapaan = "Selamat Siang";
        }else if(jam < 18){
            sapaan = "Selamat Sore";
        }else{
            sapaan = "Selamat Malam";
        }

        document.getElementById("sapaan").innerHTML =
        sapaan + ", " + namaGuru + " 👋";
    }

    updateJam();
    setInterval(updateJam,1000);

});
</script>
@endpush