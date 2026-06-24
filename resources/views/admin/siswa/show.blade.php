@extends('layouts.sidebar-admin')

@section('content')

<h3 class="page-title">
    DAFTAR SISWA
</h3>

<div class="card table-card">

    <div class="card-body">

        <div class="alert alert-light border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div>
                <div class="fw-semibold">{{ $kelasAktif->kelas->nama_kelas ?? '-' }}</div>
                <small class="text-muted">
                    Wali kelas: <strong>{{ $kelasAktif->guru->nama_guru ?? '-' }}</strong> |
                    Siswa aktif: <strong>{{ $kelasAktif->jumlah_siswa_aktif ?? 0 }}</strong>
                </small>
            </div>

            @if($kelasAktif->is_priority_intake)
                <span class="badge bg-warning text-dark">Prioritas intake baru</span>
            @endif
        </div>

        @if($kelasAktif->is_priority_intake)
            <div class="alert alert-warning">
                Rombel ini masih kosong dan diprioritaskan untuk intake siswa baru/pindahan.
            </div>
        @endif

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                @foreach($siswa as $sk)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $sk->siswa->nis ?? '-' }}</td>
                    <td>{{ $sk->siswa->nama_lengkap ?? '-' }}</td>
                    <td>{{ $sk->siswa->jenis_kelamin ?? '-' }}</td>
                    <td>{{ $sk->kelasAktif->kelas->nama_kelas ?? '-' }}</td>
                    <td>
                        <a href="{{ url('bukuinduk/'.$sk->siswa->id_siswa) }}"
                           class="btn btn-primary btn-sm">
                            Detail
                        </a>
                        <a href="{{ url('admin/export/buku-induk/'.$sk->siswa->id_siswa) }}"
                           class="btn btn-success btn-sm">
                            PDF Buku Induk
                        </a>
                    </td>
                </tr>
                @endforeach

            </tbody>

        </table>

        <a href="{{ url('/admin/siswa') }}"
           class="btn btn-secondary btn-sm mt-3">
            Kembali
        </a>

    </div>

</div>

@endsection
