@extends('layouts.sidebar-guru')

@section('content')

<h3 class="page-title">INPUT NILAI</h3>

<div class="card table-card">

    <div class="card-body">

        <table class="table table-bordered table-striped table-hover">

            <thead>
                <tr>
                    <th width="60" class="text-center">No</th>
                    <th class="text-center">Kelas</th>
                    <th class="text-center">Mata Pelajaran</th>
                    <th width="120" class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($jadwal as $j)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center fw-semibold">{{ $j->kelasAktif->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $j->mapel->nama_mapel ?? '-' }}</td>
                    <td class="text-center">
                        <a href="{{ route('guru.nilai.siswa', $j->id_jadwal) }}"
                           class="btn btn-primary btn-sm">
                            Lihat
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
