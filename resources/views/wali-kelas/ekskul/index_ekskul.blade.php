@extends('layouts.sidebar-walas')

@section('content')

<h4 class="mb-3 fw-bold">
    DATA SISWA - NILAI EKSTRAKURIKULER
</h4>

<div class="card shadow-sm border-0">

    <div class="card-body">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-light">
                <tr>
                    <th width="60" class="text-center">No</th>
                    <th class="text-center">NIS</th>
                    <th class="text-center">Nama</th>
                    <th width="180" class="text-center">Jenis Kelamin</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($siswa as $s)

                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $s->nis }}</td>
                        <td>{{ $s->nama_lengkap }}</td>
                        <td class="text-center">{{ $s->jenis_kelamin }}</td>

                        <td class="text-center">

                            <a href="{{ url('wali/ekskul/'.$s->id_siswa) }}"
                            class="btn btn-primary btn-sm"> 
                                Detail
                            </a>

                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Tidak ada siswa
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection