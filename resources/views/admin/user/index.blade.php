@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Account Settings /</span> Data User
        </h4>

        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar User</h5>
                <a href="{{ route('admin.user.create') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus"></i> Tambah
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 50px;">No</th>
                                <th class="text-center">Nama User</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Telp</th>
                                <th class="text-center">Email</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <tr>
                                <td colspan="6" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('auth_token');
            const tbody = document.getElementById('userTableBody');

            if (!token) {
                window.location.href = '/login';
                return;
            }

            fetch('/api/user', {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    const users = data.data || [];
                    tbody.innerHTML = '';

                    if (users.length === 0) {
                        tbody.innerHTML =
                            `<tr><td colspan="6" class="text-center text-muted">Belum ada data user.</td></tr>`;
                        return;
                    }

                    users.forEach((user) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                    <td class="text-center">${user.id_user}</td>
                    <td>${user.nama}</td>
                    <td class="text-center">${user.role?.nama_role || '-'}</td>
                    <td class="text-center">${user.telp}</td>
                    <td>${user.email}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info me-1" title="Lihat"
                            onclick="window.location.href='/admin/user/${user.id_user}/show'">
                            <i class='bx bx-show'></i>
                        </button>
                        <button class="btn btn-sm btn-warning me-1" title="Edit"
                            onclick="window.location.href='/admin/user/${user.id_user}/edit'">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id_user})">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(error => {
                    tbody.innerHTML =
                        `<tr><td colspan="6" class="text-danger text-center">Gagal memuat data.</td></tr>`;
                    console.error(error);
                });
        });

        function editUser(id) {
            window.location.href = `/admin/user/edit/${id}`;
        }

        function deleteUser(id) {
            const token = localStorage.getItem('auth_token');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data user ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/api/user/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Authorization': token,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(response => {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => location.reload());
                        })
                        .catch(err => {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus.', 'error');
                        });
                }
            });
        }
    </script>
@endsection
