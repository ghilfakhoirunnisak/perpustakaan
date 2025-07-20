@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Account Settings / Data User /</span> Edit Data User
        </h4>

        <div class="card mb-4">
            <div class="card-body">
                <form id="formEditUser">
                    {{-- Role --}}
                    <div class="mb-3">
                        <label class="form-label" for="id_role">Role</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-shield"></i></span>
                            <select id="id_role" name="id_role" class="form-select" required></select>
                        </div>
                    </div>

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label" for="nama">Nama</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <input type="text" id="nama" name="nama" class="form-control" required />
                        </div>
                    </div>

                    {{-- Telp --}}
                    <div class="mb-3">
                        <label class="form-label" for="telp">No Telepon</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-phone"></i></span>
                            <input type="text" id="telp" name="telp" class="form-control" required />
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control" required />
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label" for="password">Password (baru)</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-lock"></i></span>
                            <input type="password" id="password" name="password" class="form-control" required />
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const id_user = `{{ request()->segment(3) }}`;
        const token = localStorage.getItem('auth_token');

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                // Ambil semua role
                const roleRes = await fetch(`/api/role`, {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });
                const roleData = await roleRes.json();

                const roleSelect = document.getElementById('id_role');
                roleData.data.forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.id_role;
                    option.textContent = `${role.id_role} - ${role.nama_role}`;
                    roleSelect.appendChild(option);
                });

                // Ambil data user
                const userRes = await fetch(`/api/user/${id_user}`, {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });
                const userData = await userRes.json();

                if (!userData.success) {
                    Swal.fire('Gagal', 'Data user tidak ditemukan.', 'error');
                    return;
                }

                const user = userData.data;
                document.getElementById('id_role').value = user.id_role;
                document.getElementById('nama').value = user.nama;
                document.getElementById('telp').value = user.telp;
                document.getElementById('email').value = user.email;

            } catch (error) {
                console.error(error);
                Swal.fire('Gagal', 'Gagal memuat data.', 'error');
            }
        });

        document.getElementById('formEditUser').addEventListener('submit', async (e) => {
            e.preventDefault();

            const data = {
                id_role: document.getElementById('id_role').value,
                nama: document.getElementById('nama').value,
                telp: document.getElementById('telp').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            };

            try {
                const res = await fetch(`/api/user/${id_user}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (res.ok) {
                    Swal.fire('Berhasil', result.message, 'success').then(() => {
                        window.location.href = '/admin/user';
                    });
                } else {
                    Swal.fire('Gagal', result.message || 'Gagal memperbarui data.', 'error');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Terjadi kesalahan saat memperbarui.', 'error');
            }
        });
    </script>
@endsection
