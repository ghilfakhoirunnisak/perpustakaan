<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Dashboard - Admin | Perpustakaan 2025</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('template/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/css/core.css') }}"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('template/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Helpers -->
    <script src="{{ asset('template/assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('template/assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="index.html" class="app-brand-link">
                        <span class="app-brand-logo demo">
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">Perpustakaan</span>
                    </a>

                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Analytics">Dashboard</div>
                        </a>
                    </li>

                    <!-- Data Role, User, Verifikator -->
                    @php
                        $isActive =
                            request()->routeIs('admin.role.*') ||
                            request()->routeIs('admin.user.*') ||
                            request()->routeIs('admin.verifikator.*');
                    @endphp

                    <li class="menu-item {{ $isActive ? 'active open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-group"></i>
                            <div data-i18n="Account Settings">Account Settings</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item {{ request()->routeIs('admin.role.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.role.index') }}" class="menu-link">
                                    <div data-i18n="Basic Inputs">Data Role</div>
                                </a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.user.index') }}" class="menu-link">
                                    <div data-i18n="Input groups">Data User</div>
                                </a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('admin.verifikator.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.verifikator.index') }}" class="menu-link">
                                    <div data-i18n="Others">Data Verifikator</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    @php
                        $bukuActive =
                            request()->routeIs('admin.penulisbuku.*') ||
                            request()->routeIs('admin.penerbitbuku.*') ||
                            request()->routeIs('admin.buku.*');
                    @endphp

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Buku</span>
                    </li>

                    <li class="menu-item {{ $bukuActive ? 'active open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle {{ $bukuActive ? 'active' : '' }}">
                            <i class="menu-icon tf-icons bx bx-book"></i>
                            <div data-i18n="Authentications">Data Buku</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item {{ request()->routeIs('admin.penulisbuku.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.penulisbuku.index') }}" class="menu-link">
                                    <div data-i18n="Basic">Data Penulis Buku</div>
                                </a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('admin.penerbitbuku.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.penerbitbuku.index') }}" class="menu-link">
                                    <div data-i18n="Basic">Data Penerbit Buku</div>
                                </a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('admin.buku.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.buku.index') }}" class="menu-link">
                                    <div data-i18n="Basic">Data Buku</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.pengajuanbuku.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pengajuanbuku.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bxs-file-export"></i>
                            <div data-i18n="Error">Pengajuan Buku</div>
                        </a>
                    </li>

                    <!-- Peminjaman Buku -->
                    <li class="menu-item">
                        <a href="cards-basic.html" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-task"></i>
                            <div data-i18n="Basic">Peminjaman Buku</div>
                        </a>
                    </li>

                    <!-- Fasilitas -->
                    <li class="menu-header small text-uppercase"><span class="menu-header-text">Fasilitas</span></li>
                    <!-- Fasilitas -->
                    <li class="menu-item {{ request()->routeIs('admin.fasilitas.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.fasilitas.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-building"></i>
                            <div data-i18n="Boxicons">Data Fasilitas</div>
                        </a>
                    </li>

                    <!-- Reservasi Fasilitas -->
                    <li class="menu-item {{ Route::is('admin.reservasifasilitas.*') ? 'active' : '' }}"">
                        <a href="{{ route('admin.reservasifasilitas.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-file"></i>
                            <div data-i18n="Tables">Reservasi Fasilitas</div>
                        </a>
                    </li>

                    @php
                        $logActive = request()->routeIs('admin.logs.*');
                    @endphp

                    <li class="menu-header small text-uppercase"><span class="menu-header-text">Log</span></li>

                    <li class="menu-item {{ $logActive ? 'active open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle {{ $logActive ? 'active' : '' }}">
                            <i class="menu-icon tf-icons bx bx-code"></i>
                            <div data-i18n="Layouts">Log</div>
                        </a>

                        <ul class="menu-sub">
                            <li class="menu-item {{ request()->routeIs('admin.logs.logapproval') ? 'active' : '' }}">
                                <a href="{{ route('admin.logs.logapproval') }}" class="menu-link">
                                    <div data-i18n="Progress">Approval Fasilitas</div>
                                </a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('admin.logs.logapprovalbuku') ? 'active' : '' }}">
                                <a href="{{ route('admin.logs.logapprovalbuku') }}" class="menu-link">
                                    <div data-i18n="Spinners">Approval Buku</div>
                                </a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('admin.logs.logactivity') ? 'active' : '' }}">
                                <a href="{{ route('admin.logs.logactivity') }}" class="menu-link">
                                    <div data-i18n="Without navbar">Log Activity</div>
                                </a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('admin.logs.logdatabase') ? 'active' : '' }}">
                                <a href="{{ route('admin.logs.logdatabase') }}" class="menu-link">
                                    <div data-i18n="Container">Log Database</div>
                                </a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('admin.logs.logerror') ? 'active' : '' }}">
                                <a href="{{ route('admin.logs.logerror') }}" class="menu-link">
                                    <div data-i18n="Fluid">Log Error</div>
                                </a>
                            </li>

                        </ul>
                    </li>
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class="bx bx-search fs-4 lh-0"></i>
                                <input type="text" class="form-control border-0 shadow-none"
                                    placeholder="Search..." aria-label="Search..." />
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Place this tag where you want the button to render. -->
                            <li class="nav-item lh-1 me-3">
                                <a class="github-button"
                                    href="https://github.com/themeselection/sneat-html-admin-template-free"
                                    data-icon="octicon-star" data-size="large" data-show-count="true"
                                    aria-label="Star themeselection/sneat-html-admin-template-free on GitHub">Star</a>
                            </li>

                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <span id="userAvatar"
                                            class="avatar-initials w-px-40 h-40px rounded-circle bg-secondary text-white fw-bold d-flex align-items-center justify-content-center">G</span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <span id="userAvatarDropdown"
                                                            class="avatar-initials w-px-40 h-40px rounded-circle bg-secondary text-white fw-bold d-flex align-items-center justify-content-center">G</span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span id="namaUser" class="fw-semibold d-block">Memuat...</span>
                                                    <small id="roleUser" class="text-muted">Memuat...</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bx bx-cog me-2"></i>
                                            <span class="align-middle">Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <button class="dropdown-item border-0 bg-transparent" id="logoutBtn"
                                            type="button">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Log Out</span>
                                        </button>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    @yield('content')
                    <!-- / Content -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('template/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('template/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('template/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('template/assets/js/dashboards-analytics.js') }}"></script>

    <script src="{{ asset('js/interceptor.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    <script src="{{ asset('js/profile.js') }}"></script>
    <script src="{{ asset('js/logout.js') }}"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>
