@extends('layouts.sidebar-admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h2 class="fw-bold">
            DETAIL ALUMNI
        </h2>

        <a href="{{ url('admin/alumni') }}"
           class="btn btn-secondary">
            Kembali
        </a>

    </div>

    @if($riwayat->isNotEmpty())
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <strong>Pilih Riwayat Nilai</strong>
        </div>

        <div class="card-body">
            <form method="GET"
                  action="{{ route('admin.alumni.detail', $siswa->id_siswa) }}"
                  class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Kelas / Tahun / Semester</label>
                    <select name="id_kelas_aktif" class="form-control">
                        @foreach($riwayat as $item)
                            <option value="{{ $item->id_kelas_aktif }}"
                                {{ optional($riwayatAktif)->id_kelas_aktif === $item->id_kelas_aktif ? 'selected' : '' }}>
                                {{ $item->kelas }} - {{ $item->tahun }} - {{ $item->semester }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        Lihat Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- IDENTITAS --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <strong>Data Identitas Alumni</strong>
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th width="25%">ID Siswa</th>
                    <td>{{ $siswa->id_siswa }}</td>
                </tr>

                <tr>
                    <th>NIS</th>
                    <td>{{ $siswa->nis }}</td>
                </tr>

                <tr>
                    <th>NISN</th>
                    <td>{{ $siswa->nisn }}</td>
                </tr>

                <tr>
                    <th>Nama Lengkap</th>
                    <td>{{ $siswa->nama_lengkap }}</td>
                </tr>

                <tr>
                    <th>Tempat, Tanggal Lahir</th>
                    <td>
                        {{ $siswa->tempat_lahir }},
                        {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}
                    </td>
                </tr>

                <tr>
                    <th>Jenis Kelamin</th>
                    <td>{{ $siswa->jenis_kelamin }}</td>
                </tr>

                <tr>
                    <th>Agama</th>
                    <td>{{ $siswa->agama }}</td>
                </tr>

                <tr>
                    <th>Alamat</th>
                    <td>{{ $siswa->alamat }}</td>
                </tr>

                <tr>
                    <th>No Telepon</th>
                    <td>{{ $siswa->no_telp }}</td>
                </tr>

                <tr>
                    <th>Tahun Masuk</th>
                    <td>
                        {{ \Carbon\Carbon::parse($siswa->tahun_masuk)->format('Y') }}
                    </td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge bg-success">
                            {{ ucfirst($siswa->status_siswa) }}
                        </span>
                    </td>
                </tr>

            </table>

        </div>

    </div>

    {{-- RIWAYAT KELAS --}}
<div class="card shadow-sm mb-4">

    <div class="card-header">
        <strong>Riwayat Kelas</strong>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="table-light">

                    <tr class="text-center">
                        <th width="5%">No</th>
                        <th>Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($riwayat as $item)

                    <tr>

                        <td class="text-center">
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $item->kelas }}
                        </td>

                        <td>
                            {{ $item->tahun }}
                        </td>

                        <td>
                            {{ $item->semester }}
                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="4" class="text-center">
                            Riwayat kelas tidak tersedia
                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

    {{-- NILAI AKADEMIK --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <strong>Nilai Akademik</strong>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered">

                    <thead>

                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Mata Pelajaran</th>
                            <th>Nilai Akhir</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($nilai as $item)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $item->nama_mapel }}</td>

                            <td class="text-center">
                                {{ $item->nilai_akhir }}
                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="3" class="text-center">
                                Data nilai tidak tersedia
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- NILAI EKSKUL --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <strong>Nilai Ekstrakurikuler</strong>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered">

                    <thead>

                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Ekstrakurikuler</th>
                            <th>Nilai</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($ekskul as $item)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td>
                                {{ $item->ekstrakurikuler->nama_ekskul ?? '-' }}
                            </td>

                            <td class="text-center">
                                {{ $item->nilai }}
                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="3" class="text-center">
                                Data ekstrakurikuler tidak tersedia
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- KEHADIRAN --}}
    <div class="card shadow-sm">

        <div class="card-header">
            <strong>Rekap Kehadiran</strong>
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th width="30%">Sakit</th>
                    <td>{{ $kehadiran->sakit }}</td>
                </tr>

                <tr>
                    <th>Izin</th>
                    <td>{{ $kehadiran->izin }}</td>
                </tr>

                <tr>
                    <th>Alpa</th>
                    <td>{{ $kehadiran->alpa }}</td>
                </tr>

            </table>

        </div>

    </div>

</div>

@endsection
