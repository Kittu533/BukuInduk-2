@extends('layouts.app-pembina')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">
                Riwayat Nilai Ekstrakurikuler
            </h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">

                        <tr>
                            <th width="70">No</th>
                            <th>Ekstrakurikuler</th>
                            <th width="150">Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($ekskul as $item)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $item->nama_ekskul }}</td>

                            <td>

                                <a href="{{ url('pembina/riwayat/'.$item->id_ekskul) }}"
                                class="btn btn-primary btn-sm">

                                    Lihat Riwayat

                                </a>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="3"
                                class="text-center text-muted">

                                Tidak ada data.

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