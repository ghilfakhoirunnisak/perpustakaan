@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Data Fasilitas</h4>

        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Fasilitas</h5>
                <a href="{{ route('admin.fasilitas.create') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus"></i> Tambah
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 50px;">No</th>
                                <th>Nama Fasilitas</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="fasilitasTableBody">
                            <tr>
                                <td colspan="5" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const tbody = document.getElementById('fasilitasTableBody');
            const token = localStorage.getItem('auth_token');

            try {
                const res = await fetch('/api/fasilitas', {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });

                const result = await res.json();
                tbody.innerHTML = '';

                if (!result.success) {
                    tbody.innerHTML =
                        `<tr><td colspan="5" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                    return;
                }

                const data = result.data;

                if (data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="5" class="text-center">Belum ada data fasilitas.</td></tr>`;
                    return;
                }

                data.forEach((fasilitas) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                    <td class="text-center">${fasilitas.id_fasilitas}</td>
                    <td>${fasilitas.nama_fasilitas}</td>
                    <td style="white-space: normal;">${fasilitas.deskripsi}</td>
                    <td class="text-center">
                        <span class="badge bg-${fasilitas.status === 'aktif' ? 'success' : 'secondary'}">
                            ${fasilitas.status ?? '-'}
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info me-1" title="Detail"
                            onclick="window.location.href='/admin/fasilitas/${fasilitas.id_fasilitas}/show'">
                            <i class="bx bx-show"></i>
                        </button>
                        <button class="btn btn-sm btn-warning me-1" title="Edit"
                            onclick="window.location.href='/admin/fasilitas/${fasilitas.id_fasilitas}/edit'">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" title="Hapus"
                            onclick="hapusFasilitas(${fasilitas.id_fasilitas})">
                            <i class="bx bx-trash"></i>
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

        function hapusFasilitas(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data fasilitas akan dihapus secara permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const token = localStorage.getItem('auth_token');

                    fetch(`/api/fasilitas/${id}`, {
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
                                    title: 'Berhasil',
                                    text: 'Data fasilitas berhasil dihapus!',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: result.message || 'Gagal menghapus data.'
                                });
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Gagal menghubungi server.'
                            });
                        });
                }
            });
        }
    </script>
@endsection
