@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Account Settings / Data User /</span> Tambah Data User
        </h4>

        <div class="card mb-4">
            <div class="card-body">
                <form id="formUser">

                    {{-- Pilih Role --}}
                    <div class="mb-3">
                        <label class="form-label" for="id_role">Role</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-shield"></i></span>
                            <select class="form-select" id="id_role" name="id_role" required>
                                <option value="" disabled selected>Pilih Role</option>
                            </select>
                        </div>
                    </div>

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label" for="nama">Nama</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <input type="text" id="nama" name="nama" class="form-control"
                                placeholder="Masukkan nama lengkap" required />
                        </div>
                    </div>

                    {{-- Telp --}}
                    <div class="mb-3">
                        <label class="form-label" for="telp">Nomor Telepon</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-phone"></i></span>
                            <input type="text" id="telp" name="telp" class="form-control"
                                placeholder="08xxxxxxxxxx" required />
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="user@example.com" required />
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-lock"></i></span>
                            <input type="password" id="password" name="password" class="form-control" placeholder="******"
                                required />
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Simpan
                        </button>
                        <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('auth_token');

            try {
                const res = await fetch('/api/role', {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });

                const result = await res.json();
                const select = document.getElementById('id_role');

                result.data.forEach(role => {
                    const opt = document.createElement('option');
                    opt.value = role.id_role;
                    opt.textContent = `${role.id_role} - ${role.nama_role}`;
                    select.appendChild(opt);
                });
            } catch (err) {
                console.error('Gagal memuat role:', err);
                Swal.fire('Gagal', 'Gagal memuat daftar role', 'error');
            }

            // Submit user
            const form = document.getElementById('formUser');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = {
                    id_role: form.id_role.value,
                    nama: form.nama.value,
                    telp: form.telp.value,
                    email: form.email.value,
                    password: form.password.value
                };

                try {
                    const res = await fetch('/api/user', {
                        method: 'POST',
                        headers: {
                            'Authorization': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    const result = await res.json();
                    if (res.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: result.message
                        }).then(() => {
                            window.location.href = '/admin/user';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: result.message || 'Terjadi kesalahan saat menyimpan data.'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan jaringan/server.', 'error');
                }
            });
        });
    </script>
@endsection
