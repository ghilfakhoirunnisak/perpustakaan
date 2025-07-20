@extends('main.app')

@php $loginPage = true; @endphp

@section('content')
    <div class="container d-flex justify-content-center align-items-center">
        <div class="card shadow rounded-4 border-0 bg-white p-4 w-100" style="max-width: 420px;">
            <div class="text-center mb-3">
                <h3 class="fw-bold mb-1">Perpus<span class="text-primary">takaan</span></h3>
                <p class="text-muted">Masukkan kode OTP yang telah dikirim ke email Anda.</p>
            </div>

            <form id="otpForm">
                <div class="mb-3 text-center d-flex justify-content-between gap-2 justify-content-center">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" class="form-control text-center otp-input"
                            maxlength="1" inputmode="numeric" pattern="\d*"
                            style="width: 45px; height: 55px; font-size: 24px;" autocomplete="off" required>
                    @endfor
                </div>

                <button type="submit" id="submitOtpBtn" class="btn btn-primary w-100 rounded-pill">
                    <span class="default-text">Verifikasi</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">Belum menerima OTP?
                    <a href="#" id="resendOtp" class="text-primary text-decoration-none">Kirim ulang</a>
                    <span id="resendSpinner" class="spinner-border spinner-border-sm text-primary d-none"></span>
                </small>
            </div>

            <div class="mt-3 text-center">
                <span id="otpMessage" class="fw-semibold"></span>
            </div>
        </div>
    </div>

    <script>
        // Otomatis pindah ke input berikutnya
        document.querySelectorAll('.otp-input').forEach((input, idx, inputs) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && idx < inputs.length - 1) {
                    inputs[idx + 1].focus();
                }
            });
        });

        const otpForm = document.getElementById("otpForm");
        const otpInputs = document.querySelectorAll(".otp-input");
        const otpMessage = document.getElementById("otpMessage");
        const submitBtn = document.getElementById("submitOtpBtn");
        const spinner = submitBtn.querySelector(".spinner-border");
        const resendOtp = document.getElementById("resendOtp");
        const resendSpinner = document.getElementById("resendSpinner");

        function toggleButtonLoading(button, spinner, isLoading) {
            button.disabled = isLoading;
            spinner.classList.toggle('d-none', !isLoading);
            button.querySelector(".default-text").classList.toggle('d-none', isLoading);
        }

        otpForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            const otp = Array.from(otpInputs).map(input => input.value).join('');
            const id_user = localStorage.getItem("otp_user_id");

            if (otp.length !== 6 || !id_user) {
                otpMessage.textContent = "Kode OTP tidak lengkap atau ID tidak ditemukan.";
                otpMessage.style.color = 'red';
                return;
            }

            toggleButtonLoading(submitBtn, spinner, true);
            otpMessage.textContent = "";

            try {
                const response = await fetch('/api/otp/verifikasi', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ otp, id_user }),
                });

                const json = await response.json();
                otpMessage.textContent = json.message;
                otpMessage.style.color = response.ok ? 'green' : 'red';

                if (response.ok) {
                    localStorage.removeItem('otp_user_id');
                    setTimeout(() => window.location.href = "{{ route('login') }}", 1500);
                }
            } catch (error) {
                otpMessage.textContent = "Terjadi kesalahan koneksi.";
                otpMessage.style.color = 'red';
                console.error("DETAIL ERROR:", error); // Tambahan debug
            } finally {
                toggleButtonLoading(submitBtn, spinner, false);
            }
        });

        resendOtp.addEventListener("click", async function (e) {
            e.preventDefault();

            const id_user = localStorage.getItem("otp_user_id");
            if (!id_user) {
                otpMessage.textContent = "ID user tidak ditemukan.";
                otpMessage.style.color = 'red';
                return;
            }

            resendOtp.classList.add("disabled");
            resendSpinner.classList.remove("d-none");

            try {
                const user = await fetch(`/api/user/${id_user}`).then(res => res.json());

                if (user?.email) {
                    const response = await fetch('/api/otp/resend', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: user.email }),
                    });

                    const json = await response.json();
                    otpMessage.textContent = json.message;
                    otpMessage.style.color = response.ok ? 'green' : 'red';
                } else {
                    otpMessage.textContent = "Email user tidak ditemukan.";
                    otpMessage.style.color = 'red';
                }
            } catch (error) {
                otpMessage.textContent = "Gagal mengirim ulang OTP.";
                otpMessage.style.color = 'red';
            } finally {
                resendOtp.classList.remove("disabled");
                resendSpinner.classList.add("d-none");
            }
        });
    </script>
@endsection
