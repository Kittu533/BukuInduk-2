@extends('layouts.sidebar-guru')

@section('content')

<h4>Riwayat Semester Siswa</h4>

<div class="card">
<div class="card-body">

<table class="table table-bordered">

<tr>
    <th>Nama Siswa</th>
    <td>{{ $siswa->nama_lengkap }}</td>
</tr>

<tr>
    <th>Mata Pelajaran</th>
    <td>{{ $jadwal->mapel->nama_mapel }}</td>
</tr>

</table>

<table class="table table-bordered table-striped">

    <thead>
        <tr>
            <th>No</th>
            <th>Kelas</th>
            <th>Semester</th>
            <th>Tahun</th>
            <th>T1</th>
            <th>T2</th>
            <th>T3</th>
            <th>T4</th>
            <th>T5</th>
            <th>UTS</th>
            <th>UAS</th>
            <th>Nilai Akhir</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>

    @forelse($riwayatNilai as $nilai)

        <tr>

            <td>{{ $loop->iteration }}</td>

            <td>
                {{ $nilai->jadwalMengajar->kelasAktif->kelas->nama_kelas ?? '-' }}
            </td>

            <td>
                {{ $nilai->jadwalMengajar->kelasAktif->semester->nama_semester ?? '-' }}
            </td>

            <td>
                {{ $nilai->jadwalMengajar->kelasAktif->tahunAjaran->tahun ?? '-' }}
            </td>

            <td>{{ $nilai->tugas1 ?? '-' }}</td>
            <td>{{ $nilai->tugas2 ?? '-' }}</td>
            <td>{{ $nilai->tugas3 ?? '-' }}</td>
            <td>{{ $nilai->tugas4 ?? '-' }}</td>
            <td>{{ $nilai->tugas5 ?? '-' }}</td>

            <td>{{ $nilai->uts ?? '-' }}</td>

            <td>{{ $nilai->uas ?? '-' }}</td>

            <td>
                <strong>
                    {{ $nilai->nilai_akhir ?? '-' }}
                </strong>
            </td>

            <td>

                @if(is_null($nilai->nilai_akhir))
                    <span class="badge bg-secondary">
                        Belum Lengkap
                    </span>

                @elseif($nilai->nilai_akhir < 75)
                    <span class="badge bg-danger">
                        Tidak Tuntas
                    </span>

                @else
                    <span class="badge bg-success">
                        Tuntas
                    </span>
                @endif

            </td>

        </tr>

    @empty

        <tr>
            <td colspan="13" class="text-center">
                Belum ada riwayat nilai.
            </td>
        </tr>

    @endforelse

    </tbody>

</table>

<a href="{{ url('guru/nilai/'.$jadwal->id_jadwal) }}"
   class="btn btn-secondary">
    Kembali
</a>

</div>
</div>

@endsection