@extends('anggota.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Peminjaman Buku</h4>

        <!-- ALERT -->
        <div id="alert-container" class="alert alert-info d-flex align-items-center gap-2 mb-4" style="display: none;"
            role="alert">
            <i class="bx bx-info-circle fs-4"></i>
            <div class="text-dark">
                Sudah pinjam buku tapi belum muncul? Silakan kembali ke halaman ini atau hubungi kontak support.
            </div>
        </div>

        <!-- TAMPILAN JIKA TIDAK ADA DATA -->
        <div id="no-data" class="text-center d-none my-5">
            <img src="{{ asset('no_data.svg') }}" alt="Tidak ada data" class="img-fluid mb-3" style="max-width: 180px;">
            <div class="mb-2">
                <strong style="font-size: 1.25rem;">Belum ada peminjaman buku</strong>
            </div>
            <div class="text-muted mb-3">
                Silakan pinjam buku terlebih dahulu untuk melihat riwayat peminjaman Anda.
            </div>
            <a href="{{ route('anggota.buku.index') }}" class="btn btn-sm btn-primary">
                <i class="bx bx-book-add me-1"></i> Lihat Buku
            </a>
        </div>

        <!-- TABEL DATA -->
        <div class="card shadow d-none" id="card-table">
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama</th>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                                <th style="width: 100px;">Aksi</th>
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
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    const tbody = document.getElementById('peminjamanTbody');
                    const alertContainer = document.getElementById('alert-container');
                    const tableCard = document.getElementById('card-table');
                    const noData = document.getElementById('no-data');

                    tbody.innerHTML = '';

                    if (!res.data || res.data.length === 0) {
                        alertContainer.style.display = 'none';
                        tableCard.classList.add('d-none');
                        noData.classList.remove('d-none');
                        return;
                    }

                    alertContainer.style.display = 'none';
                    tableCard.classList.remove('d-none');
                    noData.classList.add('d-none');

                    res.data.forEach((item, index) => {
                        const tanggalKembali = item.tanggal_kembali ||
                            '<span class="text-muted fst-italic">-</span>';
                        const statusBadge = getStatusBadge(item.status);

                        tbody.innerHTML += `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td>${item.user?.nama || '-'}</td>
                    <td>${item.buku?.judul || '-'}</td>
                    <td class="text-center">${item.tanggal_pinjam}</td>
                    <td class="text-center">${tanggalKembali}</td>
                    <td class="text-center"><span class="badge bg-${statusBadge} text-capitalize">${item.status}</span></td>
                    <td class="text-center">
                        <a href="/anggota/peminjamanbuku/${item.id_peminjaman}/show" class="text-info" title="Detail">
                            <i class="bx bx-show bx-sm"></i>
                        </a>
                    </td>
                </tr>
            `;
                    });
                });

            function getStatusBadge(status) {
                switch (status) {
                    case 'dipinjam':
                        return 'warning';
                    case 'dikembalikan':
                        return 'success';
                    case 'telat':
                        return 'danger';
                    default:
                        return 'secondary';
                }
            }
        });
    </script>
@endsection
