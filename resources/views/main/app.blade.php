<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Perpustakaan 2025</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">

    <!-- Link Fonts Awesome -->
    <script src="https://kit.fontawesome.com/b32269b034.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
    <!-- Navbar -->
    <nav id="mainNavbar" class="navbar navbar-expand-lg navbar-light bg-transparent py-3">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand fw-bold" href="/">
                Perpus<span class="text-primary">takaan</span>
            </a>

            @if (!isset($loginPage) || !$loginPage)
                <!-- Hamburger Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Menu -->
                <div class="collapse navbar-collapse" id="navbarMenu">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#koleksi">Koleksi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#layanan">Layanan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact</a>
                        </li>
                    </ul>

                    <!-- Login Button -->
                    <a href="{{ route('login') }}" class="btn btn-primary ms-lg-3 mt-3 mt-lg-0">
                        Login <i class="fas fa-user ms-1"></i>
                    </a>
                </div>
            @endif
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Konten -->
    <main class="mt-5 py-3">
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>
    <!-- Konten End -->

     @if (!isset($loginPage) || !$loginPage)
        <!-- Footer -->
        <footer class="bg-primary text-white py-3 mt-5">
            <div class="container text-center">
                <p class="mb-1">© {{ date('Y') }} Perpustakaan</p>
                <p class="small mb-0"> ❤️ : Vousmevoyez</p>
            </div>
        </footer>
        <!-- Footer End -->
    @endif

</body>
</html>
