@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Data Buku</h4>

        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Buku</h5>
                <a href="{{ route('admin.buku.create') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus"></i> Tambah
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 50px;">No</th>
                                <th>Cover</th>
                                <th>Judul</th>
                                <th>ISBN</th>
                                <th>Penulis</th>
                                <th>Penerbit</th>
                                <th>Genre</th>
                                <th>Thn Terbit</th>
                                <th>Stok</th>
                                <th>Sinopsis</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bukuTableBody">
                            <tr>
                                <td colspan="11" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('/api/buku') // pastikan route ini mengembalikan JSON: { data: [...] }
                .then(res => res.json())
                .then(res => {
                    const tbody = document.getElementById('bukuTableBody');
                    tbody.innerHTML = '';

                    if (!res.data || res.data.length === 0) {
                        tbody.innerHTML =
                            `<tr><td colspan="11" class="text-center">Tidak ada data buku.</td></tr>`;
                        return;
                    }

                    res.data.forEach((buku, index) => {
                        tbody.innerHTML += `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td class="text-center">
                            ${buku.cover ? `<img src="/storage/${buku.cover}" style="width: 50px; height: auto;">` : '-'}
                        </td>
                        <td>${buku.judul}</td>
                        <td>${buku.isbn ?? '-'}</td>
                        <td>${buku.penulis_buku?.nama_penulis ?? '-'}</td>
                        <td style="white-space : normal;">${buku.penerbit_buku?.nama_penerbit ?? '-'}</td>
                        <td>${buku.genre}</td>
                        <td class="text-center">${buku.tahun_terbit}</td>
                        <td class="text-center">${buku.stok}</td>
                        <td style="max-width: 200px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;" title="${buku.sinopsis}">
                            ${buku.sinopsis}
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info me-1" title="Detail"
                                onclick="window.location.href='/admin/buku/${buku.id_buku}/show'">
                                <i class="bx bx-show"></i>
                            </button>
                            <button class="btn btn-sm btn-warning me-1" title="Edit"
                                onclick="window.location.href='/admin/buku/${buku.id_buku}/edit'">
                                <i class="bx bx-edit-alt"></i>
                            </button>
                            <button onclick="hapusBuku(${buku.id_buku})" class="btn btn-sm btn-danger mb-1">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                    });
                });

            window.hapusBuku = function(id) {
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: 'Data buku akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/api/buku/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                Swal.fire('Berhasil!', data.message, 'success').then(() => location
                                    .reload());
                            })
                            .catch(err => {
                                Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
                            });
                    }
                });
            }
        });
    </script>
@endsection
