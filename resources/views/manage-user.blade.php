<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Aceh Data Warehouse</title>
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

        /* ---------- Padding halaman ---------- */
        .pad-page { padding-inline: clamp(1rem, 5.5vw, 5.5rem); }

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

        .profile-toggle::after { display: none; }
        .profile-toggle:focus { box-shadow: none; }

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
        .profile-menu .dropdown-item:hover,
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

        /* ---------- Toolbar (cari + aksi) ---------- */
        .form-control, .input-group-text { border-color: #d8dde8; padding: 10px 12px; font-size: 14.5px; }
        .input-group-text { background: #f7f8fb; color: var(--soft); }
        .form-control:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 4px rgba(13, 148, 136, .14);
        }
        .input-group:focus-within .input-group-text { border-color: var(--teal); color: var(--teal); }

        .search-wrap { max-width: 460px; }

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

        .btn-outline-ui {
            background: #fff; color: var(--ink);
            font-weight: 600; font-size: 14px;
            padding: 10px 18px; border: 1px solid #d8dde8; border-radius: 10px;
            transition: background-color .15s, color .15s, border-color .15s;
        }
        .btn-outline-ui:hover { background: var(--teal-soft); color: var(--teal); border-color: var(--teal); }

        /* ---------- Tabel ---------- */
        .table-ui { margin-bottom: 0; font-size: 14.5px; }
        .table-ui thead th {
            background: #f7f8fb;
            color: var(--ink);
            font-weight: 700; font-size: 13.5px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            white-space: nowrap;
        }
        .table-ui tbody td {
            padding: 14px 16px;
            border-color: var(--line);
            vertical-align: middle;
            color: #3b4457;
        }
        .table-ui tbody tr:hover { background: #fafbfd; }

        .badge-role {
            display: inline-block;
            padding: 4px 12px; border-radius: 999px;
            font-size: 12.5px; font-weight: 600;
        }
        .badge-admin { background: var(--teal-soft); color: var(--teal-dark); }
        .badge-user { background: #eef2f9; color: var(--muted); }

        .btn-aksi {
            width: 34px; height: 34px;
            display: inline-flex; align-items: center; justify-content: center;
            border: 0; border-radius: 9px;
            font-size: 15px; text-decoration: none;
            transition: background-color .15s, color .15s;
        }
        .btn-edit { background: var(--teal-soft); color: var(--teal); }
        .btn-edit:hover { background: var(--teal); color: #fff; }
        .btn-hapus { background: #fdecec; color: #dc3545; }
        .btn-hapus:hover { background: #dc3545; color: #fff; }

        /* ---------- Footer ---------- */
        .footer-ui {
            border-top: 1px solid var(--line);
            text-align: center;
            font-size: 13px;
            color: var(--soft);
            padding: 22px 0;
        }

        .modal { z-index: 1200; }
        .modal-backdrop { z-index: 1190; }

        /* ---------- Indikator kekuatan password ---------- */
        .strength { height: 6px; border-radius: 6px; background: #eceff5; overflow: hidden; }
        .strength > span { display: block; height: 100%; width: 0; border-radius: 6px; transition: width .25s, background .25s; }
        .hint { font-size: 12.5px; color: var(--soft); }

        /* ---------- Button show/hide password ---------- */
        .btn-eye {
            background: #fff; border: 1px solid #d8dde8; color: var(--muted);
            border-left: 0; padding-inline: 14px;
        }
        .btn-eye:hover { color: var(--teal); background: #fff; }

        /* ---------- Filter Button Badge ---------- */
        .badge { padding: 4px 8px; font-size: 11px; font-weight: 600; }
        .bg-teal { background-color: var(--teal) !important; }

        .dropdown-menu .dropdown-item .bi-check {
            opacity: 0;
            transition: opacity 0.2s;
        }

        .dropdown-menu .dropdown-item.active .bi-check {
            opacity: 1;
        }

    </style>
</head>

<body>

    <div class="d-flex flex-column" style="min-height: 100vh;">

        <!-- ==================== TOP NAVBAR ==================== -->
        <nav class="topbar d-flex align-items-center">

            <!-- Kembali + Judul -->
            <div class="d-flex align-items-center gap-2">
                <a href="#" class="icon-btn" title="Kembali" style="color: var(--ink); font-size: 22px;" onclick="history.back(); return false;">
                    <i class="bi bi-chevron-left"></i>
                </a>
                <span style="font-weight: 500; font-size: 18px;">Manajemen User</span>
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
                            <div style="font-size: 11px; color: var(--soft);">{{ $user->email ?? '-' }}</div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>

                        <li>
                            <a href="{{ route('profile') }}" class="dropdown-item d-flex align-items-center">
                                <i class="bi bi-person me-2"></i> Profile
                            </a>
                        </li>

                        <li>
                            <a href="#" class="dropdown-item d-flex align-items-center active">
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
                            <button type="button" class="dropdown-item d-flex align-items-center w-100" style="color: #dc3545;" data-bs-toggle="modal" data-bs-target="#konfirmasiLogout">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- ==================== MAIN CONTENT ==================== -->
        <div class="flex-grow-1" style="margin-top: 72px; padding-top: 24px; padding-bottom: 40px;">

            <!-- Judul halaman -->
            <div class="pad-page mb-4">
                <h1 class="page-title">Kelola Data Akun</h1>
                <p class="page-sub">Atur akun pengguna yang dapat mengakses sistem</p>
            </div>

            <div class="pad-page">

                <!-- Toolbar: cari (kiri) | export + tambah (kanan) -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

                    <form class="d-flex gap-2 flex-grow-1 search-wrap" action="#" method="GET" onsubmit="return false;">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="q" class="form-control" placeholder="Cari User">
                        </div>
                        <button type="submit" class="btn btn-outline-ui">Cari</button>
                    </form>



                    <div class="d-flex flex-wrap gap-2">
                        <div class="btn-group">
                            <button class="btn btn-outline-ui dropdown-toggle d-inline-flex align-items-center gap-2" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-funnel me-1"></i> Filter <span id="filterBadge" class="badge bg-teal ms-1" style="display: none;"></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                <li><a class="dropdown-item" href="#" data-sort="created_id_asc"><i class="bi bi-check me-2"></i> Waktu Dibuat (ID)</a></li>
                                <li><a class="dropdown-item" href="#" data-sort="nama_asc"><i class="bi bi-check me-2"></i> Nama Lengkap (A-Z)</a></li>
                                <li><a class="dropdown-item" href="#" data-sort="role_admin_first"><i class="bi bi-check me-2"></i> Role (Admin → User)</a></li>
                            </ul>
                        </div>
                        <a href="#" class="btn btn-outline-ui d-inline-flex align-items-center gap-2">
                            Export Data User <i class="bi bi-download"></i>
                        </a>
                        <button type="button" class="btn btn-teal d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                            Tambah User <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Tabel user -->
                <div class="card-ui">
                    <div class="table-responsive">
                        <table class="table table-bordered table-ui align-middle">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 70px;">No</th>
                                    <th>Username</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th style="width: 120px;">Role</th>
                                    <th style="width: 130px;">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->nama_lengkap }}</td>
                                    <td>{{ $user->email ?? '-' }}</td>
                                    <td>
                                        <span class="badge-role {{ strtolower($user->role->jenis_user ?? 'user') === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                            {{ $user->role->jenis_user ?? 'User' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn-aksi btn-edit" title="Edit" data-user-id="{{ $user->id_user }}"><i class="bi bi-pencil-square"></i></a>
                                        <button type="button" class="btn-aksi btn-hapus ms-1" title="Hapus" data-user-id="{{ $user->id_user }}" data-user-name="{{ $user->nama_lengkap }}"><i class="bi bi-trash3"></i></button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center" style="padding: 40px; color: var(--muted);">
                                        <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3;"></i>
                                        <p class="mt-3 mb-0">Belum ada data user</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- ==================== FOOTER ==================== -->
        <div class="pad-page">
            <footer class="footer-ui">
                &copy; 2026 Pemerintah Provinsi Aceh - Dinas Komunikasi, Informatika dan Persandian
            </footer>
        </div>
    </div>

    <!-- NOTIFIKASI KONFIRMASI LOGOUT -->
    <div class="modal fade" id="konfirmasiLogout" tabindex="-1" role="dialog" aria-labelledby="konfirmasiLogoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form id="formLogout" method="POST" action="{{ route('logout') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-body text-center p-4 pb-2">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 56px; height: 56px; background-color: #fdecec;">
                            <i class="bi bi-box-arrow-right" aria-hidden="true" style="font-size: 24px; color: #dc3545;"></i>
                        </div>
                        <h5 class="modal-title mb-2" id="konfirmasiLogoutLabel" style="font-weight: 700; font-size: 17px; color: #1a1a2e;">Keluar dari Akun?</h5>
                        <p class="mb-0" style="font-size: 13px; color: #5a6577; line-height: 1.6;"> Anda akan keluar dari sesi ini dan perlu masuk kembali untuk melanjutkan.</p>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 gap-2">
                        <button type="button" class="btn btn-sm flex-fill" data-bs-dismiss="modal" style="background-color: #f0f2f5; color: #333; border: 1px solid #d0d0d0; font-weight: 600;">Batal</button>
                        <button type="submit" form="formLogout" class="btn btn-sm flex-fill" style="background-color: #dc3545; color: #ffffff; border: 1px solid #dc3545; font-weight: 600;">Ya, Logout</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- POPUP FORM EDIT USER -->
    <div class="modal fade" id="modalEditUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-edit-user">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditUser">
                        @csrf
                        @method('PUT')
                        
                        <div id="alertContainer"></div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-ui">Username</label>
                                <input type="text" name="username" id="edit_username" class="form-control">
                                <div class="form-text">Hanya huruf, angka, titik, garis bawah, dan tanda kurung.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-ui">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" id="edit_nama_lengkap" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-ui">Email</label>
                                <input type="email" name="email" id="edit_email" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-ui">Nomor Telepon</label>
                                <input type="tel" name="nomor_telepon" id="edit_nomor_telepon" class="form-control" placeholder="Opsional">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label-ui">Role</label>
                                <select name="id_role" id="edit_id_role" class="form-control">
                                    <option value="">Pilih Role</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-4 mb-2">
                            <button type="button" class="btn-reset-pwd" onclick="resetPassword()">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Password
                            </button>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-update" id="btnUpdateUser">
                                <i class="bi bi-check2-circle me-1"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- NOTIFIKASI KONFIRMASI HAPUS USER -->
    <div class="modal fade" id="konfirmasiHapusUser" tabindex="-1" role="dialog" aria-labelledby="konfirmasiHapusUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4 pb-2">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 56px; height: 56px; background-color: #fdecec;">
                        <i class="bi bi-trash3" aria-hidden="true" style="font-size: 24px; color: #dc3545;"></i>
                    </div>
                    <h5 class="modal-title mb-2" id="konfirmasiHapusUserLabel" style="font-weight: 700; font-size: 17px; color: #1a1a2e;">Yakin ingin menghapus user <span id="namaUserHapus"></span>?</h5>
                    <p class="mb-0" style="font-size: 13px; color: #5a6577; line-height: 1.6;">Data user yang dihapus tidak dapat dikembalikan.</p>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4 gap-2">
                    <button type="button" class="btn btn-sm flex-fill" data-bs-dismiss="modal" style="background-color: #f0f2f5; color: #333; border: 1px solid #d0d0d0; font-weight: 600;">Batal</button>
                    <button type="button" id="btnKonfirmasiHapus" class="btn btn-sm flex-fill" style="background-color: #dc3545; color: #ffffff; border: 1px solid #dc3545; font-weight: 600;">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- POPUP FORM TAMBAH USER -->
    <div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formTambahUser">
                        @csrf
                        
                        <div id="alertContainerTambah"></div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-ui">Username</label>
                                <input type="text" name="username" id="add_username" class="form-control">
                                <div class="form-text">Hanya huruf, angka, titik, garis bawah, dan tanda kurung.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-ui">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" id="add_nama_lengkap" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-ui">Email</label>
                                <input type="email" name="email" id="add_email" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-ui">Nomor Telepon</label>
                                <input type="tel" name="nomor_telepon" id="add_nomor_telepon" class="form-control" placeholder="Opsional">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-ui">Role</label>
                                <select name="id_role" id="add_id_role" class="form-control">
                                    <option value="">Pilih Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id_role }}">{{ $role->jenis_user }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-ui">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" id="add_password" name="password" class="form-control" placeholder="Minimal 8 karakter" oninput="cekKekuatanAdd(); cekKecocokanAdd();">
                                    <button type="button" class="btn btn-eye" onclick="togglePasswordAdd('add_password', this)" title="Tampilkan/sembunyikan">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="strength mt-2"><span id="strengthBarAdd"></span></div>
                                <div class="hint mt-1" id="strengthTextAdd">Kekuatan kata sandi</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-ui">Konfirmasi Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" id="add_password_confirmation" name="password_confirmation" class="form-control" placeholder="Masukkan ulang password" oninput="cekKecocokanAdd()">
                                    <button type="button" class="btn btn-eye" onclick="togglePasswordAdd('add_password_confirmation', this)" title="Tampilkan/sembunyikan">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="hint mt-2" id="matchTextAdd">&nbsp;</div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-teal" id="btnTambahUser">
                                <i class="bi bi-plus-circle me-1"></i> Tambah User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Data untuk dropdown role
        const roleOptions = @json($roles->pluck('jenis_user', 'id_role'));

        // Inisialisasi modal
        let editUserModal = null;
        let selectedUserId = null;

        // Tampilkan modal edit user
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                selectedUserId = this.getAttribute('data-user-id');
                fetchUser(selectedUserId);
            });
        });

        // Fetch data user dari server
        function fetchUser(userId) {
            fetch(`/manage-user/${userId}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateForm(data.user, data.roles);
                        editUserModal = new bootstrap.Modal(document.getElementById('modalEditUser'));
                        editUserModal.show();
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Gagal memuat data user.');
                });
        }

        // Populate form dengan data user
        function populateForm(user, roles) {
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_nama_lengkap').value = user.nama_lengkap;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_nomor_telepon').value = user.nomor_telepon || '';
            
            // Populate dropdown role
            const roleSelect = document.getElementById('edit_id_role');
            roleSelect.innerHTML = '<option value="">Pilih Role</option>';
            
            roles.forEach(role => {
                const option = document.createElement('option');
                option.value = role.id_role;
                option.textContent = role.jenis_user;
                if (role.id_role == user.id_role) {
                    option.selected = true;
                }
                roleSelect.appendChild(option);
            });
            
            // Clear alert
            document.getElementById('alertContainer').innerHTML = '';
        }

        // Handle form submit
        document.getElementById('formEditUser').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('_user_id', selectedUserId);
            
            fetch(`/manage-user/${selectedUserId}`, {
                method: 'PUT',
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        if (data.errors) {
                            displayErrors(data.errors);
                        } else {
                            alert('Gagal memperbarui data.');
                        }
                    });
                }
                return response.json().then(data => {
                    if (data.success) {
                        showSuccessNotification(data.message);
                        editUserModal.hide();
                        refreshUserTable();
                    }
                })
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        });

        // Tampilkan error validation
        function displayErrors(errors) {
            let alertHtml = '';
            
            if (errors.username) {
                alertHtml += `<div class="alert alert-danger alert-ui alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ${errors.username[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>`;
            }
            
            if (errors.email) {
                alertHtml += `<div class="alert alert-danger alert-ui alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ${errors.email[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>`;
            }
            
            if (errors.nama_lengkap) {
                alertHtml += `<div class="alert alert-danger alert-ui alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ${errors.nama_lengkap[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>`;
            }
            
            if (errors.nomor_telepon) {
                alertHtml += `<div class="alert alert-danger alert-ui alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ${errors.nomor_telepon[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>`;
            }
            
            if (errors.id_role) {
                alertHtml += `<div class="alert alert-danger alert-ui alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ${errors.id_role[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>`;
            }
            
            document.getElementById('alertContainer').innerHTML = alertHtml;
        }

        // Reset Password (placeholder)
        function resetPassword() {
            alert('Fitur reset password sedang dalam pengembangan.');
            // TODO: Implementasi reset password
        }

        // Close modal on escape key
        document.getElementById('modalEditUser').addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && editUserModal) {
                editUserModal.hide();
            }
        });

        // DESAIN NOTIFIKASI UPDATE USER
        function showSuccessNotification(message) {
            const alertHtml = `
                <div class="alert alert-success alert-ui alert-dismissible fade show d-flex align-items-center" role="alert" style="position: fixed; top: 90px; right: 20px; z-index: 1500; width: auto; max-width: 400px;">
                    <i class="bi bi-check-circle-fill me-2"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            `;

            const container = document.createElement('div');
            container.innerHTML = alertHtml;
            document.body.appendChild(container.firstElementChild);

            setTimeout(() => {
                const alert = document.querySelector('.alert-success');
                if (alert) alert.remove();
            }, 5000);
        }

        function refreshUserTable() {
            fetch(`/manage-user?sort_by=${currentSort}`)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableBody = doc.querySelector('table tbody');
                document.querySelector('table tbody').innerHTML = newTableBody.innerHTML;
                
                reattachEditDeleteHandlers();
            })
            .catch(err => {
                console.error('Error refreshing table:', err);
                location.reload();
            });
        }

        function reattachEditDeleteHandlers() {
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectedUserId = this.getAttribute('data-user-id');
                    fetchUser(selectedUserId);
                });
            });

            document.querySelectorAll('.btn-hapus').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectedUserIdDelete = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    document.getElementById('namaUserHapus').textContent = userName;
                    deleteUserModal = new bootstrap.Modal(document.getElementById('konfirmasiHapusUser'));
                    deleteUserModal.show();
                });
            });
        }

        // Handle delete user
        let selectedUserIdDelete = null;
        let deleteUserModal = null;

        document.querySelectorAll('.btn-hapus').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                selectedUserIdDelete = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                document.getElementById('namaUserHapus').textContent = userName;
                deleteUserModal = new bootstrap.Modal(document.getElementById('konfirmasiHapusUser'));
                deleteUserModal.show();
            });
        });

        document.getElementById('btnKonfirmasiHapus').addEventListener('click', function() {
            fetch(`/manage-user/${selectedUserIdDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        alert('Gagal menghapus user.');
                    });
                }
                return response.json().then(data => {
                    if (data.success) {
                        deleteUserModal.hide();
                        showSuccessNotification(data.message);
                        refreshUserTable();
                    }
                });
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        });

        // Handle tambah user
        let addUserModal = null;

        document.getElementById('formTambahUser').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/manage-user', {
                method: 'POST',
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        if (data.errors) {
                            displayErrorsTambah(data.errors);
                        } else {
                            alert('Gagal menambah user.');
                        }
                    });
                }
                return response.json().then(data => {
                    if (data.success) {
                        addUserModal.hide();
                        showSuccessNotification(data.message);
                        refreshUserTable();
                        document.getElementById('formTambahUser').reset();
                        document.getElementById('alertContainerTambah').innerHTML = '';
                        document.getElementById('strengthTextAdd').textContent = 'Kekuatan kata sandi';
                        document.getElementById('matchTextAdd').innerHTML = '&nbsp;';
                    }
                });
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        });

        function displayErrorsTambah(errors) {
            let alertHtml = '';
            
            if (errors.username) {
                alertHtml += `<div class="alert alert-danger alert-ui alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ${errors.username[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>`;
            }
            if (errors.nama_lengkap) {
                alertHtml += `<div class="alert alert-danger alert-ui alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ${errors.nama_lengkap[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>`;
            }
            if (errors.email) {
                alertHtml += `<div class="alert alert-danger alert-ui alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ${errors.email[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>`;
            }
            if (errors.nomor_telepon) {
                alertHtml += `<div class="alert alert-danger alert-ui alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ${errors.nomor_telepon[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>`;
            }
            if (errors.id_role) {
                alertHtml += `<div class="alert alert-danger alert-ui alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ${errors.id_role[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>`;
            }
            if (errors.password) {
                alertHtml += `<div class="alert alert-danger alert-ui alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ${errors.password[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>`;
            }
            if (errors.password_confirmation) {
                alertHtml += `<div class="alert alert-danger alert-ui alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ${errors.password_confirmation[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>`;
            }
            
            document.getElementById('alertContainerTambah').innerHTML = alertHtml;
        }

        function togglePasswordAdd(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            const hidden = input.type === 'password';
            
            input.type = hidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !hidden);
            icon.classList.toggle('bi-eye-slash', hidden);
        }

        function cekKekuatanAdd() {
            const v = document.getElementById('add_password').value;
            const bar = document.getElementById('strengthBarAdd');
            const txt = document.getElementById('strengthTextAdd');
            
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

        function cekKecocokanAdd() {
            const p = document.getElementById('add_password').value;
            const c = document.getElementById('add_password_confirmation').value;
            const el = document.getElementById('matchTextAdd');
            
            if (!c) { el.innerHTML = '&nbsp;'; el.style.color = ''; return; }
            if (p === c) {
                el.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Kata sandi cocok';
                el.style.color = '#0d9488';
            } else {
                el.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i>Kata sandi belum cocok';
                el.style.color = '#e5484d';
            }
        }

        // Inisialisasi modal tambah user saat page load
        document.addEventListener('DOMContentLoaded', function() {
            addUserModal = new bootstrap.Modal(document.getElementById('modalTambahUser'), { backdrop: 'static' });
        });

        // Re-attach saat modal ditutup
        document.getElementById('modalTambahUser').addEventListener('hidden.bs.modal', function() {
            document.getElementById('formTambahUser').reset();
            document.getElementById('alertContainerTambah').innerHTML = '';
        });

        // Filter user functionality
        let currentSort = '{{ $currentSort ?? "created_id_asc" }}';

        const sortLabels = {
            'created_id_asc': 'Waktu Dibuat',
            'nama_asc': 'Nama (A-Z)',
            'role_admin_first': 'Role'
        };

        // Initialize active filter indicator
        document.addEventListener('DOMContentLoaded', function() {
            updateFilterIndicator();
        });

        // Handle filter dropdown clicks
        document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const sortBy = this.getAttribute('data-sort');
                filterUserTable(sortBy);
            });
        });

        function updateFilterIndicator() {
            // Remove active class from all items
            document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to current filter
            const activeItem = document.querySelector(`[data-sort="${currentSort}"]`);
            if (activeItem) {
                activeItem.classList.add('active');
            }
            
            // Update badge on button
            const badge = document.getElementById('filterBadge');
            if (currentSort !== 'created_id_asc') {
                badge.textContent = sortLabels[currentSort] || '';
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        function filterUserTable(sortBy) {
            currentSort = sortBy;
            
            fetch(`/manage-user?sort_by=${sortBy}`)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTableBody = doc.querySelector('table tbody');
                    document.querySelector('table tbody').innerHTML = newTableBody.innerHTML;
                    
                    // Update filter indicator
                    updateFilterIndicator();
                    
                    // Re-attach event listeners untuk edit & delete
                    reattachEditDeleteHandlers();
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Gagal memfilter data.');
                });
        }

    </script>
</body>

</html>