@extends('admin.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Data Buku /</span> Data Penulis Buku
    </h4>

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Penulis Buku</h5>
            <a href="{{ route('admin.penulisbuku.create') }}" class="btn btn-sm btn-primary">
                <i class="bx bx-plus"></i> Tambah
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="width: 50px;">No</th>
                            <th>Nama Penulis</th>
                            <th>Negara</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="penulisbukuTableBody">
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
        const tbody = document.getElementById('penulisbukuTableBody');
        const token = localStorage.getItem('auth_token');

        try {
            const res = await fetch('/api/penulisbuku', {
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json'
                }
            });

            const result = await res.json();

            if (!result.success) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                return;
            }

            const data = result.data;

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center">Tidak ada data penulis buku.</td></tr>`;
                return;
            }

            tbody.innerHTML = '';
            data.forEach((penulis, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center">${index + 1}</td>
                    <td>${penulis.nama_penulis}</td>
                    <td class="text-center">${penulis.negara}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info me-1" title="Lihat"
                            onclick="window.location.href='/admin/penulisbuku/${penulis.id_penulis_buku}/show'">
                            <i class='bx bx-show'></i>
                        </button>
                        <button class="btn btn-sm btn-warning me-1" title="Edit"
                            onclick="window.location.href='/admin/penulisbuku/${penulis.id_penulis_buku}/edit'">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" title="Hapus"
                            onclick="confirmDeletePenulisBuku(${penulis.id_penulis_buku})">
                            <i class='bx bx-trash'></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } catch (error) {
            console.error(error);
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Terjadi kesalahan saat mengambil data.</td></tr>`;
        }
    });

    function confirmDeletePenulisBuku(id_penulis_buku) {
        Swal.fire({
            title: 'Hapus Penulis?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                deletePenulisBuku(id_penulis_buku);
            }
        });
    }

    async function deletePenulisBuku(id_penulis_buku) {
        const token = localStorage.getItem('auth_token');
        try {
            const res = await fetch(`/api/penulisbuku/${id_penulis_buku}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json'
                }
            });

            const result = await res.json();

            if (res.ok) {
                Swal.fire('Berhasil!', result.message, 'success').then(() => {
                    location.reload(); // Reload ulang tabel
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
