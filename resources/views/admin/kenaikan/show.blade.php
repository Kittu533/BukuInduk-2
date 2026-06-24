@extends('layouts.sidebar-admin')

@section('content')

<h3 class="page-title">KENAIKAN / KELULUSAN — {{ $siswa->nama_lengkap }}</h3>

<div class="card mb-3">
    <div class="card-body">
        <table class="table table-borderless">
            <tr><th width="150">Nama</th><td>: {{ $siswa->nama_lengkap }}</td></tr>
            <tr><th>NIS</th><td>: {{ $siswa->nis }}</td></tr>
            <tr><th>NISN</th><td>: {{ $siswa->nisn }}</td></tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($tahunDiikuti->isEmpty())
            <p class="text-muted">Siswa belum terdaftar di kelas aktif manapun.</p>
        @else
            <form action="{{ url('admin/kenaikan/siswa/'.$siswa->id_siswa) }}"
            method="POST">

            @csrf
            @method('PUT')

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <th width="280">Status Kenaikan</th>
                            <th width="220">Diproses Pada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tahunDiikuti as $ta)
                        @php
                            $current = $kenaikanList->get($ta->id_tahun);
                            $currentStatus = $current?->status ?? 'naik';
                            $processedAt = $current?->updated_at ?? $current?->created_at;
                        @endphp
                        <tr>
                            <td>{{ $ta->tahun }}</td>
                            <td>
                                <select name="tahun[{{ $ta->id_tahun }}]" class="form-select form-select-sm">
                                    <option value="naik" {{ $currentStatus == 'naik' ? 'selected' : '' }}>Naik Kelas</option>
                                    <option value="tidak_naik" {{ $currentStatus == 'tidak_naik' ? 'selected' : '' }}>Tidak Naik</option>
                                    <option value="lulus" {{ $currentStatus == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                </select>
                            </td>
                            <td>
                                {{ $processedAt ? $processedAt->format('d-m-Y H:i:s') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ url('admin/kenaikan') }}" class="btn btn-secondary">Kembali</a>
            </form>
        @endif

    </div>
</div>

@endsection
