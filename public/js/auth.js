// public/js/auth.js

window.getUserProfile = async function () {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        localStorage.clear();
        window.location.href = '/login';
        return null;
    }

    try {
        const res = await fetch('/api/profile', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) throw new Error('Unauthorized');

        const data = await res.json();
        const user = data.user;

        localStorage.setItem('user_role', user.role);
        localStorage.setItem('user_name', user.nama);
        return user;
    } catch (error) {
        console.error(error);
        localStorage.clear();
        window.location.href = '/login';
        return null;
    }
};
