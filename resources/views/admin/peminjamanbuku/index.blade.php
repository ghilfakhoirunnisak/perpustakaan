@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Peminjaman Buku</h4>

        <!-- TABEL DATA -->
        <div class="card shadow" id="card-table">
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="peminjamanTbody">
                            <tr>
                                <td colspan="7" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/peminjamanbuku', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                    }
                })
                .then(res => res.json())
                .then(res => {
                    const tbody = document.getElementById('peminjamanTbody');
                    tbody.innerHTML = '';

                    if (!res.data || res.data.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Tidak ada data peminjaman buku.
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    res.data.forEach((item, index) => {
                        const tanggalKembali = item.tanggal_kembali || '<span class="text-muted fst-italic">-</span>';
                        const statusBadge = getStatusBadge(item.status);

                        tbody.innerHTML += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td>${item.user?.nama || '-'}</td>
                                <td>${item.buku?.judul || '-'}</td>
                                <td class="text-center">${item.tanggal_pinjam}</td>
                                <td class="text-center">${tanggalKembali}</td>
                                <td class="text-center">
                                    <span class="badge bg-${statusBadge} text-capitalize">${item.status}</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info me-1" title="Detail"
                                        onclick="window.location.href='/admin/peminjamanbuku/${item.id_peminjaman_buku}/show'">
                                        <i class="bx bx-show"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                });

            function getStatusBadge(status) {
                switch (status) {
                    case 'dipinjam': return 'warning';
                    case 'dikembalikan': return 'success';
                    case 'telat': return 'danger';
                    default: return 'secondary';
                }
            }
        });
    </script>
@endsection
