document.addEventListener('DOMContentLoaded', function () {
    // --- Selektor Element ---
    const namaUserElement = document.querySelector('#namaUser');
    const roleUserElement = document.querySelector('#roleUser');
    const userAvatarElement = document.querySelector('#userAvatar');
    const userAvatarDropdownElement = document.querySelector('#userAvatarDropdown');
    const namaUserGreeting = document.querySelector('#namaUserGreeting'); // Element ini hanya ada di dashboard

    // --- Ambil Token dari localStorage ---
    const token = localStorage.getItem('auth_token');

    // --- Fungsi untuk mendapatkan inisial ---
    function getInitials(name) {
        if (!name) return '';
        const nameParts = name.split(' ');
        let initials = '';
        if (nameParts.length >= 1) {
            initials += nameParts[0].charAt(0);
        }
        if (nameParts.length > 1) {
            initials += nameParts[nameParts.length - 1].charAt(0);
        }
        return initials.toUpperCase();
    }

    if (!token) {
        if (namaUserElement) namaUserElement.textContent = 'Guest';
        if (roleUserElement) roleUserElement.textContent = 'Tidak Login';
        if (namaUserGreeting) namaUserGreeting.textContent = 'Pengguna'; // Hanya perbarui jika elemen ada
        if (userAvatarElement) userAvatarElement.textContent = 'G';
        if (userAvatarDropdownElement) userAvatarDropdownElement.textContent = 'G';
        console.warn('Peringatan: Token tidak ditemukan. Menampilkan profil Guest.');
        return;
    }

    // --- Lakukan Panggilan API ---
    fetch('/api/profile', {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Gagal mengambil data profil. Status: ' + res.status);
        }
        return res.json();
    })
    .then(data => {
        if (data.success && data.user) {
            const user = data.user;
            const name = user.nama || user.name || 'Pengguna';
            const role = user.role || 'Role Tidak Tersedia';
            const initials = getInitials(name);

            // Perbarui setiap elemen hanya jika elemen tersebut ditemukan
            if (namaUserElement) namaUserElement.textContent = name;
            if (roleUserElement) roleUserElement.textContent = role;
            if (namaUserGreeting) namaUserGreeting.textContent = name;
            if (userAvatarElement) userAvatarElement.textContent = initials;
            if (userAvatarDropdownElement) userAvatarDropdownElement.textContent = initials;

            console.log('Profil berhasil diperbarui dari API:', { name, role });

        } else {
            throw new Error('Respons API tidak valid atau tidak sukses.');
        }
    })
    .catch(error => {
        console.error('Ada kesalahan saat mengambil data:', error);
        // Tangani kesalahan dengan memperbarui elemen yang ada
        if (namaUserElement) namaUserElement.textContent = 'Guest';
        if (roleUserElement) roleUserElement.textContent = 'Kesalahan';
        if (namaUserGreeting) namaUserGreeting.textContent = 'Pengguna';
        if (userAvatarElement) userAvatarElement.textContent = 'G';
        if (userAvatarDropdownElement) userAvatarDropdownElement.textContent = 'G';
    });
});
