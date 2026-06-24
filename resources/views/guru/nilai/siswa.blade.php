@extends('layouts.sidebar-guru')

@section('css')
<link rel="stylesheet" href="{{ asset('css/guru/nilai.css') }}">
@endsection

@section('content')

<h4 class="fw-bold mb-3">Daftar Siswa</h4>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle-fill me-2"></i>
    {{ session('success') }}
    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    {{ session('error') }}
    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
    </button>
</div>
@endif

<style>
.history-chip{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:6px 10px;
    border-radius:999px;
    background:#eef2ff;
    color:#3445a4;
    font-size:13px;
    font-weight:700;
}

.history-muted{
    color:#64748b;
    font-size:13px;
    line-height:1.4;
}

.history-list{
    margin:6px 0 0;
    padding-left:18px;
    color:#475569;
    font-size:13px;
}
</style>

{{-- INFO KELAS --}}
<div class="card mb-4">

    <div class="card-body p-0">

        <table class="table table-bordered mb-0 info-table">

            <tr>

                <th width="180">Kelas</th>
                <td>{{ $detail->nama_kelas }}</td>

                <th width="180">Mata Pelajaran</th>
                <td>
                    {{ $detail->nama_mapel }}

                    <span class="text-muted">
                        ({{ $detail->kode_mapel_semester }})
                    </span>
                </td>

            </tr>

            <tr>

                <th>Tahun Ajaran</th>
                <td>{{ $detail->tahun ?? '-' }}</td>

                <th>Semester</th>
                <td>{{ $detail->nama_semester ?? '-' }}</td>

            </tr>

        </table>

    </div>

</div>

@if(str_starts_with(strtoupper($detail->nama_kelas), 'XII'))

<div class="alert alert-warning">

    Kelas XII sudah lulus.
    Nilai masih dapat dilihat tetapi tidak dapat diubah.

</div>

@endif

{{-- TABEL SISWA --}}
<div class="card table-card">

    <div class="card-body">

        <table class="table table-bordered table-striped table-hover">

            <thead>

                <tr>

                    <th width="60" class="text-center">
                        No
                    </th>

                    <th>
                        Nama Siswa
                    </th>

                    <th width="300">
                        Riwayat Kelas
                    </th>

                    <th width="150" class="text-center">
                        Status
                    </th>

                    <th width="220" class="text-center">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($siswa as $s)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $s->nama_lengkap }}
                    </td>

                    <td>

                        <a href="{{ url('guru/nilai/riwayat/'.$id.'/'.$s->id_siswa) }}"
                           class="badge bg-primary-subtle text-primary text-decoration-none">

                            {{ $s->riwayat_count }} riwayat

                        </a>

                        @if($s->riwayat_sebelumnya->isNotEmpty())

                            <ul class="history-list">

                                @foreach($s->riwayat_sebelumnya->take(2) as $item)

                                <li>

                                    {{ $item->kelasAktif->kelas->nama_kelas ?? '-' }}
                                    -
                                    {{ $item->kelasAktif->semester->nama_semester ?? '-' }}
                                    /
                                    {{ $item->kelasAktif->tahunAjaran->tahun ?? '-' }}

                                </li>

                                @endforeach

                            </ul>

                            @if($s->riwayat_sebelumnya->count() > 2)

                            <div class="history-muted">

                                +{{ $s->riwayat_sebelumnya->count() - 2 }}
                                riwayat lain, lihat Detail

                            </div>

                            @endif

                        @else

                            <div class="history-muted">

                                Belum ada riwayat kelas sebelumnya

                            </div>

                        @endif

                    </td>

                    <td class="text-center">

                        @if(!is_null($s->nilai_akhir))

                            @if($s->nilai_akhir < 75)

                                <span class="badge bg-danger">

                                    Tidak Tuntas

                                </span>

                            @else

                                <span class="badge bg-success">

                                    Tuntas

                                </span>

                            @endif

                        @else

                            <span class="badge bg-secondary">

                                Belum Dinilai

                            </span>

                        @endif

                    </td>

                    <td class="text-center">

                        <a href="{{ url('guru/nilai/'.$id.'/'.$s->id_siswa.'/detail') }}"
                           class="btn btn-info btn-sm">

                            Detail

                        </a>

                        @if(str_starts_with(strtoupper($detail->nama_kelas), 'XII'))

                            <span class="badge bg-secondary">

                                Lulus

                            </span>

                        @else

                            <a href="{{ url('guru/nilai/'.$id.'/'.$s->id_siswa.'/input') }}"
                               class="btn btn-success btn-sm">

                                Input

                            </a>

                        @endif

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="5"
                        class="text-center text-muted">

                        Tidak ada data siswa

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

        <a href="{{ route('guru.nilai.index') }}"
           class="btn btn-secondary mt-3">

            Kembali

        </a>

    </div>

</div>

@endsection