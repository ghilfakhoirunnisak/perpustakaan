@extends('verifikator.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Pengajuan Buku /</span> Verifikasi Pengajuan Buku
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Pengajuan</h5>
                    <select id="filterStatus" class="form-select w-auto">
                        <option value="">Semua Status</option>
                        <option value="diproses">Hanya Diproses</option>
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="pengajuanTable">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>Nama Pengguna</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th>Buku yang Diajukan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
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
        const token = localStorage.getItem('auth_token');

        async function loadPengajuan(status = '') {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = `<tr><td colspan="6" class="text-center">Memuat data...</td></td>`;

            // UBAH BARIS INI:
            const url = status ? `/api/approvalbuku?status=${status}` : `/api/approvalbuku`;
            //              ^---------------------^
            // Pastikan ini adalah rute API yang benar untuk list pengajuan verifikator

            try {
                const response = await fetch(url, {
                    headers: {
                        Authorization: `Bearer ${token}` // Pastikan format token adalah "Bearer YOUR_TOKEN"
                    }
                });

                const result = await response.json();

                if (!result.success || result.data.length === 0) { // Periksa juga array kosong
                    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">Tidak ada data.</td></tr>`;
                    return;
                }

                tbody.innerHTML = '';
                result.data.forEach((item, index) => {
                    let daftarBuku = '<ul class="mb-0">';
                    if (item.detail_pengajuan_buku && item.detail_pengajuan_buku.length) {
                        item.detail_pengajuan_buku.forEach(b => {
                            // Pastikan judul buku tetap tampil di sini
                            daftarBuku +=
                                `<li>${b.buku?.judul ?? '(tidak ditemukan)'} - ${b.jumlah} item</li>`;
                        });
                    } else {
                        daftarBuku += '<li class="text-muted">Tidak ada</li>';
                    }
                    daftarBuku += '</ul>';

                    tbody.innerHTML += `
                <tr class="align-middle text-center">
                    <td>${index + 1}</td>
                    <td>${item.nama_lengkap ?? '-'}</td>
                    <td>${item.alamat ?? '-'}</td>
                    <td><span class="badge bg-${getBadge(item.status)}">${item.status}</span></td>
                    <td class="text-start">${daftarBuku}</td>
                    <td>
                        <button class="btn btn-sm btn-icon btn-info" title="Detail"
                            onclick="window.location.href='/verifikator/pengajuanbuku/${item.id_pengajuan_buku}/show'">
                            <i class="bx bx-show"></i>
                        </button>
                    </td>
                </tr>
            `;
                });
            } catch (err) {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Gagal memuat data</td></tr>`;
            }
        }

        function getBadge(status) {
            switch (status) {
                case 'diproses':
                    return 'warning text-dark';
                case 'disetujui':
                    return 'success';
                case 'ditolak':
                    return 'danger';
                default:
                    return 'secondary';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const filter = document.getElementById('filterStatus');
            loadPengajuan();

            filter.addEventListener('change', function() {
                loadPengajuan(this.value);
            });
        });
    </script>
@endsection
