@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Account Settings /</span> Data Role
        </h4>

        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Role</h5>
                <a href="" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus"></i> Tambah
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 50px;">No</th>
                                <th class="text-center">Nama Role</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="roleTableBody">
                            <tr>
                                <td colspan="3" class="text-center">Memuat data...</td>
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
            const tbody = document.getElementById('roleTableBody');

            if (!token) {
                window.location.href = '/login';
                return;
            }

            fetch('/api/role', {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    const roles = data.data || [];
                    tbody.innerHTML = '';

                    if (roles.length === 0) {
                        tbody.innerHTML =
                            `<tr><td colspan="3" class="text-center text-muted">Belum ada data role.</td></tr>`;
                        return;
                    }

                    roles.forEach((role, index) => {
                        const tr = document.createElement('tr');
                        tr.classList.add('text-center');
                        tr.innerHTML = `
                    <td>${role.id_role}</td>
                    <td>${role.nama_role}</td>
                    <td>
                        
                        <button class="btn btn-sm btn-warning me-1" onclick="editRole(${role.id_role})">
                            <i class="bx bx-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteRole(${role.id_role})">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(error => {
                    tbody.innerHTML =
                        `<tr><td colspan="3" class="text-danger text-center">Gagal memuat data.</td></tr>`;
                    console.error(error);
                });
        });

        function editRole(id) {
            window.location.href = `/admin/role/edit/${id}`;
        }

        function deleteRole(id) {
            const token = localStorage.getItem('auth_token');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data role ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/api/role/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Authorization': token,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                        })
                        .catch(err => {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus.', 'error');
                        });
                }
            });
        }
    </script>
@endsection
