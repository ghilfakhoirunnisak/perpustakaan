@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Account Settings /</span> Data Verifikator
        </h4>

        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Verifikator</h5>
                <a href="{{ route('admin.verifikator.create') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus"></i> Tambah
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 50px;">No</th>
                                <th class="text-center">ID & Nama User</th>
                                <th class="text-center">Level</th>
                                <th class="text-center">Jabatan</th>
                                <th class="text-center">Status</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="verifikatorTableBody">
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
        document.addEventListener('DOMContentLoaded', async () => {
            const tbody = document.getElementById('verifikatorTableBody');
            const token = localStorage.getItem('auth_token');

            try {
                const res = await fetch('/api/verifikator', {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });

                const result = await res.json();

                if (!result.success) {
                    tbody.innerHTML =
                        `<tr><td colspan="6" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                    return;
                }

                const data = result.data;

                if (data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="6" class="text-center">Belum ada data verifikator.</td></tr>`;
                    return;
                }

                tbody.innerHTML = '';
                data.forEach((verifikator, index) => {
                    const user = verifikator.user || {};
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                    <td class="text-center">${verifikator.id_verifikator}</td>
                    <td class="text-center">
                        <strong>ID:</strong> ${user.id_user ?? '-'} &nbsp; | &nbsp;
                        <strong>Nama:</strong> ${user.nama ?? '-'}
                    </td>
                    <td class="text-center">Level ${verifikator.level}</td>
                    <td class="text-center">${verifikator.jabatan ?? '-'}</td>
                    <td class="text-center">
                        <span class="badge bg-${verifikator.status === 'aktif' ? 'success' : 'secondary'}">
                            ${verifikator.status ?? '-'}
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info me-1" title="Detail"
                            onclick="window.location.href='{{ url('admin/verifikator') }}/${verifikator.id_verifikator}/show'">
                            <i class="bx bx-show"></i>
                        </button>
                        <button class="btn btn-sm btn-warning me-1" title="Edit"
                            onclick="window.location.href='{{ url('admin/verifikator') }}/${verifikator.id_verifikator}/edit'">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" title="Hapus"
                            onclick="hapusVerifikator(${verifikator.id_verifikator})">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                `;
                    tbody.appendChild(tr);
                });

            } catch (error) {
                console.error(error);
                tbody.innerHTML =
                    `<tr><td colspan="6" class="text-center text-danger">Terjadi kesalahan saat mengambil data.</td></tr>`;
            }
        });

        function hapusVerifikator(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data verifikator yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const token = localStorage.getItem('auth_token');

                    fetch(`/api/verifikator/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': token,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Data verifikator berhasil dihapus.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: result.message || 'Gagal menghapus data.'
                            });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan!',
                            text: 'Terjadi kesalahan saat menghapus data.'
                        });
                    });
                }
            });
        }

    </script>
@endsection
