@extends('layouts.sidebar-admin')

@section('css')

<link rel="stylesheet"
      href="{{ asset('css/kelola-akun.css') }}">

@endsection

@section('content')

<h3 class="page-title">
    EDIT AKUN
</h3>

<div class="card form-card">

    <div class="card-body">

        <form action="{{ url('admin/kelola-akun/'.$user->id_users) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="row">

                <!-- NAMA -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Nama <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ $user->name }}"
                           required>

                </div>


                <!-- USERNAME -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Username <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="username"
                           class="form-control"
                           value="{{ $user->username }}"
                           required>

                </div>


                <!-- EMAIL -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Email <span class="text-danger">*</span>
                    </label>

                    <input type="email"
                           name="email"
                           class="form-control"
                           value="{{ $user->email }}"
                           required>

                </div>


                <!-- PASSWORD -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Password Baru
                    </label>

                    <input type="password"
                           name="password"
                           class="form-control">

                    <small class="text-muted">
                        Kosongkan jika tidak ingin mengganti password
                    </small>

                </div>


                <!-- ROLE -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Role <span class="text-danger">*</span>
                    </label>

                    <select name="role"
                            id="role"
                            class="form-control"
                            required>

                        <option value="admin"
                            {{ $user->role == 'admin' ? 'selected' : '' }}>

                            Admin

                        </option>

                        <option value="guru"
                            {{ $user->role == 'guru' ? 'selected' : '' }}>

                            Guru

                        </option>

                        <option value="orangtua"
                            {{ $user->role == 'orangtua' ? 'selected' : '' }}>

                            Orang Tua

                        </option>

                    </select>

                </div>


                <!-- GURU -->
                <div class="col-md-6 mb-3"
                     id="guru-group">

                    <label class="form-label">
                        Guru
                    </label>

                    <select name="id_guru"
                            class="form-control">

                        <option value="">
                            -- Pilih Guru --
                        </option>

                        @foreach($guru as $g)

                        <option value="{{ $g->id_guru }}"
                            {{ $user->id_guru == $g->id_guru ? 'selected' : '' }}>

                            {{ $g->nama_guru }}

                        </option>

                        @endforeach

                    </select>

                </div>


                <!-- KELAS -->
                <div class="col-md-6 mb-3"
                     id="kelas-group">

                    <label class="form-label">
                        Kelas
                    </label>

                    <select id="kelas"
                            class="form-control">

                        <option value="">
                            -- Pilih Kelas --
                        </option>

                        @foreach($kelas as $k)

                        <option value="{{ $k->id_kelas }}"
                            {{ $kelasSelected == $k->id_kelas ? 'selected' : '' }}>

                            {{ $k->nama_kelas }}

                        </option>

                        @endforeach

                    </select>

                </div>


                <!-- SISWA -->
                <div class="col-md-6 mb-3"
                     id="siswa-group">

                    <label class="form-label">
                        Siswa
                    </label>

                    <select name="id_siswa"
                            id="siswa"
                            class="form-control">

                        <option value="">
                            -- Pilih Siswa --
                        </option>

                        @foreach($siswa as $s)

                        <option value="{{ $s->id_siswa }}"
                                data-kelas="{{ $s->id_kelas }}"
                            {{ $user->id_siswa == $s->id_siswa ? 'selected' : '' }}>

                            {{ $s->nama_lengkap }}

                        </option>

                        @endforeach

                    </select>

                </div>

            </div>


            <!-- BUTTON -->
            <div class="mt-3">

                <button type="submit"
                        class="btn btn-success">

                    Update

                </button>

                <a href="{{ url('admin/kelola-akun') }}"
                   class="btn btn-secondary">

                    Kembali

                </a>

            </div>

        </form>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function(){

    const roleSelect =
        document.getElementById('role');

    const kelasSelect =
        document.getElementById('kelas');

    const siswaSelect =
        document.getElementById('siswa');

    const guruGroup =
        document.getElementById('guru-group');

    const kelasGroup =
        document.getElementById('kelas-group');

    const siswaGroup =
        document.getElementById('siswa-group');



    function toggleRole(){

        if(roleSelect.value == 'guru'){

            guruGroup.style.display = 'block';

            kelasGroup.style.display = 'none';

            siswaGroup.style.display = 'none';

        }

        else if(roleSelect.value == 'orangtua'){

            guruGroup.style.display = 'none';

            kelasGroup.style.display = 'block';

            siswaGroup.style.display = 'block';

        }

        else{

            guruGroup.style.display = 'none';

            kelasGroup.style.display = 'none';

            siswaGroup.style.display = 'none';

        }

    }


    toggleRole();

    roleSelect.addEventListener(
        'change',
        toggleRole
    );



    kelasSelect.addEventListener('change', function(){

        let kelas = this.value;

        let options =
            siswaSelect.querySelectorAll('option');

        options.forEach(function(option){

            if(option.value == ''){

                option.style.display = 'block';

            }

            else if(option.dataset.kelas == kelas){

                option.style.display = 'block';

            }

            else{

                option.style.display = 'none';

            }

        });

    });


    kelasSelect.dispatchEvent(
        new Event('change')
    );

});

</script>

@endsection