@extends('layouts.sidebar-walas')

@section('content')

<h4 class="mb-3 fw-bold">
    KEHADIRAN
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
                    <th width="120" class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($siswa as $s)

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $s->nis }}</td>
                        <td>{{ $s->nama_lengkap }}</td>
                        <td>{{ $s->jenis_kelamin }}</td>

                        <td>
                            <a href="{{ url('wali/kehadiran/'.$s->id_siswa) }}"
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