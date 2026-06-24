@extends('layouts.sidebar-admin')

@section('content')

<h3 class="page-title">
    DETAIL KEHADIRAN SISWA
</h3>

<div class="d-flex justify-content-between align-items-center mb-3">
        
    <a href="{{ url('admin/kehadiran/create/'.$siswa->id_siswa) }}"
       class="btn btn-success btn-sm">

        + Tambah

    </a>

</div>

<div class="card table-card mb-3">

    <div class="card-body">

        <table class="table table-bordered">

            <tr>

                <th width="220">
                    NIS
                </th>

                <td>
                    {{ $siswa->nis }}
                </td>

            </tr>

            <tr>

                <th>
                    Nama Siswa
                </th>

                <td>
                    {{ $siswa->nama_lengkap }}
                </td>

            </tr>

            <tr>

                <th>
                    Semester
                </th>

                <td>
                    {{ $kelas->kelasAktif->semester->nama_semester ?? '-' }}
                </td>

            </tr>

            <tr>

                <th>
                    Tahun Ajaran
                </th>

                <td>
                    {{ $kelas->kelasAktif->tahunAjaran->tahun ?? '-' }}
                </td>

            </tr>

        </table>

    </div>

</div>

<div class="row g-3 mb-3">

    <div class="col-md-2 col-sm-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body text-center rounded"
                 style="background:#198754;color:white;">

                <h6 class="mb-2">
                    Hadir
                </h6>

                <h1 class="fw-bold mb-0">
                    {{ $hadir }}
                </h1>

            </div>

        </div>

    </div>

    <div class="col-md-2 col-sm-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body text-center rounded"
                 style="background:#ffc107;color:white;">

                <h6 class="mb-2">
                    Sakit
                </h6>

                <h1 class="fw-bold mb-0">
                    {{ $sakit }}
                </h1>

            </div>

        </div>

    </div>

    <div class="col-md-2 col-sm-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body text-center rounded"
                 style="background:#0dcaf0;color:white;">

                <h6 class="mb-2">
                    Izin
                </h6>

                <h1 class="fw-bold mb-0">
                    {{ $izin }}
                </h1>

            </div>

        </div>

    </div>

    <div class="col-md-2 col-sm-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body text-center rounded"
                 style="background:#dc3545;color:white;">

                <h6 class="mb-2">
                    Alpa
                </h6>

                <h1 class="fw-bold mb-0">
                    {{ $alpa }}
                </h1>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body rounded d-flex align-items-center justify-content-between"
                 style="background:#6f42c1;color:white;">

                <div>

                    <h5 class="mb-1">
                        Persentase Kehadiran
                    </h5>

                    <small>
                        Total Kehadiran Semester
                    </small>

                </div>

                <h1 class="fw-bold mb-0">
                    {{ $persentase }}%
                </h1>

            </div>

        </div>

    </div>

</div>

<div class="card table-card">

    <div class="card-body">

        <table class="table table-bordered table-striped">

            <thead>

                <tr>

                    <th class="text-center" width="60">
                        No
                    </th>

                    <th class="text-center">
                        Tanggal
                    </th>

                    <th>
                        Mata Pelajaran
                    </th>

                    <th>
                        Guru
                    </th>

                    <th class="text-center">
                        Status
                    </th>
                </tr>

            </thead>

            <tbody>

                @forelse($kehadiran as $k)

                    <tr>

                        <td class="text-center">
                            {{ $loop->iteration }}
                        </td>

                        <td class="text-center">
                            {{ date('d-m-Y', strtotime($k->tanggal)) }}
                        </td>

                        <td>
                            {{ $k->nama_mapel }}
                        </td>

                        <td>
                            {{ $k->nama_guru }}
                        </td>

                        <td class="text-center">

                            @if($k->status == 'hadir')

                                <span class="badge bg-success">
                                    Hadir
                                </span>

                            @elseif($k->status == 'sakit')

                                <span class="badge bg-warning">
                                    Sakit
                                </span>

                            @elseif($k->status == 'izin')

                                <span class="badge bg-info">
                                    Izin
                                </span>

                            @else

                                <span class="badge bg-danger">
                                    Alpa
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="text-center">
                            Data kehadiran belum tersedia
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

        <a href="{{ url('admin/kehadiran/'.$kelas->id_kelas_aktif) }}"
           class="btn btn-secondary btn-sm mt-3">

            Kembali

        </a>

    </div>

</div>

@endsection