<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Akun - Aceh Data Warehouse</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700&family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --adw-green: #0f5c4d;
            --adw-green-dark: #0a4439;
            --adw-gold: #c8962e;
            --adw-red: #a82a2f;
            --adw-ink: #17231f;
            --adw-muted: #62716b;
            --adw-line: #d9e2de;

            --bs-body-font-family: 'Public Sans', system-ui, sans-serif;
            --bs-body-color: var(--adw-ink);
            --bs-primary: var(--adw-green);
            --bs-primary-rgb: 15, 92, 77;
            --bs-link-color: var(--adw-green);
            --bs-link-hover-color: var(--adw-green-dark);
        }

        body {
            min-height: 100vh;
            background-color: #f3f6f4;
            /* Motif pucuk rebung: segitiga berulang yang sangat halus */
            background-image:
                linear-gradient(135deg, rgba(15, 92, 77, .045) 25%, transparent 25%),
                linear-gradient(225deg, rgba(15, 92, 77, .045) 25%, transparent 25%);
            background-size: 56px 56px;
        }

        .page { min-height: 100vh; }

        /* Brand */
        .brand-mark {
            width: 46px; height: 46px;
            display: grid; place-items: center;
            background: var(--adw-green);
            color: #f1d38a;
            border-radius: 12px;
            font-size: 1.4rem;
        }
        .brand-name {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            line-height: 1.15;
            letter-spacing: -.01em;
        }
        .brand-org { font-size: .85rem; color: var(--adw-muted); }

        /* Kartu login */
        .login-card {
            position: relative;
            width: 100%;
            max-width: 430px;
            background: #fff;
            border: 1px solid var(--adw-line);
            border-radius: 16px;
            box-shadow: 0 18px 40px -22px rgba(10, 68, 57, .35);
            overflow: hidden;
        }
        /* Garis warna songket Aceh: merah, emas, hijau */
        .login-card::before {
            content: '';
            display: block;
            height: 5px;
            background: linear-gradient(90deg,
                var(--adw-red) 0 34%,
                var(--adw-gold) 34% 67%,
                var(--adw-green) 67% 100%);
        }
        .login-title {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 700;
            font-size: 1.85rem;
            letter-spacing: -.02em;
        }
        .login-lead { color: var(--adw-muted); font-size: .95rem; }

        /* Form */
        .form-label { font-weight: 600; font-size: .875rem; margin-bottom: .35rem; }
        .input-group-text {
            background: #f6f9f7;
            border-color: var(--adw-line);
            color: var(--adw-muted);
        }
        .form-control {
            border-color: var(--adw-line);
            padding: .65rem .85rem;
            font-size: .95rem;
        }
        .form-control::placeholder { color: #9aa8a2; }
        .form-control:focus {
            border-color: var(--adw-green);
            box-shadow: 0 0 0 .2rem rgba(15, 92, 77, .15);
        }
        .input-group:focus-within .input-group-text { border-color: var(--adw-green); }
        .btn-toggle {
            background: #fff;
            border: 1px solid var(--adw-line);
            border-left: 0;
            color: var(--adw-muted);
        }
        .btn-toggle:hover { color: var(--adw-green); background: #fff; }
        .input-group:focus-within .btn-toggle { border-color: var(--adw-green); }

        .btn-adw {
            --bs-btn-color: #fff;
            --bs-btn-bg: var(--adw-green);
            --bs-btn-border-color: var(--adw-green);
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: var(--adw-green-dark);
            --bs-btn-hover-border-color: var(--adw-green-dark);
            --bs-btn-active-bg: var(--adw-green-dark);
            --bs-btn-active-border-color: var(--adw-green-dark);
            --bs-btn-focus-shadow-rgb: 15, 92, 77;
            font-weight: 600;
            padding: .7rem 1rem;
            border-radius: 10px;
        }

        .divider { border-top: 1px solid var(--adw-line); opacity: 1; }
        .register-text { font-size: .9rem; color: var(--adw-muted); }
        .register-text a { font-weight: 600; text-decoration: none; }
        .register-text a:hover { text-decoration: underline; }

        /* Footer */
        .footer-line {
            border-top: 1px solid var(--adw-line);
            color: var(--adw-muted);
            font-size: .85rem;
        }

        @media (max-width: 575.98px) {
            .login-title { font-size: 1.6rem; }
        }
    </style>
</head>
<body>
    <div class="page d-flex flex-column">

        {{-- Header --}}
        <header class="container-xl pt-4 px-4 px-md-5">
            <div class="d-flex align-items-center gap-3">
                <div class="brand-mark" aria-hidden="true">
                    <i class="bi bi-bank2"></i>
                </div>
                <div>
                    <div class="brand-name">Aceh Data Warehouse</div>
                    <div class="brand-org">Pemerintah Provinsi Aceh</div>
                </div>
            </div>
        </header>

        {{-- Konten --}}
        <main class="flex-grow-1 d-flex justify-content-center align-items-center px-3 py-5">
            <section class="login-card" aria-labelledby="judul-login">
                <div class="p-4 p-sm-5">

                    <h1 id="judul-login" class="login-title text-center mb-2">Masuk Akun</h1>
                    <p class="login-lead text-center mb-4">
                        Masukkan akun Anda untuk mengakses dashboard analitik.
                    </p>

                    <form method="POST" action="{{ route('login.authenticate') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="username" name="username"
                                       placeholder="Masukkan username" autocomplete="username" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <label for="password" class="form-label">Password</label>
                                <a href="#" class="small text-decoration-none">Lupa password?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Masukkan password" autocomplete="current-password" required>
                                <button type="button" id="togglePassword" class="btn btn-toggle"
                                        aria-label="Tampilkan password">
                                    <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-adw w-100">
                            Masuk ke dashboard
                        </button>
                    </form>

                    <hr class="divider my-4">

                    <p class="register-text text-center mb-0">
                        Belum punya akun? <a href="{{ route('signin') }}">Daftar akun di sini</a>
                    </p>
                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="container-xl px-4 px-md-5 pb-3">
            <div class="footer-line text-center pt-3">
                &copy; {{ date('Y') }} Pemerintah Provinsi Aceh &ndash; Dinas Komunikasi
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const pw = document.getElementById('password');
        const btn = document.getElementById('togglePassword');
        const icon = document.getElementById('togglePasswordIcon');

        btn.addEventListener('click', () => {
            const show = pw.type === 'password';
            pw.type = show ? 'text' : 'password';
            icon.className = show ? 'bi bi-eye' : 'bi bi-eye-slash';
            btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    </script>
</body>
</html>
