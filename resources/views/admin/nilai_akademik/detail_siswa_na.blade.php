@extends('layouts.sidebar-admin')

@section('css')

<link rel="stylesheet"
      href="{{ asset('css/detail-nilai.css') }}">

@endsection

@section('content')

<h3 class="text-center mb-4 nilai-title">
    HASIL BELAJAR SISWA
</h3>

<div class="mb-3 text-end">
    @if($riwayat->count() > 1)
    <form method="GET" class="d-inline-flex gap-2 align-items-center me-2">
        <label class="small text-muted">Riwayat</label>
        <select name="kelas_aktif" class="form-select form-select-sm" onchange="this.form.submit()">
            @foreach($riwayat as $item)
                <option value="{{ $item->id_kelas_aktif }}" {{ request('kelas_aktif', $kelas?->id_kelas_aktif) == $item->id_kelas_aktif ? 'selected' : '' }}>
                    {{ $item->kelasAktif->kelas->nama_kelas ?? '-' }} - {{ $item->kelasAktif->semester->nama_semester ?? '-' }} / {{ $item->kelasAktif->tahunAjaran->tahun ?? '-' }}
                </option>
            @endforeach
        </select>
    </form>
    @endif

    @if($canEdit)
    <a href="{{ url('admin/nilai_akademik/'.$siswa->id_siswa.'/edit?kelas_aktif='.$kelas->id_kelas_aktif) }}"
    class="btn btn-warning btn-edit">

        Edit Nilai

    </a>
    @else
    <span class="badge bg-light text-dark border px-3 py-2">
        Nilai read-only
    </span>
    @endif

</div>

@if(!$canEdit)
<div class="alert alert-warning">
    {{ $editLockReason ?? 'Nilai semester ini hanya bisa dilihat dan tidak bisa diedit dari UI normal.' }}
</div>
@endif

<table class="table table-bordered">

    <tr>

        <td width="150" class="judul">
            Nama
        </td>

        <td>
            {{ $siswa->nama_lengkap }}
        </td>

        <td width="150" class="right">
            <b>Tahun Ajaran</b>
        </td>

        <td>
            {{ $kelas->tahun }}
        </td>

    </tr>

    <tr>

        <td class="judul">
            NIS
        </td>

        <td>
            {{ $siswa->nis }}
        </td>

        <td class="right">
            <b>Semester</b>
        </td>

        <td>
            {{ $kelas->nama_semester }}
        </td>

    </tr>

</table>

<table class="table table-bordered">

    <thead class="table-light center">

        <tr>

            <th width="50">
                No
            </th>

            <th>
                Kode
            </th>

            <th>
                Mata Pelajaran
            </th>

            <th width="60">
                T1
            </th>

            <th width="60">
                T2
            </th>

            <th width="60">
                T3
            </th>

            <th width="60">
                T4
            </th>

            <th width="60">
                T5
            </th>

            <th width="80">
                UTS
            </th>

            <th width="80">
                UAS
            </th>

            <th width="100">
                Nilai Akhir
            </th>

        </tr>

    </thead>

    <tbody>

        {{-- ========================================= --}}
        {{-- KELOMPOK UMUM --}}
        {{-- ========================================= --}}

        <tr class="table-light fw-bold">

            <td colspan="11" class="text-center">

                Kelompok Umum

            </td>

        </tr>

        @foreach($nilai_wajib as $n)

        <tr>

            <td class="center">
                {{ $loop->iteration }}
            </td>

            <td class="center">
                {{ $n->kode_mapel_semester }}
            </td>

            <td class="mapel">
                {{ $n->nama_mapel }}
            </td>

            <td class="center">
                {{ $n->tugas1 }}
            </td>

            <td class="center">
                {{ $n->tugas2 }}
            </td>

            <td class="center">
                {{ $n->tugas3 }}
            </td>

            <td class="center">
                {{ $n->tugas4 }}
            </td>

            <td class="center">
                {{ $n->tugas5 }}
            </td>

            <td class="center">
                {{ $n->uts }}
            </td>

            <td class="center">
                {{ $n->uas }}
            </td>

            <td class="center fw-bold">
                {{ $n->nilai_akhir }}
            </td>

        </tr>

        @endforeach

        {{-- ========================================= --}}
        {{-- KELOMPOK PILIHAN --}}
        {{-- ========================================= --}}

        @if($nilai_pilihan->count() > 0)

        <tr class="table-light fw-bold">

            <td colspan="11" class="text-center">

                Kelompok Pilihan

            </td>

        </tr>

        @foreach($nilai_pilihan as $n)

        <tr>

            <td class="center">

                {{ $loop->iteration }}

            </td>

            <td class="center">
                {{ $n->kode_mapel_semester }}
            </td>

            <td class="mapel">

                {{ $n->nama_mapel }}

            </td>

            <td class="center">
                {{ $n->tugas1 }}
            </td>

            <td class="center">
                {{ $n->tugas2 }}
            </td>

            <td class="center">
                {{ $n->tugas3 }}
            </td>

            <td class="center">
                {{ $n->tugas4 }}
            </td>

            <td class="center">
                {{ $n->tugas5 }}
            </td>

            <td class="center">
                {{ $n->uts }}
            </td>

            <td class="center">
                {{ $n->uas }}
            </td>

            <td class="center fw-bold">
                {{ $n->nilai_akhir }}
            </td>

        </tr>

        @endforeach

        @endif

    </tbody>

    <tfoot>

        <tr class="table-light fw-bold">

            <td colspan="10" class="text-end">

                Rata-rata Keseluruhan

            </td>

            <td class="center">

                {{

                    round(

                        $nilai_wajib
                        ->concat($nilai_pilihan)
                        ->avg('nilai_akhir'),

                        2

                    )

                }}

            </td>

        </tr>

    </tfoot>

</table>

<div class="mt-3">

    @if(str_starts_with(strtoupper($kelas->nama_kelas ?? ''), 'XII'))

        <a href="{{ route('export.rekap.hasil.belajar', $siswa->id_siswa) }}"
           class="btn btn-danger btn-sm">
            Download Rapor (3 Tahun)
        </a>

    @endif

    <a href="{{ url('admin/nilai_akademik/'.$siswa->id_siswa.'/edit') }}"
       class="btn btn-warning btn-sm">
        <i class="bi bi-pencil-square"></i> Edit Nilai
    </a>

    <a href="{{ url('admin/nilai_akademik') }}"
       class="btn btn-secondary btn-sm">
        Kembali
    </a>

</div>

@endsection
