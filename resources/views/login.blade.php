<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Login
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="{{ asset('css/login.css') }}">

    <!-- GOOGLE RECAPTCHA -->

    <script src="https://www.google.com/recaptcha/api.js"
            async
            defer>

    </script>

</head>

<body>

<!-- PEMBUNGKUS LOGIN -->

<div class="login-page">

    <div class="overlay"></div>

    <div class="login-container">

        <div class="login-box">

            <img src="{{ asset('img/logosma.png') }}"
                 class="logo-sekolah">

            <div class="subtitle">
                Buku Induk Siswa Berbasis Web<br>
                SMA Negeri 3 Cilacap
            </div>

            <h1>
                Masuk
            </h1>

            <!-- ALERT ERROR -->

            @if(session('error'))

                <div class="alert alert-danger">

                    {{ session('error') }}

                </div>

            @endif

            <form action="{{ route('proses.login') }}"
                  method="POST">

                @csrf

                <!-- USERNAME -->

                <div class="input-group-custom">

                    <span>
                        👤
                    </span>

                    <input type="text"
                           name="username"
                           class="form-control"
                           placeholder="Username"
                           required>

                </div>

                <!-- PASSWORD -->

                <div class="input-group-custom">

                    <span>
                        🔒
                    </span>

                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="Password"
                           required>

                </div>

                <!-- CAPTCHA -->

                <div class="recaptcha-wrapper">

                    <div class="g-recaptcha"
                         data-sitekey="6LcJZeYsAAAAAGt0McEj1DUGYNqXY9dJmLX4YqKI">

                    </div>

                </div>

                <!-- LUPA PASSWORD -->

                <div class="forgot-password">

                    <a href="{{ url('/forgot-password') }}">
                        Lupa Password?
                    </a>

                </div>


                <!-- BUTTON -->

                <button class="btn btn-login w-100">

                    Masuk

                </button>
            </form>

        </div>

    </div>

    <!-- FOOTER -->

    <footer>

        <div class="footer-content">

            <div class="footer-item">

                <h6>
                    Alamat
                </h6>

                <p>
                    Jl. Kalimantan No.14,
                    Cilacap Tengah
                </p>

            </div>

            <div class="footer-item">

                <h6>
                    Telepon
                </h6>

                <p>
                    (0282) 541809
                </p>

            </div>

            <div class="footer-item">

                <h6>
                    Email
                </h6>

                <p>
                    info@sman3cilacap.sch.id
                </p>

            </div>

        </div>

    </footer>

</div>

</body>

</html>