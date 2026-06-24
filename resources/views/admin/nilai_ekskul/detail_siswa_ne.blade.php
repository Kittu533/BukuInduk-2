@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/nilai-ekskul-detail.css') }}">
@endsection

@section('content')

<h3 class="page-title">
    DETAIL NILAI EKSTRAKURIKULER
</h3>

@if(!$canEdit && $editLockReason)
<div class="alert alert-warning">
    {{ $editLockReason }}
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">

    @if($canEdit)
    <a href="{{ url('admin/nilai_ekskul/create/'.$siswa->id_siswa) }}"
       class="btn btn-success btn-sm">
        + Tambah
    </a>
    @endif

</div>

<div class="card table-card">

    <div class="card-body">

        {{-- INFO SISWA --}}

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
                <td>: {{ $siswa->nama_kelas }}</td>
            </tr>

            <tr>
                <td><b>Tahun Ajaran</b></td>
                <td>: {{ $siswa->tahun }}</td>
            </tr>

            <tr>
                <td><b>Semester</b></td>
                <td>: {{ $siswa->nama_semester }}</td>
            </tr>

        </table>

        {{-- TABEL NILAI --}}

        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th width="60" class="text-center">No</th>
                    <th class="text-center">Ekstrakurikuler</th>
                    <th width="120" class="text-center">Nilai</th>
                    <th width="140" class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($data as $d)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $d->ekstrakurikuler->nama_ekskul ?? '-' }}
                    </td>

                    <td class="text-center">

                        @if($d->nilai == 'A')
                            <span class="badge bg-success">A</span>
                        @elseif($d->nilai == 'B')
                            <span class="badge bg-primary">B</span>
                        @else
                            <span class="badge bg-danger">C</span>
                        @endif

                    </td>

                    <td class="text-center">

                        @if($canEdit)

                            <a href="{{ url('admin/nilai_ekskul/edit_siswa/'.$d->id_siswa) }}"
                               class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <form action="{{ url('admin/nilai_ekskul/delete/'.$d->id_nilai_ekskul) }}"
                                  method="POST"
                                  class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    Hapus
                                </button>

                            </form>

                        @else

                            <span class="text-muted">
                                Read Only
                            </span>

                        @endif

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="4" class="text-center">
                        Belum ada data nilai ekstrakurikuler.
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

        <div class="mt-3">

                <a href="{{ url('admin/nilai_ekskul') }}"
                   class="btn btn-secondary btn-sm">
                    Kembali
                </a>

        </div>

    </div>

</div>

@endsection