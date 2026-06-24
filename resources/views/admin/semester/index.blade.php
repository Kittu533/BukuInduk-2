@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/semester.css') }}">
@endsection

@section('content')

<h3 class="page-title">DATA SEMESTER</h3>

@if(session('success'))
<script>
alert("{{ session('success') }}");
</script>
@endif

@if(session('error'))
<script>
alert("{{ session('error') }}");
</script>
@endif

<div class="card table-card">
    <div class="card-body">

        <div class="alert alert-light border d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
            <div>
                <div class="fw-semibold">Konteks Akademik Aktif</div>
                <small class="text-muted">
                    Tahun ajaran aktif:
                    <strong>{{ $tahunAktif?->tahun ?? '-' }}</strong>
                    |
                    Semester aktif:
                    <strong>{{ $semesterAktif?->nama_semester ?? '-' }}</strong>
                </small>
            </div>

            <div class="d-flex gap-2">
                <form action="{{ url('admin/semester/proses-baru') }}" method="POST" onsubmit="return confirm('Proses semester baru akan memindahkan siswa aktif dari semester Ganjil ke Genap pada tahun ajaran yang sama. Lanjutkan?')">
                    @csrf
                    <button class="btn btn-primary btn-sm" {{ $canProcessSemesterBaru ? '' : 'disabled' }}>
                        Proses Semester Baru
                    </button>
                </form>
            </div>
        </div>

        <div class="alert alert-info mb-3">
            Data semester bersifat <strong>tetap</strong>: hanya <strong>Ganjil</strong> dan <strong>Genap</strong>. Halaman ini dipakai untuk mengganti semester aktif pada <strong>tahun ajaran berjalan</strong>, bukan menambah master semester baru.
        </div>

        @if(! $tahunAktif)
            <div class="alert alert-danger mb-3">
                Tahun ajaran aktif belum ada. Aktifkan dulu tahun ajaran yang sedang berjalan sebelum memproses semester baru.
            </div>
        @elseif(! $semesterAktif)
            <div class="alert alert-danger mb-3">
                Semester aktif belum ada. Aktifkan semester <strong>Ganjil</strong> untuk mulai flow akademik.
            </div>
        @elseif(strtolower((string) $semesterAktif->nama_semester) !== 'ganjil')
            <div class="alert alert-warning mb-3">
                Semester aktif saat ini <strong>{{ $semesterAktif->nama_semester }}</strong>. Tombol <strong>Proses Semester Baru</strong> hanya dipakai saat semester aktif masih <strong>Ganjil</strong>.
            </div>
        @else
            <div class="alert alert-success mb-3">
                Semester aktif saat ini <strong>Ganjil</strong>. Setelah kegiatan semester ganjil selesai, klik <strong>Proses Semester Baru</strong> untuk memindahkan siswa ke semester <strong>Genap</strong> pada tahun ajaran yang sama.
            </div>
        @endif

        <div class="alert alert-secondary mb-3">
            <strong>Urutan pakai:</strong>
            <ol class="mb-0 mt-2">
                <li>Aktifkan tahun ajaran yang sedang berjalan.</li>
                <li>Pastikan semester aktif masih <strong>Ganjil</strong>, lalu proses ke <strong>Genap</strong> di halaman ini.</li>
                <li>Setelah semester akhir selesai, buka menu <strong>Kenaikan/Kelulusan</strong> untuk proses pindah tahun ajaran.</li>
            </ol>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">ID Semester</th>
                    <th class="text-center">Semester</th>
                    <th class="text-center">Tahun Ajaran Terkait</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach($semester as $s)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $s->id_semester }}</td>
                    <td>{{ $s->nama_semester }}</td>
                    <td>{{ $s->tahun_ajaran_terkait }}</td>
                    <td class="text-center">
                        @if($s->status_label === 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection
