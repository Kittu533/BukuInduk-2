@extends('layouts.sidebar-admin')

@section('content')

@php
    $canEdit = $canEdit ?? false;
    $nilaiFields = [
        'tugas1' => 'T1',
        'tugas2' => 'T2',
        'tugas3' => 'T3',
        'tugas4' => 'T4',
        'tugas5' => 'T5',
        'uts' => 'UTS',
        'uas' => 'UAS',
    ];
    $deadline = $kelas->batas_edit_nilai
        ? \Carbon\Carbon::parse($kelas->batas_edit_nilai)->format('d/m/Y H:i')
        : 'Belum diatur';
@endphp

<style>
    .nilai-edit-shell {
        max-width: 100%;
    }

    .nilai-status-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
    }

    .nilai-status-pill {
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        padding: 8px 14px;
    }

    .nilai-input-table {
        min-width: 1180px;
    }

    .nilai-input-table th {
        white-space: nowrap;
    }

    .nilai-code-cell {
        min-width: 120px;
        font-weight: 700;
        color: #334155;
    }

    .nilai-mapel-cell {
        min-width: 300px;
        color: #1f2937;
    }

    .nilai-number-input {
        width: 86px;
        min-width: 86px;
        height: 44px;
        margin: 0 auto;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.2;
        padding: 8px 10px;
        text-align: center;
        font-variant-numeric: tabular-nums;
    }

    .nilai-number-input:disabled {
        background: #f1f5f9;
        color: #64748b;
        opacity: 1;
    }

    .nilai-number-input::-webkit-outer-spin-button,
    .nilai-number-input::-webkit-inner-spin-button {
        margin: 0;
        -webkit-appearance: none;
    }

    .nilai-number-input[type=number] {
        -moz-appearance: textfield;
    }

    .nilai-help-text {
        color: #64748b;
        font-size: 13px;
    }
</style>

<h3 class="mb-4">
    EDIT NILAI SISWA
</h3>

<div class="nilai-edit-shell">

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    Periksa lagi input nilai. Nilai harus angka 0 sampai 100 atau dikosongkan.
</div>
@endif

<div class="card nilai-status-card mb-4">

    <div class="card-body">

        <div class="d-flex flex-wrap justify-content-between gap-3 mb-3">
            <div>
                <h5 class="mb-1">
                    Status Edit Nilai
                </h5>
                <div class="nilai-help-text">
                    Edit hanya dibuka untuk semester aktif dan selama belum melewati batas edit.
                </div>
            </div>

            @if($canEdit)
                <span class="nilai-status-pill bg-success-subtle text-success">
                    Bisa diedit
                </span>
            @else
                <span class="nilai-status-pill bg-secondary-subtle text-secondary">
                    Read-only
                </span>
            @endif
        </div>

        @if(!$canEdit)
            <div class="alert alert-warning mb-4">
                {{ $editLockReason ?? 'Nilai semester ini sedang dikunci.' }}
            </div>
        @endif

        <table class="table table-borderless mb-0">

            <tr>

                <td width="180">
                    <b>Nama Siswa</b>
                </td>

                <td width="10">
                    :
                </td>

                <td>
                    {{ $siswa->nama_lengkap }}
                </td>

            </tr>

            <tr>
                <td>
                    <b>Kelas</b>
                </td>

                <td>
                    :
                </td>

                <td>
                    {{ $kelas->nama_kelas }} — {{ $kelas->nama_semester }} / {{ $kelas->tahun }}
                </td>

            </tr>

            <tr>

                <td>
                    <b>Status Semester</b>
                </td>

                <td>
                    :
                </td>

                <td>
                    {{ ucfirst($kelas->status_semester ?? '-') }}
                </td>

            </tr>

            <tr>

                <td>
                    <b>Batas Edit</b>
                </td>

                <td>
                    :
                </td>

                <td>
                    {{ $deadline }}
                </td>

            </tr>

            <tr>

                <td>
                    <b>NIS</b>
                </td>

                <td>
                    :
                </td>

                <td>
                    {{ $siswa->nis }}
                </td>

            </tr>

        </table>

    </div>

</div>

<form action="{{ url('admin/nilai_akademik/'.$siswa->id_siswa) }}"
      method="POST">

    @csrf
    @method('PUT')

    <input type="hidden" name="kelas_aktif" value="{{ $kelas->id_kelas_aktif }}">

    <div class="card nilai-status-card">

        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h5 class="mb-1">
                        Input Komponen Nilai
                    </h5>
                    <div class="nilai-help-text">
                        Gunakan angka 0-100. Kolom boleh dikosongkan jika nilai belum tersedia.
                    </div>
                </div>

                @if(!$canEdit)
                    <span class="badge bg-light text-dark border">
                        Form terkunci
                    </span>
                @endif
            </div>

            <div class="table-responsive">

            <table class="table table-bordered align-middle nilai-input-table">

                <thead class="table-light text-center">

                    <tr>

                        <th width="300">
                            Kode
                        </th>

                        <th width="300">
                            Mata Pelajaran
                        </th>

                        @foreach($nilaiFields as $label)
                            <th width="96">
                                {{ $label }}
                            </th>
                        @endforeach

                    </tr>

                </thead>

                <tbody>

                    @forelse($nilai as $n)

                    @php
                        $rowIndex = $loop->index;
                    @endphp

                    <tr>

                        <td class="nilai-code-cell">
                            {{ $n->kode_mapel_semester }}
                        </td>

                        <td class="nilai-mapel-cell">

                            {{ $n->nama_mapel }}

                            <input type="hidden"
                                   name="id_nilai[]"
                                   value="{{ $n->id_nilai }}">

                        </td>

                        @foreach($nilaiFields as $field => $label)
                            <td class="text-center">
                                <input type="number"
                                       name="{{ $field }}[]"
                                       value="{{ old($field.'.'.$rowIndex, $n->{$field}) }}"
                                       class="form-control nilai-number-input @error($field.'.'.$rowIndex) is-invalid @enderror"
                                       min="0"
                                       max="100"
                                       step="1"
                                       inputmode="numeric"
                                       placeholder="-"
                                       aria-label="{{ $label }} {{ $n->nama_mapel }}"
                                       @disabled(!$canEdit)>
                            </td>
                        @endforeach

                    </tr>

                    @empty

                    <tr>
                        <td colspan="{{ count($nilaiFields) + 2 }}" class="text-center text-muted py-4">
                            Belum ada draft nilai untuk siswa dan kelas aktif ini.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

            </div>

            <div class="mt-3 text-end">

                <a href="{{ url('admin/nilai_akademik/detail/'.$siswa->id_siswa) }}"
                   onclick="event.preventDefault(); window.location.href='{{ url('admin/nilai_akademik/detail/'.$siswa->id_siswa.'?kelas_aktif='.$kelas->id_kelas_aktif) }}';"
                   class="btn btn-secondary">

                    Kembali

                </a>

                @if($canEdit)
                <button type="submit" class="btn btn-success">

                    Simpan

                </button>
                @else
                <button type="button" class="btn btn-outline-secondary" disabled>

                    Nilai Dikunci

                </button>
                @endif

            </div>

        </div>

    </div>

</form>

</div>

@endsection
