@extends('layouts.sidebar-admin')

@section('content')

@php

$pekerjaan = [
    'Tidak Bekerja',
    'Petani',
    'Nelayan',
    'Buruh',
    'Wiraswasta',
    'Pedagang',
    'Karyawan Swasta',
    'PNS',
    'TNI',
    'POLRI',
    'Guru',
    'Dosen',
    'Dokter',
    'Perawat',
    'Pensiunan',
    'Lainnya'
];

@endphp

<h3 class="page-title">
    EDIT DATA SISWA
</h3>

<div class="card">
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>
            </div>
        @endif

        @if($emptyGradeXClassIds->isNotEmpty())
            <div class="alert alert-warning">
                Masih ada rombel <strong>kelas X</strong> yang kosong. Hindari memindahkan siswa aktif terakhir dari rombel X, dan prioritaskan pengisian rombel yang berlabel <strong>Prioritas intake baru</strong>.
            </div>
        @endif

        <form action="{{ url('admin/siswa/'.$siswa->id_siswa) }}"
              method="POST">

            @csrf
            @method('PUT')

            {{-- DATA AKADEMIK --}}

            <div class="card mb-4">
                <div class="card-header">
                    <strong>Data Akademik</strong>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-3 mb-3">
                            <label>ID Siswa</label>

                            <input type="text"
                                   class="form-control"
                                   value="{{ $siswa->id_siswa }}"
                                   readonly>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Tahun Masuk</label>

                            <input type="date"
                                   class="form-control"
                                   value="{{ optional($siswa->tahun_masuk)->format('Y-m-d') }}"
                                   readonly>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Status Siswa</label>

                            <select name="status_siswa"
                                    class="form-control">

                                <option value="aktif"
                                    {{ $siswa->status_siswa == 'aktif' ? 'selected' : '' }}>
                                    Aktif
                                </option>

                                <option value="lulus"
                                    {{ $siswa->status_siswa == 'lulus' ? 'selected' : '' }}>
                                    Lulus
                                </option>

                                <option value="do"
                                    {{ $siswa->status_siswa == 'do' ? 'selected' : '' }}>
                                    Drop Out
                                </option>

                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Kelas Aktif</label>

                            <select name="id_kelas_aktif"
                                    class="form-control"
                                    required>

                                @foreach($kelasAktif as $ka)

                                    <option value="{{ $ka->id_kelas_aktif }}"
                                        {{
                                            old('id_kelas_aktif', optional($siswa->siswaKelas->first())->id_kelas_aktif)
                                            ==
                                            $ka->id_kelas_aktif
                                            ? 'selected'
                                            : ''
                                        }}>
                                        {{ $ka->kelas->nama_kelas }}
                                        @if($emptyGradeXClassIds->contains($ka->id_kelas_aktif))
                                            - Prioritas intake baru
                                        @endif
                                        ({{ $ka->jumlah_siswa_aktif ?? 0 }} siswa aktif)
                                    </option>

                                @endforeach

                            </select>
                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Kelas Awal Masuk</label>

                            <select name="id_kelas"
                                    class="form-control">

                                @foreach($kelas as $k)

                                    <option value="{{ $k->id_kelas }}"
                                        {{ old('id_kelas', $siswa->id_kelas) == $k->id_kelas ? 'selected' : '' }}>

                                        {{ $k->nama_kelas }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>

                </div>
            </div>

            {{-- DATA SISWA --}}

            <div class="card mb-4">

                <div class="card-header">
                    <strong>Data Siswa</strong>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-3 mb-3">
                            <label>NIS *</label>

                            <input type="text"
                                   name="nis"
                                   class="form-control"
                                   value="{{ old('nis',$siswa->nis) }}"
                                   required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>NISN</label>

                            <input type="text"
                                   name="nisn"
                                   class="form-control"
                                   value="{{ old('nisn',$siswa->nisn) }}">
                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Asal Sekolah</label>

                            <input type="text"
                                name="asal_sekolah"
                                class="form-control"
                                value="{{ old('asal_sekolah', $siswa->asal_sekolah) }}">

                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Nama Lengkap *</label>

                            <input type="text"
                                   name="nama_lengkap"
                                   class="form-control"
                                   value="{{ old('nama_lengkap',$siswa->nama_lengkap) }}"
                                   required>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-3 mb-3">
                            <label>Jenis Kelamin</label>

                            <select name="jenis_kelamin"
                                    class="form-control">

                                <option value="Laki-laki"
                                    {{ in_array($siswa->jenis_kelamin, ['L', 'Laki-Laki', 'Laki-laki'], true) ? 'selected' : '' }}>
                                    Laki-laki
                                </option>

                                <option value="Perempuan"
                                    {{ in_array($siswa->jenis_kelamin, ['P', 'Perempuan'], true) ? 'selected' : '' }}>
                                    Perempuan
                                </option>

                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Agama</label>

                            <select name="agama"
                                    class="form-control">

                                <option value="">-- Pilih --</option>

                                @foreach([
                                    'Islam',
                                    'Kristen',
                                    'Katolik',
                                    'Hindu',
                                    'Buddha',
                                    'Konghucu'
                                ] as $agama)

                                    <option value="{{ $agama }}"
                                        {{ old('agama',$siswa->agama)==$agama ? 'selected' : '' }}>
                                        {{ $agama }}
                                    </option>

                                @endforeach

                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Tempat Lahir</label>

                            <input type="text"
                                   name="tempat_lahir"
                                   class="form-control"
                                   value="{{ old('tempat_lahir',$siswa->tempat_lahir) }}">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Tanggal Lahir</label>

                            <input type="date"
                                   name="tanggal_lahir"
                                   class="form-control"
                                   value="{{ optional($siswa->tanggal_lahir)->format('Y-m-d') }}">
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-3 mb-3">
                            <label>Anak Ke</label>

                            <input type="number"
                                   name="anak_ke"
                                   class="form-control"
                                   value="{{ old('anak_ke',$siswa->anak_ke) }}">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Status Keluarga</label>

                            <select name="status_keluarga"
                                    class="form-control">

                                <option value="">-- Pilih --</option>

                                <option value="Kandung"
                                    {{ old('status_keluarga',$siswa->status_keluarga)=='Kandung' ? 'selected' : '' }}>
                                    Kandung
                                </option>

                                <option value="Tiri"
                                    {{ old('status_keluarga',$siswa->status_keluarga)=='Tiri' ? 'selected' : '' }}>
                                    Tiri
                                </option>

                                <option value="Angkat"
                                    {{ old('status_keluarga',$siswa->status_keluarga)=='Angkat' ? 'selected' : '' }}>
                                    Angkat
                                </option>

                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>No HP</label>

                            <input type="text"
                                   name="no_telp"
                                   class="form-control"
                                   value="{{ old('no_telp',$siswa->no_telp) }}">
                        </div>

                    </div>

                    <div class="mb-3">
                        <label>Alamat</label>

                        <textarea name="alamat"
                                  rows="3"
                                  class="form-control">{{ old('alamat',$siswa->alamat) }}</textarea>
                    </div>

                </div>

            </div>

            {{-- DATA ORANG TUA --}}

            <div class="card mb-4">

                <div class="card-header">
                    <strong>Data Orang Tua</strong>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Nama Ayah</label>

                            <input type="text"
                                   name="nama_ayah"
                                   class="form-control"
                                   value="{{ old('nama_ayah',$siswa->nama_ayah) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Pekerjaan Ayah</label>

                            <select name="pekerjaan_ayah"
                                    class="form-control">

                                <option value="">-- Pilih --</option>

                                @foreach($pekerjaan as $p)
                                    <option value="{{ $p }}"
                                        {{ old('pekerjaan_ayah',$siswa->pekerjaan_ayah)==$p ? 'selected' : '' }}>
                                        {{ $p }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Nama Ibu</label>

                            <input type="text"
                                   name="nama_ibu"
                                   class="form-control"
                                   value="{{ old('nama_ibu',$siswa->nama_ibu) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Pekerjaan Ibu</label>

                            <select name="pekerjaan_ibu"
                                    class="form-control">

                                <option value="">-- Pilih --</option>

                                @foreach($pekerjaan as $p)
                                    <option value="{{ $p }}"
                                        {{ old('pekerjaan_ibu',$siswa->pekerjaan_ibu)==$p ? 'selected' : '' }}>
                                        {{ $p }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-8 mb-3">
                            <label>Alamat Orang Tua</label>

                            <textarea name="alamat_ortu"
                                      rows="3"
                                      class="form-control">{{ old('alamat_ortu',$siswa->alamat_ortu) }}</textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>No HP Orang Tua</label>

                            <input type="text"
                                   name="no_telp_ortu"
                                   class="form-control"
                                   value="{{ old('no_telp_ortu',$siswa->no_telp_ortu) }}">
                        </div>

                    </div>

                </div>

            </div>

            {{-- DATA WALI --}}

            <div class="card mb-4">

                <div class="card-header">
                    <strong>Data Wali</strong>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Nama Wali</label>

                            <input type="text"
                                   name="nama_wali"
                                   class="form-control"
                                   value="{{ old('nama_wali',$siswa->nama_wali) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Pekerjaan Wali</label>

                            <select name="pekerjaan_wali"
                                    class="form-control">

                                <option value="">-- Pilih --</option>

                                @foreach($pekerjaan as $p)
                                    <option value="{{ $p }}"
                                        {{ old('pekerjaan_wali',$siswa->pekerjaan_wali)==$p ? 'selected' : '' }}>
                                        {{ $p }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-8 mb-3">
                            <label>Alamat Wali</label>

                            <textarea name="alamat_wali"
                                      rows="3"
                                      class="form-control">{{ old('alamat_wali',$siswa->alamat_wali) }}</textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>No HP Wali</label>

                            <input type="text"
                                   name="no_telp_wali"
                                   class="form-control"
                                   value="{{ old('no_telp_wali',$siswa->no_telp_wali) }}">
                        </div>

                    </div>

                </div>

            </div>

            <div class="text-end">

                <a href="{{ url('admin/siswa/detail/'.$siswa->id_siswa) }}"
                   class="btn btn-secondary">
                    Kembali
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    Update Data
                </button>

            </div>

        </form>

    </div>
</div>

@endsection
