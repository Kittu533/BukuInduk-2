@extends('layouts.sidebar-admin')

@section('content')

<style>
    .alumni-summary {
        border: 1px solid #dbe5f0;
        border-radius: 12px;
        background: #f8fbff;
        padding: 14px 18px;
    }

    .alumni-summary .label {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 4px;
    }

    .alumni-summary .value {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        line-height: 1;
    }

    .filter-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
    }

    .badge-lulus {
        background: #198754;
        color: #fff;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
    }

    .table thead th {
        vertical-align: middle;
        white-space: nowrap;
    }
</style>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">DATA ALUMNI</h2>
            <div class="text-muted">Filter berdasarkan angkatan dan tahun ajaran kelulusan.</div>
        </div>
        <div class="alumni-summary text-end">
            <div class="label">Total Alumni</div>
            <div class="value">{{ $alumni->total() }}</div>
        </div>
    </div>

    <div class="card filter-card mb-4">
        <div class="card-body">
            <form action="{{ url('admin/alumni') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label mb-1">Cari Alumni</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="ID siswa, NIS, NISN, atau nama alumni"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-3 col-lg-2">
                    <label class="form-label mb-1">Angkatan</label>
                    <select name="angkatan" class="form-select">
                        <option value="">Semua</option>
                        @foreach($angkatanOptions as $angkatan)
                            <option value="{{ $angkatan }}" {{ request('angkatan') == $angkatan ? 'selected' : '' }}>
                                {{ $angkatan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-lg-3">
                    <label class="form-label mb-1">Tahun Ajaran Lulus</label>
                    <select name="tahun_ajaran" class="form-select">
                        <option value="">Semua</option>
                        @foreach($tahunAjaranOptions as $tahunAjaran)
                            <option value="{{ $tahunAjaran->id_tahun }}" {{ request('tahun_ajaran') == $tahunAjaran->id_tahun ? 'selected' : '' }}>
                                {{ $tahunAjaran->tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5 col-lg-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Terapkan</button>
                        <a href="{{ url('admin/alumni') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card filter-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>ID Siswa</th>
                            <th>NIS</th>
                            <th>Nama Alumni</th>
                            <th>Angkatan</th>
                            <th>Tahun Ajaran Lulus</th>
                            <th>Jenis Kelamin</th>
                            <th>Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($alumni as $item)
                            <tr>
                                <td class="text-center">{{ $alumni->firstItem() + $loop->index }}</td>
                                <td>{{ $item->id_siswa }}</td>
                                <td>{{ $item->nis }}</td>
                                <td>{{ $item->nama_lengkap }}</td>
                                <td class="text-center">
                                    {{ $item->tahun_masuk ? \Carbon\Carbon::parse($item->tahun_masuk)->format('Y') : '-' }}
                                </td>
                                <td class="text-center">{{ $item->tahun_lulus_label ?? '-' }}</td>
                                <td class="text-center">{{ $item->jenis_kelamin }}</td>
                                <td class="text-center">
                                    <span class="badge-lulus">{{ strtolower($item->status_siswa) === 'lulus' ? 'Lulus' : $item->status_siswa }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.alumni.detail', $item->id_siswa) }}" class="btn btn-info btn-sm">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    Tidak ada data alumni untuk filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                <small class="text-muted">
                    @if($alumni->total() > 0)
                        Menampilkan {{ $alumni->firstItem() }} - {{ $alumni->lastItem() }} dari {{ $alumni->total() }} alumni
                    @else
                        Tidak ada data alumni yang ditampilkan
                    @endif
                </small>

                {{ $alumni->withQueryString()->links() }}
            </div>
        </div>
    </div>

</div>

@endsection
