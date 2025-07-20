document.addEventListener('DOMContentLoaded', async () => {
    const currentPath = window.location.pathname;

    // Lewatkan validasi jika di halaman login/register/otp
    if (['/login', '/register', '/otp'].includes(currentPath)) {
        console.log('Lewat auth.js karena halaman:', currentPath);
        return;
    }

    const token = localStorage.getItem('auth_token');
    if (!token) return redirectToLogin();

    try {
        const res = await fetch('/api/profile', {
            headers: {
                Authorization: token,
                Accept: 'application/json'
            }
        });

        if (!res.ok) {
            localStorage.clear();
            return redirectToLogin();
        }

        const { user } = await res.json();
        const role = user.role?.nama_role || null;
        const name = user.nama || user.name || '-';

        // Simpan role dan nama jika belum
        localStorage.setItem('user_role', role);
        localStorage.setItem('user_name', name);

        // Jika akses /dashboard umum → redirect ke sesuai role
        if (currentPath === '/dashboard') {
            return redirectToDashboard(role);
        }

        // Cek jika user masuk ke halaman yang tidak sesuai rolenya
        if (
            (role === 'admin' && !currentPath.startsWith('/admin')) ||
            (role === 'verifikator' && !currentPath.startsWith('/verifikator')) ||
            (role === 'anggota' && !currentPath.startsWith('/anggota'))
        ) {
            return redirectToDashboard(role);
        }

    } catch (error) {
        console.error('Autentikasi gagal:', error);
        localStorage.clear();
        return redirectToLogin();
    }

    function redirectToDashboard(role) {
        const routeMap = {
            admin: '/admin/dashboard',
            verifikator: '/verifikator/dashboard',
            anggota: '/anggota/dashboard'
        };
        window.location.href = routeMap[role] || '/login';
    }

    function redirectToLogin() {
        window.location.href = '/login';
    }
});
