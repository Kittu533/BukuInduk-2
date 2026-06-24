@extends('layouts.ortu-app')

@section('content')

<div class="card border-0 shadow-sm p-4">

    <h3 class="fw-bold mb-4">
        JADWAL PELAJARAN
    </h3>

    {{-- INFO SISWA --}}
    <table class="table table-bordered mb-4">

        <tr>

            <td width="220">
                <b>Nama Siswa</b>
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
                {{ $kelasAktif->nama_kelas }}
            </td>

        </tr>

        <tr>

            <td>
                <b>Tahun Ajaran</b>
            </td>

            <td>
                {{ $kelasAktif->tahun }}
            </td>

        </tr>

        <tr>

            <td>
                <b>Semester</b>
            </td>

            <td>
                {{ $kelasAktif->nama_semester }}
            </td>

        </tr>

    </table>

    {{-- TABEL JADWAL --}}
    <table class="table table-bordered">

        <thead>

            <tr class="text-center">

                <th width="60">
                    No
                </th>

                <th>
                    Mata Pelajaran
                </th>

                <th width="300">
                    Guru
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($jadwal as $j)

            <tr>

                <td class="text-center">
                    {{ $loop->iteration }}
                </td>

                <td>
                    {{ $j->nama_mapel }}
                </td>

                <td>
                    {{ $j->nama_guru }}
                </td>

            </tr>

            @empty

            <tr>

                <td colspan="3"
                    class="text-center text-muted">

                    Jadwal belum tersedia

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection