@extends('layouts.sidebar-admin')

@section('content')

<h3 class="page-title">
    TAMBAH DATA SISWA
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
                Intake siswa baru diprioritaskan untuk rombel <strong>kelas X</strong> yang masih kosong. Pilih salah satu rombel X berlabel <strong>Prioritas intake baru</strong> sebelum lanjut proses tahun ajaran berikutnya.
            </div>
        @endif

        <form action="{{ url('admin/siswa') }}" method="POST">
            @csrf

            {{-- DATA AKADEMIK --}}
            <div class="card mb-3">
                <div class="card-header">
                    <strong>Data Akademik</strong>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label>Status Masuk <span class="text-danger">*</span></label>

                            <select name="status_masuk"
                                    class="form-control"
                                    required>

                                <option value="baru" {{ old('status_masuk') === 'baru' ? 'selected' : '' }}>
                                    Siswa Baru
                                </option>

                                <option value="pindahan" {{ old('status_masuk') === 'pindahan' ? 'selected' : '' }}>
                                    Siswa Pindahan
                                </option>

                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Tahun Masuk <span class="text-danger">*</span></label>

                            <input type="date"
                                   name="tahun_masuk"
                                   class="form-control"
                                   value="{{ old('tahun_masuk') }}"
                                   required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Kelas Awal <span class="text-danger">*</span></label>

                            <select name="id_kelas"
                                    class="form-control"
                                    required>

                                <option value="">
                                    -- Pilih Kelas Awal --
                                </option>

                                @foreach($kelas as $k)

                                    <option value="{{ $k->id_kelas }}" {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>

                                @endforeach

                            </select>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Kelas Aktif Saat Ini <span class="text-danger">*</span></label>

                            <select name="id_kelas_aktif"
                                    class="form-control"
                                    required>

                                <option value="">
                                    -- Pilih Kelas Aktif --
                                </option>

                                @foreach($kelasAktif as $ka)

                                    <option value="{{ $ka->id_kelas_aktif }}" {{ old('id_kelas_aktif') == $ka->id_kelas_aktif ? 'selected' : '' }}>

                                        {{ $ka->kelas->nama_kelas ?? '-' }}
                                        @if($emptyGradeXClassIds->contains($ka->id_kelas_aktif))
                                            - Prioritas intake baru
                                        @endif
                                        ({{ $ka->jumlah_siswa_aktif ?? 0 }} siswa aktif)

                                    </option>

                                @endforeach

                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Asal Sekolah</label>

                            <input type="text"
                                   name="asal_sekolah"
                                   class="form-control"
                                   value="{{ old('asal_sekolah') }}">
                        </div>

                    </div>

                </div>
            </div>

            {{-- DATA SISWA --}}
            <div class="card mb-3">
                <div class="card-header">
                    <strong>Data Siswa</strong>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label>NIS *</label>

                            <input type="number"
                                   name="nis"
                                   class="form-control"
                                   value="{{ old('nis') }}"
                                   required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>NISN</label>

                            <input type="number"
                                   name="nisn"
                                   class="form-control"
                                   value="{{ old('nisn') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Nama Lengkap *</label>

                            <input type="text"
                                   name="nama_lengkap"
                                   class="form-control"
                                   value="{{ old('nama_lengkap') }}"
                                   required>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label>Jenis Kelamin *</label>

                            <select name="jenis_kelamin"
                                    class="form-control"
                                    required>

                                <option value="">-- Pilih --</option>

                                <option value="Laki-laki">
                                    Laki-laki
                                </option>

                                <option value="Perempuan">
                                    Perempuan
                                </option>

                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Tempat Lahir</label>

                            <input type="text"
                                   name="tempat_lahir"
                                   class="form-control"
                                   value="{{ old('tempat_lahir') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Tanggal Lahir</label>

                            <input type="date"
                                   name="tanggal_lahir"
                                   class="form-control"
                                   value="{{ old('tanggal_lahir') }}">
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label>Agama</label>

                            <select name="agama"
                                    class="form-control">

                                <option value="">-- Pilih --</option>
                                <option>Islam</option>
                                <option>Kristen</option>
                                <option>Katolik</option>

                            </select>
                        </div>

                        <div class="col-md-8 mb-3">
                            <label>Alamat</label>

                            <textarea name="alamat"
                                      rows="3"
                                      class="form-control">{{ old('alamat') }}</textarea>
                        </div>

                    </div>

                </div>
            </div>

            {{-- DATA ORANG TUA --}}
            <div class="card mb-3">
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
                                   value="{{ old('nama_ayah') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Nama Ibu</label>

                            <input type="text"
                                   name="nama_ibu"
                                   class="form-control"
                                   value="{{ old('nama_ibu') }}">
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Pekerjaan Ayah</label>

                            <input type="text"
                                   name="pekerjaan_ayah"
                                   class="form-control"
                                   value="{{ old('pekerjaan_ayah') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Pekerjaan Ibu</label>

                            <input type="text"
                                   name="pekerjaan_ibu"
                                   class="form-control"
                                   value="{{ old('pekerjaan_ibu') }}">
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>No Telp Orang Tua</label>

                            <input type="text"
                                   name="no_telp_ortu"
                                   class="form-control"
                                   value="{{ old('no_telp_ortu') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Alamat Orang Tua</label>

                            <input type="text"
                                   name="alamat_ortu"
                                   class="form-control"
                                   value="{{ old('alamat_ortu') }}">
                        </div>

                    </div>

                </div>
            </div>

            <div class="text-end">

                <a href="{{ url('admin/siswa') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>

                <button type="submit"
                        class="btn btn-success">
                    Simpan
                </button>

            </div>

        </form>

    </div>
</div>

@endsection
