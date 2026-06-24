@extends('layouts.sidebar-guru')

@section('content')

<div class="mb-4">

    <h4 class="fw-bold mb-2">
        Detail Jadwal Mengajar
    </h4>

    <div class="d-flex gap-2">
        <span class="badge bg-primary">Kelas: {{ $detail->nama_kelas }}</span>
        <span class="badge bg-success">Mapel: {{ $detail->nama_mapel }}</span>
    </div>

</div>

<div class="card table-card">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-striped table-hover mb-0">

                <thead>
                    <tr>
                        <th width="60" class="text-center">No</th>
                        <th>Nama Siswa</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($siswa as $s)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $s->nama_lengkap }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">Tidak ada data siswa</td>
                    </tr>
                    @endforelse

                </tbody>

            </table>

            <div class="mt-3">
                <a href="{{ url('guru/jadwal') }}" class="btn btn-secondary btn-sm">
                    ← Kembali
                </a>
            </div>

        </div>

    </div>

</div>

@endsection
