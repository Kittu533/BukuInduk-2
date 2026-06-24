@extends('layouts.app-pembina')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">
                Nilai Ekstrakurikuler
            </h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="70">No</th>
                            <th>Ekstrakurikuler</th>
                            <th width="180">Aksi</th>
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

                                <a href="{{ url('pembina/nilai/'.$item->id_ekskul) }}"
                                   class="btn btn-primary btn-sm">

                                    Lihat Kelas

                                </a>

                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="3"
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