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
                        <a href="#" class="btn btn-outline-ui d-inline-flex align-items-center gap-2">
                            Export Data User <i class="bi bi-download"></i>
                        </a>
                        <a href="#" class="btn btn-teal d-inline-flex align-items-center gap-2">
                            Tambah User <i class="bi bi-plus-lg"></i>
                        </a>
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
                                        <a href="#" class="btn-aksi btn-edit" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <a href="#" class="btn-aksi btn-hapus ms-1 title="Hapus"><i class="bi bi-trash3"></i></a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>