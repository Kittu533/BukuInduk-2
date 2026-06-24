@extends('layouts.sidebar-admin')

@section('css')

<link rel="stylesheet"
      href="{{ asset('css/kelola-akun.css') }}">

@endsection

@section('content')

<h3 class="page-title">
    TAMBAH AKUN
</h3>

<div class="card form-card">

    <div class="card-body">

        <form action="{{ url('admin/kelola-akun') }}"
              method="POST">

            @csrf

            <div class="row">

                <!-- NAMA -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Nama <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           id="name"
                           class="form-control"
                           required>

                </div>


                <!-- USERNAME -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Username <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="username"
                           id="username"
                           class="form-control"
                           required>

                </div>


                <!-- EMAIL -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Email <span class="text-danger">*</span>
                    </label>

                    <input type="email"
                           name="email"
                           id="email"
                           class="form-control"
                           required>

                </div>


                <!-- PASSWORD -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Password <span class="text-danger">*</span>
                    </label>

                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control"
                           required>

                </div>


                <!-- ROLE -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Role <span class="text-danger">*</span>
                    </label>

                    <select name="role"
                            id="role"
                            class="form-control"
                            disabled
                            required>

                        <option value="">
                            -- Pilih Role --
                        </option>

                        <option value="admin">
                            Admin
                        </option>

                        <option value="guru">
                            Guru
                        </option>

                        <option value="orangtua">
                            Orang Tua
                        </option>

                    </select>

                </div>


                <!-- GURU -->
                <div class="col-md-6 mb-3"
                     id="guru-group"
                     style="display:none;">

                    <label class="form-label">
                        Guru
                    </label>

                    <select name="id_guru"
                            class="form-control">

                        <option value="">
                            -- Pilih Guru --
                        </option>

                        @foreach($guru as $g)

                        <option value="{{ $g->id_guru }}">

                            {{ $g->nama_guru }}

                        </option>

                        @endforeach

                    </select>

                </div>


                <!-- KELAS -->
                <div class="col-md-6 mb-3"
                     id="kelas-group"
                     style="display:none;">

                    <label class="form-label">
                        Kelas
                    </label>

                    <select name="id_kelas"
                            id="kelas"
                            class="form-control">

                        <option value="">
                            -- Pilih Kelas --
                        </option>

                        @foreach($kelas as $k)

                        <option value="{{ $k->id_kelas }}">

                            {{ $k->nama_kelas }}

                        </option>

                        @endforeach

                    </select>

                </div>


                <!-- SISWA -->
                <div class="col-md-6 mb-3"
                     id="siswa-group"
                     style="display:none;">

                    <label class="form-label">
                        Siswa
                    </label>

                    <select name="id_siswa"
                            id="siswa"
                            class="form-control">

                        <option value="">
                            -- Pilih Siswa --
                        </option>

                    </select>

                </div>

            </div>


            <!-- BUTTON -->
            <div class="mt-3">

                <button type="submit"
                        class="btn btn-success">

                    Simpan

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

window.onload = function () {

    const nameInput     = document.getElementById('name');
    const usernameInput = document.getElementById('username');
    const emailInput    = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    const roleSelect  = document.getElementById('role');
    const kelasSelect = document.getElementById('kelas');
    const siswaSelect = document.getElementById('siswa');

    const guruGroup  = document.getElementById('guru-group');
    const kelasGroup = document.getElementById('kelas-group');
    const siswaGroup = document.getElementById('siswa-group');

    const allSiswa = @json($siswa);


    /* =========================
       AKTIFKAN ROLE
    ========================= */

    function checkForm(){

        if(
            nameInput.value.trim()     !== '' &&
            usernameInput.value.trim() !== '' &&
            emailInput.value.trim()    !== '' &&
            passwordInput.value.trim() !== ''
        ){

            roleSelect.disabled = false;

        }

        else{

            roleSelect.disabled = true;

            roleSelect.value = '';

            guruGroup.style.display  = 'none';
            kelasGroup.style.display = 'none';
            siswaGroup.style.display = 'none';

        }

    }

    nameInput.addEventListener('keyup', checkForm);
    usernameInput.addEventListener('keyup', checkForm);
    emailInput.addEventListener('keyup', checkForm);
    passwordInput.addEventListener('keyup', checkForm);


    /* =========================
       ROLE
    ========================= */

    roleSelect.addEventListener('change', function(){

        let role = this.value;

        if(role == 'guru'){

            guruGroup.style.display  = 'block';
            kelasGroup.style.display = 'none';
            siswaGroup.style.display = 'none';

        }

        else if(role == 'orangtua'){

            guruGroup.style.display  = 'none';
            kelasGroup.style.display = 'block';
            siswaGroup.style.display = 'block';

        }

        else{

            guruGroup.style.display  = 'none';
            kelasGroup.style.display = 'none';
            siswaGroup.style.display = 'none';

        }

    });


    /* =========================
       FILTER SISWA
    ========================= */

    kelasSelect.addEventListener('change', function(){

        let kelasAktif = this.value;

        siswaSelect.innerHTML =
            '<option value="">-- Pilih Siswa --</option>';


        let filtered = allSiswa.filter(function(item){

            return item.id_kelas == kelasAktif;

        });


        filtered.forEach(function(item){

            let option = document.createElement('option');

            option.value = item.id_siswa;

            option.textContent = item.nama_lengkap;

            siswaSelect.appendChild(option);

        });

    });

}

</script>

@endsection
