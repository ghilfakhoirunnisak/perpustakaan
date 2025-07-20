@extends('main.app')

@php $loginPage = true; @endphp

@section('content')
    <div class="container d-flex justify-content-center align-items-center">
        <div class="card shadow rounded-4 border-0 bg-white p-4 w-100" style="max-width: 420px;">
            <div class="text-center mb-3">
                <h3 class="fw-bold mb-1">Perpus<span class="text-primary">takaan</span></h3>
                <p class="text-muted">Silakan login untuk melanjutkan</p>
            </div>

            <form id="loginForm">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-envelope"></i></span>
                        <input type="email" class="form-control rounded-end-pill" id="email" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control rounded-end-pill" id="password" required>
                    </div>
                </div>

                <div class="text-end mb-3">
                    <a href="{{ route('forgot-password') }}" class="text-decoration-none small text-primary">
                        Lupa password?
                    </a>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill" id="loginButton">
                    <span class="default-text"><i class="fa fa-sign-in-alt me-1"></i> Login</span>
                    <span class="spinner-text d-none"><i class="fa fa-spinner fa-spin me-1"></i> Memproses...</span>
                </button>
            </form>

            <div class="text-center mt-3">
                <small>Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a></small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const loginBtn = document.getElementById('loginButton');
            const defaultText = loginBtn.querySelector('.default-text');
            const spinnerText = loginBtn.querySelector('.spinner-text');

            loginBtn.disabled = true;
            defaultText.classList.add('d-none');
            spinnerText.classList.remove('d-none');

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const result = await response.json();

                if (!response.ok) {
                    // --- MODIFIKASI DIMULAI DI SINI ---
                    // Tangani jika ada redirect_to dalam respons error (misal: untuk verifikasi OTP)
                    if (result.redirect_to) {
                        await Swal.fire({
                            icon: 'error',
                            title: 'Login Gagal',
                            text: result.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = result.redirect_to; // Langsung redirect
                        });
                        return; // Penting: Hentikan eksekusi setelah redirect
                    } else {
                        // Jika tidak ada redirect_to, lempar error seperti biasa
                        throw new Error(result.message || 'Login gagal.');
                    }
                    // --- MODIFIKASI BERAKHIR DI SINI ---
                }

                const token = result.data.token;
                const user = result.data.user;
                const role = user.role?.nama_role || null;
                const name = user.nama || user.name || 'Pengguna';

                // Simpan ke localStorage
                localStorage.setItem('auth_token', token);
                localStorage.setItem('user_role', role);
                localStorage.setItem('user_name', name);

                await Swal.fire({
                    icon: 'success',
                    title: 'Selamat Datang!',
                    text: 'Halo, ' + name,
                    timer: 1500,
                    showConfirmButton: false
                });

                // Redirect berdasarkan role
                const redirectMap = {
                    admin: '/admin/dashboard',
                    verifikator: '/verifikator/dashboard',
                    anggota: '/anggota/dashboard'
                };

                if (!role || !redirectMap[role]) {
                    await Swal.fire({
                        icon: 'error',
                        title: 'Gagal Redirect',
                        text: 'Role pengguna tidak dikenali.'
                    });
                    return;
                }

                window.location.href = redirectMap[role];

            } catch (error) {
                await Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: error.message
                });
            } finally {
                loginBtn.disabled = false;
                defaultText.classList.remove('d-none');
                spinnerText.classList.add('d-none');
            }
        });
    </script>
@endsection
