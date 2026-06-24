@extends('layouts.ortu-app')

@section('content')

<div class="card border-0 shadow-sm p-4">

    <h3 class="fw-bold mb-4">
        KEHADIRAN SISWA
    </h3>

    {{-- INFO --}}
    <table class="table table-bordered mb-4">

        <tr>
            <td width="200">
                <b>Nama Siswa</b>
            </td>

            <td>
                {{ $siswa->nama_lengkap }}
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

    {{-- TABEL ABSENSI --}}
    <table class="table table-bordered">

        <thead>

            <tr class="text-center">

                <th width="60">No</th>
                <th width="180">Tanggal</th>
                <th>Mata Pelajaran</th>
                <th>Guru</th>
                <th width="120">Status</th>

            </tr>

        </thead>

        <tbody>

            @forelse($absensi as $a)

            <tr>

                <td class="text-center">
                    {{ $loop->iteration }}
                </td>

                <td>
                    {{ $a->tanggal }}
                </td>

                <td>
                    {{ $a->nama_mapel }}
                </td>

                <td>
                    {{ $a->nama_guru }}
                </td>

                <td class="text-center">

                    @if($a->status == 'Sakit')

                        <span class="badge bg-warning text-dark">
                            Sakit
                        </span>

                    @elseif($a->status == 'Izin')

                        <span class="badge bg-info">
                            Izin
                        </span>

                    @else

                        <span class="badge bg-danger">
                            Alpa
                        </span>

                    @endif

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="5"
                    class="text-center text-muted">

                    Tidak ada data absensi

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection