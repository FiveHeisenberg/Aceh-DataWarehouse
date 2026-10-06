<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aceh Data Warehouse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    @stack('styles')

    <style>
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
            background-color: #e8f5f0;
            color: #0d9488;
        }

        .modal { z-index: 1200; }
        .modal-backdrop { z-index: 1190; }

        /* NOTIFIKASI "LENGKAPI DATA DIRI" */
        .toast-container-adw {
            position: fixed;
            top: 86px;
            right: 20px;
            z-index: 1200;
        }

        .toast-adw {
            opacity: 0;
            transition: opacity .35s ease-in-out;
            background-color: #fef3c7;
            border-color: #fcd34d;
        }

        .toast-adw.show {
            opacity: 1;
        }

        .toast-adw .toast-header {
            background-color: #fef3c7;
            border-bottom-color: #fcd34d;
        }

        /* ANIMASI FADE-OUT NOTIFIKASI */
        .toast-adw:not(.show) {
            display: block;
            pointer-events: none;
        }

        @media (max-width: 575.98px) {
            .toast-container-adw { left: 20px; right: 20px; width: auto; }
        }

        /* Active state untuk sidebar menu */
        .sidebar .text-decoration-none.active {
            background-color: #e8f5f0 !important;
            color: #0d9488 !important;
            font-weight: 600;
            border-radius: 6px;
        }

        /* Putar chevron saat submenu terbuka (Bootstrap Collapse) */
        .sidebar-menu-item > a .chevron-icon {
            transition: transform .3s ease;
        }

        .sidebar-menu-item > a[aria-expanded="true"] .chevron-icon {
            transform: rotate(90deg);
        }

    </style>
</head>

