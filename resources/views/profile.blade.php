<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - Aceh Data Warehouse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --teal: #0d9488;
            --teal-dark: #0a7068;
            --teal-soft: #e8f5f0;
            --ink: #1a1a2e;
            --muted: #5a6577;
            --soft: #8892a4;
            --line: #e0e4f0;
            --bg: #f0f2f5;
        }

        body {
            background-color: var(--bg);
            color: var(--ink);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ---------- Padding halaman (mengikuti proporsi gambar) ---------- */
        .pad-title { padding-inline: clamp(1rem, 5.5vw, 5.5rem); }
        .pad-cards { padding-left: clamp(1rem, 13vw, 13rem); padding-right: clamp(1rem, 8vw, 8rem); }

        /* ---------- Navbar ---------- */
        .topbar {
            height: 72px;
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--line);
            box-shadow: 0 2px 12px rgba(16, 24, 40, .04);
            position: fixed; top: 0; left: 0; right: 0; z-index: 1100;
            padding-inline: 1.75rem;
        }
        .icon-btn {
            width: 40px; height: 40px;
            display: inline-flex; align-items: center; justify-content: center;
            border: 0; border-radius: 50%;
            background: transparent; color: var(--muted);
            font-size: 19px; text-decoration: none;
            transition: background .15s, color .15s;
            position: relative;
        }
        .icon-btn:hover { background: var(--teal-soft); color: var(--teal); }
        .notif-dot {
            position: absolute; top: 9px; right: 10px;
            width: 9px; height: 9px; border-radius: 50%;
            background: #e5484d; border: 2px solid #fff;
        }
        .nav-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background-color: #eef2f9; color: var(--muted);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 18px;
        }

        .profile-toggle::after {
            display: none;
        }

        .profile-toggle:focus {
            box-shadow: none;
        }

        .profile-menu {
            min-width: 210px;
            border: 1px solid #e0e4f0;
            border-radius: 10px;
            padding: 6px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.10);
        }

        .profile-menu .dropdown-item {
            font-size: 13px;
            color: #333;
            border-radius: 6px;
            padding: 8px 12px;
        }

        .profile-menu .dropdown-item:hover {
            background-color: var(--teal-soft);
            color: #0d9488;
        }

        .profile-menu .dropdown-item.active {
            background-color: var(--teal-soft);
            color: #0d9488;
        }

        /* ---------- Judul halaman ---------- */
        .page-title { font-weight: 800; font-size: 28px; letter-spacing: -.3px; margin-bottom: 4px; }
        .page-sub { font-size: 14px; color: var(--muted); margin: 0; }

        /* ---------- Kartu ---------- */
        .card-ui {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .04), 0 8px 24px rgba(16, 24, 40, .05);
            transition: box-shadow .2s ease;
            overflow: hidden;
        }
        .card-ui:hover { box-shadow: 0 1px 2px rgba(16, 24, 40, .04), 0 12px 32px rgba(16, 24, 40, .08); }

        .card-heading { display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem; }
        .card-heading .chip {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--teal-soft); color: var(--teal);
            display: inline-flex; align-items: center; justify-content: center; font-size: 18px;
        }
        .card-heading h5 { margin: 0; font-weight: 800; font-size: 18px; }
        .card-heading small { display: block; color: var(--soft); font-size: 12.5px; font-weight: 500; }

        /* ---------- Kartu profil (kiri) ---------- */
        .profile-banner {
            height: 120px;
        }
        .avatar-wrap { position: relative; display: inline-block; margin-top: -78px; }
        .avatar-box {
            width: 144px; height: 144px; border-radius: 34px;
            background: #f4f6fa; border: 5px solid #fff;
            box-shadow: 0 6px 18px rgba(16, 24, 40, .14);
            display: flex; align-items: center; justify-content: center; overflow: hidden;
        }
        .avatar-box .placeholder-icon { font-size: 104px; color: #444; line-height: 1; }
        .avatar-box img { width: 100%; height: 100%; object-fit: cover; }
        .camera-btn {
            position: absolute; right: -6px; bottom: -6px;
            width: 40px; height: 40px; border-radius: 50%;
            background: #fff; color: var(--ink);
            border: 1px solid var(--line);
            box-shadow: 0 4px 10px rgba(16, 24, 40, .15);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: background-color .15s, color .15s, border-color .15s, transform .15s;
        }
        .camera-btn:hover { background: var(--teal); color: #fff; border-color: var(--teal); transform: scale(1.06); }
        .link-danger-soft {
            background: none; border: 0; padding: 0;
            font-size: 13.5px; color: var(--muted); text-decoration: none;
            transition: color .15s;
        }
        .link-danger-soft:hover { color: #e5484d; text-decoration: underline; }

        /* ---------- Form ---------- */
        .form-label-ui { font-weight: 600; font-size: 13px; color: var(--ink); margin-bottom: 6px; }
        .form-control, .input-group-text { border-color: #d8dde8; padding: 10px 12px; font-size: 14.5px; }
        .input-group-text { background: #f7f8fb; color: var(--soft); }
        .form-control:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 4px rgba(13, 148, 136, .14);
        }
        .form-control[readonly] { background: #f7f8fb; color: #3b4457; }
        .form-control[readonly]:focus { box-shadow: none; border-color: #d8dde8; }
        .input-group:focus-within .input-group-text { border-color: var(--teal); color: var(--teal); }
        .btn-eye {
            background: #fff; border: 1px solid #d8dde8; color: var(--muted);
            border-left: 0; padding-inline: 14px;
        }
        .btn-eye:hover { color: var(--teal); background: #fff; }

        /* ---------- Tombol ---------- */
        .btn-teal {
            background: linear-gradient(135deg, var(--teal), var(--teal-dark));
            color: #fff; font-weight: 600; font-size: 14px;
            padding: 10px 22px; border: 0; border-radius: 10px;
            box-shadow: 0 4px 12px rgba(13, 148, 136, .28);
            transition: transform .15s, box-shadow .15s;
        }
        .btn-teal:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 8px 18px rgba(13, 148, 136, .35); }
        .btn-teal:active { transform: translateY(0); }

        /* ---------- Indikator kekuatan password ---------- */
        .strength { height: 6px; border-radius: 6px; background: #eceff5; overflow: hidden; }
        .strength > span { display: block; height: 100%; width: 0; border-radius: 6px; transition: width .25s, background .25s; }
        .hint { font-size: 12.5px; color: var(--soft); }

        .alert-ui { border: 0; border-radius: 12px; font-size: 14px; font-weight: 500; }

        @media (max-width: 991.98px) {
            .pad-cards { padding-inline: clamp(1rem, 4vw, 2rem); }
        }
    </style>
</head>

<body>

    <div class="d-flex flex-column" style="min-height: 100vh;">

        <!-- ==================== TOP NAVBAR ==================== -->
        <nav class="topbar d-flex align-items-center">

            <!-- Kembali + Judul -->
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('index') }}" class="icon-btn" title="Kembali" style="color: var(--ink); font-size: 22px;">
                    <i class="bi bi-chevron-left"></i>
                </a>
                <span style="font-weight: 500; font-size: 18px;">Profile User</span>
            </div>

            <div class="flex-grow-1"></div>

            <!-- Ikon kanan -->
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="icon-btn" title="Notifikasi">
                    <i class="bi bi-bell"></i>
                    <span class="notif-dot"></span>
                </button>
                <button type="button" class="icon-btn" title="Pengaturan">
                    <i class="bi bi-gear"></i>
                </button>
                <div class="dropdown">
                    <button class="btn p-0 border-0 bg-transparent dropdown-toggle profile-toggle" type="button" data-bs-toggle="dropdown" aria-label="Menu Akun">
                        <span class="nav-avatar">
                            <i class="bi bi-person-fill"></i>
                        </span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end profile-menu">
                        <li class="px-3 pt-1 pb-2">
                            <div style="font-size: 13px; font-weight: 700; color: var(--ink);">
                                {{ $user->nama_lengkap ?? $user->username ?? 'Administrator' }}
                            </div>
                            <div style="font-size: 11px; color: var(--soft);">{{ $user->email ?: '-' }}</div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>

                        <li>
                            <a href="{{ route('profile') }}" class="dropdown-item d-flex align-items-center active">
                                <i class="bi bi-person me-2"></i> Profile
                            </a>
                        </li>

                        <li>
                            <a href="#" class="dropdown-item d-flex align-items-center">
                                <i class="bi bi-people me-2"></i> Manajemen User
                            </a>
                        </li>
                        <li>
                            <a href="http://192.168.222.152:8080/" target="_blank" rel="noopener" class="dropdown-item d-flex align-items-center">
                                <i class="bi bi-arrow-repeat me-2"></i> ETL
                            </a>
                        </li>

                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center w-100" style="color: #dc3545;">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>

        <!-- ==================== MAIN CONTENT ==================== -->
        <div class="flex-grow-1" style="margin-top: 72px; padding-top: 24px; padding-bottom: 56px;">

            <!-- Judul halaman -->
            <div class="pad-title mb-4">
                <h1 class="page-title">Pengaturan Profile Pengguna</h1>
                <p class="page-sub">Kelola informasi pengguna akun</p>
            </div>

            <div class="pad-cards">

                {{-- Notifikasi sukses / error --}}
                @if (session('success'))
                    <div class="alert alert-success alert-ui alert-dismissible fade show d-flex align-items-center mb-4" role="alert" aria-live="polite">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <br>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-ui d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Terdapat data yang belum valid. Periksa kembali isian Anda.
                    </div>
                @endif

                <!-- ========== GRID UTAMA: KIRI (kartu profil) | KANAN (2 kartu) ========== -->
                <div class="row gx-5 gy-4">

                    <!-- ==================== KOLOM KIRI: KARTU PROFIL ==================== -->
                    <div class="col-lg-4">
                        <div class="card-ui h-100">

                            <div class="profile-banner"></div>

                            <div class="p-4 pt-0">

                                <!-- Foto profil -->
                                <div class="text-center mb-4">
                                    <div class="avatar-wrap">
                                        <div class="avatar-box" id="avatarBox">
                                            <i class="bi bi-person-fill placeholder-icon"></i>
                                        </div>
                                        <label for="fotoInput" class="camera-btn mb-0" title="Ubah foto">
                                            <i class="bi bi-camera" style="font-size: 17px;"></i>
                                        </label>
                                        <input type="file" id="fotoInput" name="foto" accept="image/*" class="d-none" onchange="previewFoto(event)">
                                    </div>

                                    <div class="mt-2">
                                        <button type="button" class="link-danger-soft" onclick="hapusFoto()">Hapus Foto</button>
                                    </div>

                                    <h5 class="mt-3 mb-0" style="font-weight: 800; font-size: 20px;">
                                        {{ $user->nama_lengkap ?? $user->username }}
                                    </h5>
                                </div>

                                <!-- Info ringkas -->
                                <div class="mb-3">
                                    <label class="form-label-ui">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" value="{{ $user->username }}" readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label-ui">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="text" class="form-control" value="{{ $user->email ?: '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label-ui">Nomer Handphone</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" class="form-control" value="{{ $user->nomor_telepon ?: '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label-ui">Role</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                        <input type="text" class="form-control" value="{{ $user->role?->jenis_user ?? 'User' }}" readonly>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ==================== KOLOM KANAN ==================== -->
                    <div class="col-lg-8">

                        <!-- ---------- KARTU 1: INFORMASI DAN IDENTITAS ---------- -->
                        <div class="card-ui p-4" style="margin-bottom: 2.25rem;">
                            <div class="card-heading">
                                <span class="chip"><i class="bi bi-person-vcard"></i></span>
                                <div>
                                    <h5>Informasi dan Identitas</h5>
                                    <small>Perbarui data diri yang tampil pada akun Anda</small>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label-ui">Username Akun</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-at"></i></span>
                                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}">
                                        </div>
                                        @error('username') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-ui">Nama Lengkap</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $user->nama_lengkap ?? '') }}">
                                        </div>
                                        @error('nama_lengkap') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-ui">Nomer Telepon</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                            <input type="tel" name="nomor_telepon" class="form-control @error('nomor_telepon') is-invalid @enderror" value="{{ old('nomor_telepon', $user->nomor_telepon ?? '') }}">
                                        </div>
                                        @error('nomor_telepon') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-ui">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email ?? '') }}">
                                        </div>
                                        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-teal">
                                        <i class="bi bi-check2-circle me-1"></i> Perbarui Data Identitas
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- ---------- KARTU 2: UBAH KATA SANDI ---------- -->
                        <div class="card-ui p-4">
                            <div class="card-heading">
                                <span class="chip"><i class="bi bi-shield-lock"></i></span>
                                <div>
                                    <h5>Ubah Kata Sandi (Password)</h5>
                                    <small>Gunakan kombinasi huruf, angka, dan simbol agar lebih aman</small>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('profile.password.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <!-- Baris 1: lebar penuh -->
                                    <div class="col-12">
                                        <label class="form-label-ui">Kata Sandi Saat ini</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                                            <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Masukkan Kata Sandi Lama Anda">
                                            <button type="button" class="btn btn-eye" onclick="togglePassword('current_password', this)" title="Tampilkan/sembunyikan">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        @error('current_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Baris 2: dua kolom -->
                                    <div class="col-md-6">
                                        <label class="form-label-ui">Kata Sandi Baru</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan Kata Sandi Baru" oninput="cekKekuatan(); cekKecocokan();">
                                            <button type="button" class="btn btn-eye" onclick="togglePassword('password', this)" title="Tampilkan/sembunyikan">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="strength mt-2"><span id="strengthBar"></span></div>
                                        <div class="hint mt-1" id="strengthText">Kekuatan kata sandi</div>
                                        @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-ui">Konfirmasi Kata Sandi Baru</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Masukkan Kembali Kata Sandi Baru" oninput="cekKecocokan()">
                                            <button type="button" class="btn btn-eye" onclick="togglePassword('password_confirmation', this)" title="Tampilkan/sembunyikan">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="hint mt-2" id="matchText">&nbsp;</div>
                                        @error('password_confirmation') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-teal">
                                        <i class="bi bi-shield-check me-1">  Simpan Kata Sandi Baru</i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /* Tampilkan / sembunyikan password */
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            const hidden = input.type === 'password';

            input.type = hidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !hidden);
            icon.classList.toggle('bi-eye-slash', hidden);
        }

        /* Indikator kekuatan password */
        function cekKekuatan() {
            const v = document.getElementById('password').value;
            const bar = document.getElementById('strengthBar');
            const txt = document.getElementById('strengthText');

            let skor = 0;
            if (v.length >= 8) skor++;
            if (/[A-Z]/.test(v) && /[a-z]/.test(v)) skor++;
            if (/\d/.test(v)) skor++;
            if (/[^A-Za-z0-9]/.test(v)) skor++;

            const level = [
                { w: '0%',   c: '#eceff5', t: 'Kekuatan kata sandi' },
                { w: '25%',  c: '#e5484d', t: 'Lemah' },
                { w: '50%',  c: '#f59e0b', t: 'Cukup' },
                { w: '75%',  c: '#84cc16', t: 'Kuat' },
                { w: '100%', c: '#0d9488', t: 'Sangat kuat' },
            ];
            const l = v.length ? level[Math.max(skor, 1)] : level[0];
            bar.style.width = l.w;
            bar.style.background = l.c;
            txt.textContent = l.t;
        }

        /* Cek kecocokan konfirmasi password */
        function cekKecocokan() {
            const p = document.getElementById('password').value;
            const c = document.getElementById('password_confirmation').value;
            const el = document.getElementById('matchText');

            if (!c) { el.innerHTML = '&nbsp;'; el.style.color = ''; return; }
            if (p === c) {
                el.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Kata sandi cocok';
                el.style.color = '#0d9488';
            } else {
                el.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i>Kata sandi belum cocok';
                el.style.color = '#e5484d';
            }
        }

        /* Pratinjau foto profil */
        function previewFoto(e) {
            const file = e.target.files[0];
            if (!file) return;
            const box = document.getElementById('avatarBox');
            const reader = new FileReader();
            reader.onload = ev => { box.innerHTML = '<img src="' + ev.target.result + '" alt="Foto Profil">'; };
            reader.readAsDataURL(file);
        }

        function hapusFoto() {
            document.getElementById('fotoInput').value = '';
            document.getElementById('avatarBox').innerHTML = '<i class="bi bi-person-fill placeholder-icon"></i>';
        }
    </script>
</body>

</html>