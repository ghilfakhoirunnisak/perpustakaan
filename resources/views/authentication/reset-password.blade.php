@extends('main.app')

@php $loginPage = true; @endphp

@section('content')
    <div class="container d-flex justify-content-center align-items-center">
        <div class="card shadow rounded-4 border-0 bg-white p-4 w-100" style="max-width: 420px;">
            <div class="text-center mb-3">
                <h3 class="fw-bold mb-1">Perpus<span class="text-primary">takaan</span></h3>
                <p class="text-muted">Masukkan password baru Anda</p>
            </div>

            <form id="resetForm">
                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control rounded-end-pill" id="password" required minlength="8">
                    </div>
                    <div class="invalid-feedback" id="passwordError"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control rounded-end-pill" id="confirmPassword" required
                            minlength="8">
                    </div>
                    <div class="invalid-feedback" id="confirmError"></div>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill" id="submitBtn">
                    <span id="btnText"><i class="fa fa-sync-alt me-1"></i> Reset Password</span>
                    <span id="btnLoading" class="d-none"><i class="fa fa-spinner fa-spin me-1"></i> Memproses...</span>
                </button>
            </form>

            <div class="text-center mt-3">
                <small><a href="{{ route('login') }}">Kembali ke login</a></small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const form = document.getElementById('resetForm');
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirmPassword');
        const passwordError = document.getElementById('passwordError');
        const confirmError = document.getElementById('confirmError');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            passwordError.textContent = '';
            confirmError.textContent = '';
            passwordInput.classList.remove('is-invalid');
            confirmInput.classList.remove('is-invalid');

            if (passwordInput.value !== confirmInput.value) {
                confirmInput.classList.add('is-invalid');
                confirmError.textContent = 'Konfirmasi password tidak cocok.';
                return;
            }

            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            submitBtn.disabled = true;

            const id_user = localStorage.getItem('reset_user_id');

            if (!id_user) {
                await Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'ID pengguna tidak ditemukan. Silakan ulangi proses.'
                });
                return;
            }

            try {
                const res = await fetch('/api/forgot-password/reset', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id_user: id_user,
                        password: passwordInput.value,
                        password_confirmation: confirmInput.value
                    })
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.message || 'Reset password gagal.');
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                localStorage.removeItem('reset_user_id');
                window.location.href = '{{ route('login') }}';

            } catch (err) {
                await Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message
                });
            } finally {
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
                submitBtn.disabled = false;
            }
        });
    </script>
@endsection
