<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - Aceh Data Warehouse</title>

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
            --bs-link-color: var(--adw-green);
            --bs-link-hover-color: var(--adw-green-dark);
        }

        body {
            min-height: 100vh;
            background-color: #f3f6f4;
            background-image:
                linear-gradient(135deg, rgba(15, 92, 77, .045) 25%, transparent 25%),
                linear-gradient(225deg, rgba(15, 92, 77, .045) 25%, transparent 25%);
            background-size: 56px 56px;
        }

        .page { min-height: 100vh; }

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

        .auth-card {
            position: relative;
            width: 100%;
            max-width: 460px;
            background: #fff;
            border: 1px solid var(--adw-line);
            border-radius: 16px;
            box-shadow: 0 18px 40px -22px rgba(10, 68, 57, .35);
            overflow: hidden;
        }
        .auth-card::before {
            content: '';
            display: block;
            height: 5px;
            background: linear-gradient(90deg,
                var(--adw-red) 0 34%,
                var(--adw-gold) 34% 67%,
                var(--adw-green) 67% 100%);
        }
        .auth-title {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 700;
            font-size: 1.85rem;
            letter-spacing: -.02em;
        }
        .auth-lead { color: var(--adw-muted); font-size: .95rem; }

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

        .alert-adw {
            display: flex;
            align-items: flex-start;
            gap: .5rem;
            border: 1px solid #bcdfd3;
            background: #eef7f3;
            color: var(--adw-green-dark);
            font-size: .9rem;
            border-radius: 10px;
        }
        .alert-adw .btn-close { margin-left: auto; }

        .footer-line {
            border-top: 1px solid var(--adw-line);
            color: var(--adw-muted);
            font-size: .85rem;
        }

        @media (max-width: 575.98px) {
            .auth-title { font-size: 1.6rem; }
        }
    </style>
</head>
<body>
    <div class="page d-flex flex-column">

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

        <main class="flex-grow-1 d-flex justify-content-center align-items-center px-3 py-5">
            <section class="auth-card" aria-labelledby="judul-lupa-password">
                <div class="p-4 p-sm-5">

                    <h1 id="judul-lupa-password" class="auth-title text-center mb-2">Lupa Password</h1>
                    <p class="auth-lead text-center mb-4">
                        Masukkan email akun Anda. Kami akan mengirimkan link untuk membuat password baru.
                    </p>

                    @if (session('status'))
                        <div class="alert alert-adw alert-dismissible fade show mb-4" role="alert" aria-live="polite">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>{{ session('status') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    @endif

                    @error('email')
                        <div class="alert alert-danger py-2 mb-4" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>{{ $message }}
                        </div>
                    @enderror

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{ old('email') }}" placeholder="nama@email.com"
                                       autocomplete="email" required autofocus>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-adw w-100">
                            Kirim Link Reset
                        </button>
                    </form>

                    <hr class="divider my-4">

                    <p class="register-text text-center mb-0">
                        Ingat password Anda? <a href="{{ route('login') }}">Kembali ke login</a>
                    </p>
                </div>
            </section>
        </main>

        <footer class="container-xl px-4 px-md-5 pb-3">
            <div class="footer-line text-center pt-3">
                &copy; {{ date('Y') }} Pemerintah Provinsi Aceh &ndash; Dinas Komunikasi
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>