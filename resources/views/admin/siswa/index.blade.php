@extends('layouts.sidebar-admin')

@section('content')

<h3 class="page-title">
    DATA SISWA
</h3>

<div class="card table-card">

    <div class="card-body">

        <div class="alert alert-light border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div>
                <div class="fw-semibold">Tampilan Data Aktif</div>
                <small class="text-muted">
                    Tahun ajaran aktif: <strong>{{ $tahun->tahun }}</strong> |
                    Semester aktif: <strong>{{ $semester->nama_semester }}</strong>
                </small>
            </div>
            <small class="text-muted">
                Kelas kosong ditampilkan di bawah. Kelas X tahun ajaran baru memang disiapkan untuk siswa baru.
            </small>
        </div>

        @if($priorityIntakeClasses->isNotEmpty())
            <div class="alert alert-warning mb-3">
                Prioritas intake baru saat ini:
                <strong>{{ $priorityIntakeClasses->join(', ') }}</strong>.
                Isi rombel kelas X yang kosong ini dulu sebelum proses tahun ajaran baru berikutnya.
            </div>
        @endif

        <div class="mb-3 d-flex justify-content-between align-items-center">

            <a href="{{ url('admin/siswa/create') }}"
            class="btn btn-success">

                + Tambah

            </a>

            <form method="GET"
                action="{{ url('admin/siswa') }}"
                class="d-flex">

                <input type="text"
                    name="keyword"
                    class="form-control me-2"
                    placeholder="Cari NIS, NISN, atau Nama Siswa..."
                    value="{{ request('keyword') }}"
                    style="width: 300px;">

                <button type="submit"
                        class="btn btn-primary me-2">

                    Cari

                </button>

                <a href="{{ url('admin/siswa') }}"
                class="btn btn-secondary">

                    Reset

                </a>

            </form>

        </div>
        </div>


        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th width="60" class="text-center">No</th>
                    <th class="text-center">Kelas</th>
                    <th class="text-center">Wali Kelas</th>
                    <th class="text-center">Tahun Ajaran</th>
                    <th class="text-center">Semester</th>
                    <th class="text-center">Jumlah Siswa</th>
                    <th width="120" class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($kelas as $k)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $k->kelas->nama_kelas }}
                        @if($k->is_priority_intake)
                            <div>
                                <span class="badge bg-warning text-dark">Prioritas intake baru</span>
                            </div>
                        @endif
                    </td>

                    <td>
                        {{ $k->guru->nama_guru }}
                    </td>

                    <td>
                        {{ $k->tahunAjaran->tahun }}
                    </td>

                    <td>
                        {{ $k->semester->nama_semester }}
                    </td>

                    <td class="text-center">
                        {{ $k->jumlah_siswa }}
                    </td>

                    <td class="text-center">

                        <a href="{{ url('siswa/'.$k->id_kelas_aktif) }}"
                           class="btn btn-info btn-sm">
                            Detail
                        </a>
                    </td>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Tidak ada kelas yang cocok dengan filter saat ini.
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
