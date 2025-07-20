@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Data Buku /</span> Data Penerbit Buku
        </h4>

        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Penerbit Buku</h5>
                <a href="{{ url('admin/penerbitbuku/create') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus"></i> Tambah
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 50px;">No</th>
                                <th>Nama Penerbit</th>
                                <th>Telp</th>
                                <th>Alamat</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="penerbitbukuTableBody">
                            <tr>
                                <td colspan="4" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const tbody = document.getElementById('penerbitbukuTableBody');
            const token = localStorage.getItem('auth_token');

            try {
                const res = await fetch('/api/penerbitbuku', {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });

                const result = await res.json();

                if (!result.success) {
                    tbody.innerHTML =
                        `<tr><td colspan="5" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                    return;
                }

                const data = result.data;

                if (data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="5" class="text-center">Belum ada data penerbit buku.</td></tr>`;
                    return;
                }

                tbody.innerHTML = '';
                data.forEach((penerbit) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                    <td class="text-center">${penerbit.id_penerbit_buku}</td>
                    <td>${penerbit.nama_penerbit}</td>
                    <td class="text-center">${penerbit.telp || '-'}</td>
                    <td style="white-space: normal;">${penerbit.alamat || '-'}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info me-1" title="Lihat"
                            onclick="window.location.href='/admin/penerbitbuku/${penerbit.id_penerbit_buku}/show'">
                            <i class='bx bx-show'></i>
                        </button>
                        <button class="btn btn-sm btn-warning me-1" title="Edit"
                            onclick="window.location.href='/admin/penerbitbuku/${penerbit.id_penerbit_buku}/edit'">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" title="Hapus"
                            onclick="confirmDelete(${penerbit.id_penerbit_buku})">
                            <i class='bx bx-trash'></i>
                        </button>
                    </td>
                `;
                    tbody.appendChild(tr);
                });
            } catch (error) {
                console.error(error);
                tbody.innerHTML =
                    `<tr><td colspan="5" class="text-center text-danger">Terjadi kesalahan saat mengambil data.</td></tr>`;
            }
        });

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Penerbit?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    deletePenerbit(id);
                }
            });
        }

        async function deletePenerbit(id) {
            const token = localStorage.getItem('auth_token');
            try {
                const res = await fetch(`/api/penerbitbuku/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });

                const result = await res.json();

                if (res.ok) {
                    Swal.fire('Berhasil!', result.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', result.message || 'Gagal menghapus data.', 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Terjadi kesalahan server.', 'error');
            }
        }
    </script>
@endsection
