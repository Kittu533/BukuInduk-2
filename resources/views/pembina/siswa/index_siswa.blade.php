@extends('layouts.app-pembina')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">
                Data Siswa Ekstrakurikuler
            </h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="70">No</th>
                            <th>Ekstrakurikuler</th>
                            <th width="180">Jumlah Anggota</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($ekskul as $item)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $item->nama_ekskul }}
                            </td>

                            <td>
                                <span class="badge bg-success">
                                    {{ $item->jumlah_anggota }} Siswa
                                </span>
                            </td>

                            <td>

                                <a href="{{ url('pembina/siswa/'.$item->id_ekskul) }}"
                                   class="btn btn-sm btn-primary">

                                    Detail

                                </a>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="4"
                                class="text-center text-muted">

                                Belum ada data ekstrakurikuler.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection