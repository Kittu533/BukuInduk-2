@extends('layouts.app-pembina')

@section('content')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    {{-- INFO KELAS --}}
    <div class="card shadow-sm mb-3">

        <div class="card-header">

            <h5 class="mb-0">
                Nilai Ekskul
                <span class="text-primary">
                    {{ $ekskul->nama_ekskul }}
                </span>
            </h5>

        </div>

        <div class="card-body p-0">

            <table class="table table-bordered mb-0">

                <tr>

                    <th width="180">
                        Kelas
                    </th>

                    <td>
                        {{ $kelas->nama_kelas ?? '-' }}
                    </td>

                    <th width="180">
                        Tahun Ajaran
                    </th>

                    <td>
                        {{ $kelas->tahun ?? '-' }}
                    </td>

                </tr>

                <tr>

                    <th>
                        Semester
                    </th>

                    <td>
                        {{ $kelas->nama_semester ?? '-' }}
                    </td>

                    <th>
                        Status
                    </th>

                    <td>

                        @if($bolehEdit)

                            <span class="badge bg-success">
                                Dapat Diedit
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                Ditutup
                            </span>

                        @endif

                    </td>

                </tr>

            </table>

        </div>

    </div>

    {{-- ALERT --}}
    @unless($bolehEdit)

        <div class="alert alert-warning">

            Semester ini sudah ditutup.
            Nilai ekstrakurikuler masih dapat dilihat,
            tetapi tidak dapat diubah.

        </div>

    @endunless

    {{-- TABEL --}}
    <div class="card shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="table-light">

                        <tr>

                            <th width="60" class="text-center">
                                No
                            </th>

                            <th width="120">
                                ID Siswa
                            </th>

                            <th>
                                Nama Siswa
                            </th>

                            <th width="120" class="text-center">
                                Nilai Saat Ini
                            </th>

                            <th width="250" class="text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($siswa as $s)

                        <tr>

                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $s->id_siswa }}
                            </td>

                            <td>
                                {{ $s->nama_lengkap }}
                            </td>

                            <td class="text-center">

                                @if($s->nilai == 'A')

                                    <span class="badge bg-success">
                                        A
                                    </span>

                                @elseif($s->nilai == 'B')

                                    <span class="badge bg-primary">
                                        B
                                    </span>

                                @else

                                    <span class="badge bg-warning text-dark">
                                        C
                                    </span>

                                @endif

                            </td>

                            <td class="text-center">

                                @if($s->sudah_lulus)

                                    <span class="badge bg-secondary">
                                        Lulus
                                    </span>

                                @elseif(!$bolehEdit)

                                    <span class="badge bg-warning text-dark">
                                        Ditutup
                                    </span>

                                @else

                                    <form method="POST"
                                          action="{{ url('pembina/nilai/update/'.$s->id_nilai_ekskul) }}"
                                          class="d-flex justify-content-center gap-2">

                                        @csrf

                                        <select name="nilai"
                                                class="form-select form-select-sm"
                                                style="width:90px">

                                            <option value="A"
                                                {{ $s->nilai == 'A' ? 'selected' : '' }}>
                                                A
                                            </option>

                                            <option value="B"
                                                {{ $s->nilai == 'B' ? 'selected' : '' }}>
                                                B
                                            </option>

                                            <option value="C"
                                                {{ $s->nilai == 'C' ? 'selected' : '' }}>
                                                C
                                            </option>

                                        </select>

                                        <button type="submit"
                                                class="btn btn-success btn-sm">

                                            Simpan

                                        </button>

                                    </form>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="5"
                                class="text-center text-muted">

                                Tidak ada data siswa.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

                <a href="{{ url('pembina/nilai') }}"
                class="btn btn-secondary mt-2">

                    Kembali

                </a>

        </div>

    </div>

</div>

@endsection