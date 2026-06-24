@extends('layouts.sidebar-walas')

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
    <form method="GET" class="d-inline-flex gap-2 align-items-center">
        <label class="small text-muted">Riwayat</label>
        <select name="kelas_aktif" class="form-select form-select-sm" onchange="this.form.submit()">
            @foreach($riwayat as $item)
                <option value="{{ $item->id_kelas_aktif }}" {{ request('kelas_aktif', $kelas_walas?->id_kelas_aktif) == $item->id_kelas_aktif ? 'selected' : '' }}>
                    {{ $item->kelasAktif->kelas->nama_kelas ?? '-' }} - {{ $item->kelasAktif->semester->nama_semester ?? '-' }} / {{ $item->kelasAktif->tahunAjaran->tahun ?? '-' }}
                </option>
            @endforeach
        </select>
    </form>
    @endif
</div>

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
            {{ $kelas_walas->tahun }}
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
            {{ $kelas_walas->nama_semester }}
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

    <a href="{{ url('wali/nilai') }}"
       class="btn btn-secondary btn-sm">

        Kembali

    </a>

</div>

@endsection
