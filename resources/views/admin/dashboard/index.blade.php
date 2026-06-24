@extends('layouts.sidebar-admin')

@section('content')

<!-- HEADER SAPAAN -->
<div class="row mb-4">
    <div class="col-md-13">
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


<!-- KARTU STATISTIK -->
<div class="row g-4">

    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-primary text-white me-3">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h6 class="mb-1">Total Siswa</h6>
                    <h4 class="fw-bold">{{ $totalSiswa ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-success text-white me-3">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <h6 class="mb-1">Total Guru</h6>
                    <h4 class="fw-bold">{{ $totalGuru ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-warning text-white me-3">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h6 class="mb-1">Total Kelas</h6>
                    <h4 class="fw-bold">{{ $totalKelas ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-dark text-white me-3">
                    <i class="bi bi-book-fill"></i>
                </div>
                <div>
                    <h6 class="mb-1">Total Mapel</h6>
                    <h4 class="fw-bold">{{ $totalMapel ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- GRAFIK -->
<div class="row mt-4">

    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold mb-3">Rata-Rata Nilai Siswa</h6>
            <canvas id="nilaiChart"></canvas>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold mb-3">Jumlah Gender Siswa</h6>
            <div style="width:55%; margin:auto;">
                <canvas id="genderChart"></canvas>
            </div>
        </div>
    </div>

</div>


<!-- TABEL NILAI -->
<div class="card border-0 shadow-sm p-3 mt-4">

    <h6 class="fw-bold mb-3">10 Nilai Tertinggi</h6>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Nilai</th>
            </tr>
        </thead>

        <tbody>
        @foreach($topNilai ?? [] as $index => $nilai)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $nilai->nama_lengkap }}</td>
                <td>{{ $nilai->nama_kelas }}</td>
                <td>{{ $nilai->nilai_akhir }}</td>
            </tr>
        @endforeach
        </tbody>

    </table>

</div>

@endsection


@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener("DOMContentLoaded", function(){

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
        sapaan + ", Admin 👋";
    }

    updateJam();
    setInterval(updateJam,1000);


    /* BAR CHART */
    const nilaiChart = document.getElementById('nilaiChart');

    if(nilaiChart){
        new Chart(nilaiChart,{
            type:'bar',
            data:{
                labels:[
                    @foreach($rataNilaiKelas ?? [] as $kelas)
                    "{{ $kelas->nama_kelas }}",
                    @endforeach
                ],
                datasets:[{
                    label:'Rata-rata Nilai',
                    data:[
                        @foreach($rataNilaiKelas ?? [] as $kelas)
                        {{ round($kelas->rata_nilai,2) }},
                        @endforeach
                    ],
                    backgroundColor:'#3b82f6'
                }]
            }
        });
    }


    /* PIE CHART */
    const genderChart = document.getElementById('genderChart');

    if(genderChart){
        new Chart(genderChart,{
            type:'pie',
            data:{
                labels:{!! json_encode($gender->keys() ?? []) !!},
                datasets:[{
                    data:{!! json_encode($gender->values() ?? []) !!},
                    backgroundColor:['#3b82f6','#ec4899']
                }]
            }
        });
    }

});

</script>

@endpush