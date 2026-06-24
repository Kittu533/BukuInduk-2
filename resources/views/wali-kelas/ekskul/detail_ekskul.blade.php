@extends('layouts.sidebar-walas')

@section('content')

<h3 class="page-title">
    DETAIL NILAI EKSTRAKURIKULER
</h3>

<div class="card table-card">

    <div class="card-body">

        <!-- ================= INFO SISWA ================= -->

        <table class="table table-borderless mb-4">
            <tr>
                <td width="180"><b>Nama Siswa</b></td>
                <td>: {{ $siswa->nama_lengkap }}</td>
            </tr>
            <tr>
                <td><b>NIS</b></td>
                <td>: {{ $siswa->nis }}</td>
            </tr>
            <tr>
                <td><b>Kelas</b></td>
                <td>: {{ $kelasSiswa?->kelasAktif?->kelas?->nama_kelas ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>Tahun Ajaran</b></td>
                <td>: {{ $kelasSiswa?->kelasAktif?->tahunAjaran?->tahun ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>Semester</b></td>
                <td>: {{ $kelasSiswa?->kelasAktif?->semester?->nama_semester ?? '-' }}</td>
            </tr>
        </table>

        <!-- ================= TABEL NILAI ================= -->

        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th width="60" class="text-center">No</th>
                    <th class="text-center">Ekstrakurikuler</th>
                    <th width="120" class="text-center">Nilai</th>
                </tr>
            </thead>

            <tbody>

                @forelse($data as $d)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $d->ekstrakurikuler->nama_ekskul ?? '-' }}</td>
                    <td class="text-center">
                        @if($d->nilai == 'A')
                            <span class="badge bg-success">A</span>
                        @elseif($d->nilai == 'B')
                            <span class="badge bg-primary">B</span>
                        @elseif($d->nilai == 'C')
                            <span class="badge bg-warning text-dark">C</span>
                        @else
                            <span class="badge bg-secondary">{{ $d->nilai }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        Belum ada nilai ekstrakurikuler
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

        <!-- ================= BUTTON ================= -->

        <div class="mt-3">
            <a href="{{ url('wali/ekskul') }}"
               class="btn btn-secondary btn-sm">
                Kembali
            </a>
        </div>

    </div>

</div>

@endsection
