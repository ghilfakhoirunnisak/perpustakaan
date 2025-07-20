@extends('main.app')

@php $loginPage = true; @endphp

@section('content')
    <div class="container d-flex justify-content-center align-items-center">
        <div class="card shadow rounded-4 border-0 bg-white p-4 w-100" style="max-width: 420px;">
            <div class="text-center mb-3">
                <h3 class="fw-bold mb-1">Perpus<span class="text-primary">takaan</span></h3>
                <p class="text-muted">Reset Password</p>
            </div>

            <form id="forgotForm">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa fa-envelope"></i></span>
                        <input type="email" class="form-control rounded-end-pill" id="email" required>
                    </div>
                    <div class="invalid-feedback" id="emailError"></div>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill" id="submitBtn">
                    <span id="btnText"><i class="fa fa-paper-plane me-1"></i> Kirim OTP</span>
                    <span id="btnLoading" class="d-none"><i class="fa fa-spinner fa-spin me-1"></i> Mengirim...</span>
                </button>
            </form>

            <div class="text-center mt-3">
                <small><a href="{{ route('login') }}">Kembali ke login</a></small>
            </div>
        </div>
    </div>

    {{-- SweetAlert & FontAwesome --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <script>
        const form = document.getElementById('forgotForm');
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            emailInput.classList.remove('is-invalid');
            emailError.textContent = '';
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            submitBtn.disabled = true;

            try {
                const res = await fetch('/api/forgot-password/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email: emailInput.value })
                });

                const data = await res.json();

                if (!res.ok) {
                    if (data.errors?.email) {
                        emailInput.classList.add('is-invalid');
                        emailError.textContent = data.errors.email[0];
                    }
                    throw new Error(data.message || 'Gagal mengirim OTP.');
                }

                // Simpan ID user ke localStorage untuk halaman verifikasi OTP
                if (data.user?.id_user) {
                    localStorage.setItem('reset_user_id', data.user.id_user);
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                window.location.href = '{{ route('verifikasi-otp') }}';

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
