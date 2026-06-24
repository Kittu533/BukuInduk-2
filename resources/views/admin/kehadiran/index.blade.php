@extends('layouts.sidebar-admin')

@section('content')

<h3 class="page-title">
    DATA KEHADIRAN
</h3>

<div class="card table-card">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <strong>Riwayat Kehadiran</strong>
            </div>

            <form method="GET"
                  action="{{ url('admin/kehadiran') }}">

                <div class="d-flex">

                    <select name="id_tahun"
                            class="form-control me-2"
                            style="width:140px">

                        @foreach($tahunAjaran as $ta)

                            <option
                                value="{{ $ta->id_tahun }}"
                                {{ $idTahun == $ta->id_tahun ? 'selected' : '' }}>

                                {{ $ta->tahun }}

                            </option>

                        @endforeach

                    </select>

                    <select name="semester"
                            class="form-control me-2"
                            style="width:130px">

                        @foreach($semester as $s)

                            <option
                                value="{{ $s->nama_semester }}"
                                {{ $namaSemester == $s->nama_semester ? 'selected' : '' }}>

                                {{ $s->nama_semester }}

                            </option>

                        @endforeach

                    </select>

                    <input type="text"
                           name="search"
                           class="form-control me-2"
                           style="width:250px"
                           placeholder="Cari kelas atau wali kelas..."
                           value="{{ request('search') }}">

                    <button type="submit"
                            class="btn btn-primary me-2">

                        Filter

                    </button>

                    <a href="{{ url('admin/kehadiran') }}"
                       class="btn btn-secondary">

                        Reset

                    </a>

                </div>

            </form>

        </div>

        <table class="table table-bordered table-striped">

            <thead>

                <tr>

                    <th class="text-center" width="60">
                        No
                    </th>

                    <th class="text-center">
                        Kelas
                    </th>

                    <th class="text-center">
                        Wali Kelas
                    </th>

                    <th class="text-center">
                        Tahun Ajaran
                    </th>

                    <th class="text-center">
                        Semester
                    </th>

                    <th class="text-center">
                        Jumlah Siswa
                    </th>

                    <th class="text-center" width="120">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($kelas as $k)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $k->kelas->nama_kelas ?? '-' }}
                    </td>

                    <td>
                        {{ $k->guru->nama_guru ?? '-' }}
                    </td>

                    <td>
                        {{ $k->tahunAjaran->tahun ?? '-' }}
                    </td>

                    <td>
                        {{ $k->semester->nama_semester ?? '-' }}
                    </td>

                    <td class="text-center">
                        {{ $k->siswaKelas->count() }}
                    </td>

                    <td class="text-center">

                        <a href="{{ url('admin/kehadiran/'.$k->id_kelas_aktif) }}"
                           class="btn btn-info btn-sm">

                            Detail

                        </a>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="8"
                        class="text-center text-muted">

                        Tidak ada data ditemukan

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
