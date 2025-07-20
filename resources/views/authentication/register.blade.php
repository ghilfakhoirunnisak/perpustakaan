@extends('main.app')

@php $loginPage = true; @endphp

@section('content')
    <div class="container d-flex justify-content-center align-items-center">
        <div class="card shadow rounded-4 border-0 bg-white p-4 w-100" style="max-width: 420px;">
            <div class="text-center mb-4">
                <h3 class="fw-bold mb-1">Perpus<span class="text-primary">takaan</span></h3>
                <p class="text-muted">Silakan daftar untuk membuat akun</p>
            </div>

            <form id="registerForm">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control rounded-end-pill" id="nama" name="nama" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="telp" class="form-label">Nomor Telepon</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-phone"></i></span>
                        <input type="text" class="form-control rounded-end-pill" id="telp" name="telp" maxlength="13" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-envelope"></i></span>
                        <input type="email" class="form-control rounded-end-pill" id="email" name="email" required>
                    </div>
                    <div class="invalid-feedback" id="emailError"></div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control rounded-end-pill" id="password" name="password" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control rounded-end-pill" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill" id="registerButton">
                    <span class="default-text"><i class="fa fa-user-plus me-1"></i> Daftar</span>
                    <span class="spinner-text d-none"><i class="fa fa-spinner fa-spin me-1"></i> Memproses...</span>
                </button>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">Sudah punya akun? </small>
                <a href="{{ route('login') }}" class="text-primary text-decoration-none">Masuk Sekarang</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById("registerForm");
            const button = document.getElementById("registerButton");
            const defaultText = button.querySelector(".default-text");
            const spinnerText = button.querySelector(".spinner-text");

            form.addEventListener("submit", async function (e) {
                e.preventDefault();

                const data = new FormData(form);
                const emailInput = document.getElementById("email");
                const emailError = document.getElementById("emailError");

                emailInput.classList.remove('is-invalid');
                emailError.textContent = "";

                // Show loading
                defaultText.classList.add("d-none");
                spinnerText.classList.remove("d-none");
                button.disabled = true;

                try {
                    const response = await fetch('/api/register', {
                        method: 'POST',
                        body: data
                    });

                    const json = await response.json();

                    if (response.ok) {
                        localStorage.setItem("otp_user_id", json.data.id_user);
                        window.location.href = '{{ route('otp') }}';
                    } else {
                        if (json.errors?.email) {
                            emailInput.classList.add('is-invalid');
                            emailError.textContent = json.errors.email[0];
                        }
                        if (json.errors?.password) alert(json.errors.password[0]);
                        if (json.errors?.nama) alert(json.errors.nama[0]);
                        if (json.errors?.telp) alert(json.errors.telp[0]);
                        if (json.message) alert(json.message);
                    }
                } catch (err) {
                    alert("Terjadi kesalahan saat mengirim data.");
                    console.error(err);
                } finally {
                    // Reset loading state
                    defaultText.classList.remove("d-none");
                    spinnerText.classList.add("d-none");
                    button.disabled = false;
                }
            });
        });
    </script>
@endsection