<body style="background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <div class="d-flex flex-column" style="min-height: 100vh;">

        <!-- ==================== TOP NAVBAR ==================== -->
        <nav class="d-flex align-items-center px-4" style="height: 70px; background-color: #ffffff; border-bottom: 1px solid #e0e4f0; position: fixed; top: 0; left: 0; right: 0; z-index: 1100;">

            <!-- Logo -->
            <div class="d-flex align-items-center" style="width: 300px; flex-shrink: 0;">
                <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background-color: #f0f0f0; border: 2px solid #d0d0d0; margin-right: 10px; flex-shrink: 0;">
                    <span style="font-weight: 800; font-size: 16px; color: #1a1a2e;">A</span>
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 14px; color: #1a1a2e; line-height: 1.2;">Aceh Data<br>Warehouse</div>
                </div>
            </div>

            <!-- Back Button + Page Title -->
            <div class="d-none align-items-center gap-2" id="backButtonGroup">
                <a href="#" class="icon-btn" title="Kembali" 
                style="color: #1a1a2e; font-size: 22px; width: 40px; height: 40px; 
                        display: inline-flex; align-items: center; justify-content: center; 
                        border-radius: 50%; transition: background 0.15s, color 0.15s;"
                onmouseover="this.style.backgroundColor='#e8f5f0'; this.style.color='#0d9488';"
                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#1a1a2e';"
                onclick="history.back(); return false;">
                    <i class="bi bi-chevron-left"></i>
                </a>
                <span id="pageTitle" style="font-weight: 500; font-size: 16px; color: #1a1a2e;"></span>
            </div>

            <!-- Back + Top Nav -->
            <div class="d-flex align-items-center">
                <a href="/" class="text-decoration-none me-4" style="color: #1a1a2e; font-weight: 600; font-size: 15px;">Home</a>
                <a href="#" class="text-decoration-none" style="color: #5a6577; font-weight: 500; font-size: 15px;">About</a>
            </div>

            <!-- Spacer -->
            <div class="flex-grow-1"></div>

            <!-- Icons -->
            <div class="d-flex align-items-center" style="gap: 18px;">
                <i class="bi bi-bell nav-icon" style="font-size: 19px; color: #5a6577;"></i>
                <i class="bi bi-gear nav-icon" style="font-size: 19px; color: #5a6577;"></i>
                <div class="dropdown">
                    <button class="btn p-0 border-0 bg-transparent dropdown-toggle profile-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu akun">
                        <span class="d-flex align-items-center justify-content-center rounded-circle" style="width: 34px; height: 34px; background-color: #eef2f9;">
                            <i class="bi bi-person-fill" style="font-size: 18px; color: #5a6577;"></i>
                        </span>
                    </button>

                    @php($user = auth()->user())
                    @php($adminRoleId = 1)
                    <ul class="dropdown-menu dropdown-menu-end profile-menu">
                        <li class="px-3 pt-1 pb-2">
                            <div style="font-size: 13px; font-weight: 700; color: #1a1a2e;">{{ $user->nama_lengkap ?? $user->username ?? 'Administrator' }}</div>
                            <div style="font-size: 11px; color: #8892a4;">{{ $user?->email ?: '-' }}</div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>

                        <li>
                            <a href="{{ route('profile') }}" class="dropdown-item d-flex align-items-center">
                                <i class="bi bi-person me-2"></i> Profile
                            </a>
                        </li>

                        @if($user->id_role === $adminRoleId)
                        <li>
                            <a href="{{ route('manage-user') }}" class="dropdown-item d-flex align-items-center">
                                <i class="bi bi-people me-2"></i> Manajemen User
                            </a>
                        </li>
                        @endif

                        @if($user->id_role === $adminRoleId)
                        <li>
                            <a href="http://192.168.222.152:8080/" target="blank" class="dropdown-item d-flex align-items-center">
                                <i class="bi bi-arrow-repeat me-2"></i> ETL
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        @endif

                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center w-100" style="color: #dc3545;" data-bs-toggle="modal" data-bs-target='#konfirmasiLogout'>
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="d-flex flex-grow-1" style="margin-top: 70px;">

            <!-- ==================== SIDEBAR ==================== -->
            <div class="p-3 sidebar" id="sidebarMenu" style="width: 260px; background-color: #ffffff; border-right: 1px solid #e0e0e0; position: fixed; top: 70px; left: 0; bottom: 0; overflow-y: auto; z-index: 1000;">

                <!-- Penduduk -->
                <div class="mb-1 sidebar-menu-item">
                    <a href="#submenuPenduduk" data-bs-toggle="collapse" role="button"
                       aria-expanded="{{ request()->routeIs('penduduk.*') ? 'true' : 'false' }}"
                       aria-controls="submenuPenduduk"
                       class="d-flex align-items-center justify-content-between text-decoration-none p-2 rounded" style="color: #333;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-people-fill me-2" style="font-size: 18px;"></i>
                            <span style="font-weight: 600; font-size: 14px;">Penduduk</span>
                        </div>
                        <i class="bi bi-chevron-right chevron-icon" style="font-size: 14px;"></i>
                    </a>
                    <div class="collapse ms-4 mt-1 {{ request()->routeIs('penduduk.*') ? 'show' : '' }}" id="submenuPenduduk" data-bs-parent="#sidebarMenu">
                        <a href="{{ route('penduduk.jumlah_penduduk') }}" class="d-block text-decoration-none py-1 px-2 {{ request()->routeIs('penduduk.jumlah_penduduk') ? 'active' : '' }}" style="font-size: 13px; color: #555;">Jumlah Penduduk</a>
                        <a href="{{ route('penduduk.kartu_keluarga') }}" class="d-block text-decoration-none py-2 px-2 {{ request()->routeIs('penduduk.kartu_keluarga') ? 'active' : '' }}" style="font-size: 13px; color: #555;">Kartu Keluarga</a>
                    </div>
                </div>

                <!-- Pendapatan Daerah -->
                <div class="mb-1 sidebar-menu-item">
                    <a href="#submenuDispenda" data-bs-toggle="collapse" role="button"
                       aria-expanded="{{ request()->routeIs('dispenda.*') ? 'true' : 'false' }}"
                       aria-controls="submenuDispenda"
                       class="d-flex align-items-center justify-content-between text-decoration-none p-2 rounded" style="color: #333;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bank me-2" style="font-size: 18px;"></i>
                            <span style="font-weight: 600; font-size: 14px;">Pendapatan Daerah</span>
                        </div>
                        <i class="bi bi-chevron-right chevron-icon" style="font-size: 14px;"></i>
                    </a>
                    <div class="collapse ms-4 mt-1 {{ request()->routeIs('dispenda.*') ? 'show' : '' }}" id="submenuDispenda" data-bs-parent="#sidebarMenu">
                        <a href="{{ route('dispenda.dashboard') }}" class="d-block text-decoration-none py-1 px-2 {{ request()->routeIs('dispenda.dashboard') ? 'active' : '' }}" style="font-size: 13px; color: #555;">Ringkasan Pendapatan</a>
                        <a href="{{ route('dispenda.tagihan') }}" class="d-block text-decoration-none py-1 px-2 {{ request()->routeIs('dispenda.tagihan') ? 'active' : '' }}" style="font-size: 13px; color: #555;">Data Tagihan</a>
                        <a href="{{ route('dispenda.objek-pajak') }}" class="d-block text-decoration-none py-1 px-2 {{ request()->routeIs('dispenda.objek-pajak') ? 'active' : '' }}" style="font-size: 13px; color: #555;">Objek Pajak</a>
                    </div>
                </div>
            </div>

            <!-- ==================== MAIN CONTENT ==================== -->
            <div class="flex-grow-1 main-content" style="margin-left: 260px;">

                <!-- Content Area -->
                <div class="p-4">
                    @yield('content')
                </div>
            </div>
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

    <!-- NOTIFIKASI "LENGKAPI DATA DIRI" -->
    @php($notifLengkapiDiri = session()->pull('notif_lengkapi_diri', false))

    @if ($notifLengkapiDiri)
    <div class="toast-container toast-container-adw">
        <div id="notifLengkapiDiri" class="toast toast-adw" role="alert" aria-live="polite" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-person-exclamation me-2" style="color: #b45309;"></i>
                <strong class="me-auto" style="color: #1a1a2e;">Data Diri Belum Lengkap</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
            <div class="toast-body" style="color: #5a6577;">Silahkan Lengkapi data diri anda</div>
        </div>
    </div>
    @endif

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/landing.js') }}"></script>

    <script>
        // Toast notification
        const toastLengkapiDiri = document.getElementById('notifLengkapiDiri');
        if (toastLengkapiDiri) {
            bootstrap.Toast.getOrCreateInstance(toastLengkapiDiri, {
                delay: 2000,
                autohide: true
            }).show();
        }

        // Back Button Logic
        document.addEventListener('DOMContentLoaded', function () {
            const backButtonGroup = document.getElementById('backButtonGroup');
            const pageTitle = document.getElementById('pageTitle');

            // Mapping route ke page title
            const routeTitles = {
                'penduduk.jumlah_penduduk': 'Jumlah Penduduk',
                'penduduk.kartu_keluarga': 'Kartu Keluarga',
                'dispenda.dashboard': 'Ringkasan Pendapatan',
                'dispenda.tagihan': 'Data Tagihan',
                'dispenda.objek-pajak': 'Objek Pajak',
            };

            // Deteksi current route
            let currentRoute = document.body.getAttribute('data-route') || '';
            if (!currentRoute) {
                const path = window.location.pathname;
                if (path === '/' || path === '/index' || path === '/index.php') {
                    currentRoute = 'index';
                } else if (path.includes('jumlah-penduduk')) {
                    currentRoute = 'penduduk.jumlah_penduduk';
                } else if (path.includes('kartu-keluarga')) {
                    currentRoute = 'penduduk.kartu_keluarga';
                } else if (path.includes('dispenda') && path.includes('dashboard')) {
                    currentRoute = 'dispenda.dashboard';
                } else if (path.includes('dispenda') && path.includes('tagihan')) {
                    currentRoute = 'dispenda.tagihan';
                } else if (path.includes('dispenda') && path.includes('objek-pajak')) {
                    currentRoute = 'dispenda.objek-pajak';
                }
            }

            const path = window.location.pathname;
            const isIndexPage = currentRoute === '' || currentRoute === 'index' ||
                path === '/' || path === '/index' || path === '/index.php';

            // Halaman asal: harus dari aplikasi yang sama dan bukan dari login
            const ref = document.referrer;
            let cameFromApp = false;
            if (ref) {
                const refUrl = new URL(ref);
                cameFromApp = refUrl.origin === window.location.origin
                    && !refUrl.pathname.includes('/login');
            }

            // Tampilkan tombol back hanya jika bukan index dan datang dari halaman lain di aplikasi
            if (!isIndexPage && cameFromApp) {
                backButtonGroup.classList.remove('d-none');
                backButtonGroup.classList.add('d-flex');
                pageTitle.textContent = routeTitles[currentRoute] || 'Dashboard';
            }
        });
    </script>

    @stack('scripts')
</body>

</html>