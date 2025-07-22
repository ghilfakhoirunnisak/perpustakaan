document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn = document.getElementById('logoutBtn');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', async (e) => {
            e.preventDefault();

            // Konfirmasi logout dengan SweetAlert
            const confirm = await Swal.fire({
                title: 'Keluar?',
                text: 'Apakah Anda yakin ingin logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            });

            if (!confirm.isConfirmed) return;

            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            try {
                const res = await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });

                localStorage.clear();

                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil logout',
                    text: 'Anda telah keluar dari sistem.',
                    timer: 1500,
                    showConfirmButton: false
                });

                window.location.href = '/login';
            } catch (err) {
                console.error('Logout error:', err);
                localStorage.clear();
                window.location.href = '/login';
            }
        });
    }
});
