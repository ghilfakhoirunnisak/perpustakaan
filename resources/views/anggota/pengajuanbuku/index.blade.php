@extends('anggota.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Pengajuan Buku Saya</h4>

        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Pengajuan</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Lengkap</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th>Buku yang Diajukan</th>
                                <th>Catatan</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pengajuanTableBody">
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
        document.addEventListener("DOMContentLoaded", function() {
            fetch('/api/pengajuanbuku', {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                const tbody = document.getElementById('pengajuanTableBody');
                tbody.innerHTML = '';

                if (!res.data || res.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center">Belum ada pengajuan buku.</td></tr>`;
                    return;
                }

                res.data.forEach((item, index) => {
                    const detailList = item.detail_pengajuan_buku?.length ?
                        item.detail_pengajuan_buku.map(detail => {
                            const judul = detail.buku?.judul ?? 'Judul tidak ditemukan';
                            return `<li>${judul} (${detail.jumlah} buku)</li>`;
                        }).join('') :
                        '<li><em>Belum ada buku</em></li>';

                    const catatan = item.catatan ?? '<em>(Tidak ada)</em>';

                    tbody.innerHTML += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td>${item.nama_lengkap}</td>
                            <td>${item.alamat}</td>
                            <td class="text-center">
                                <span class="badge bg-${getBadgeClass(item.status)}">${item.status}</span>
                            </td>
                            <td><ul class="mb-0 ps-3">${detailList}</ul></td>
                            <td>${item.catatan || "-"}</td>
                            <td class="text-center">
                                <a href="/anggota/pengajuanbuku/${item.id_pengajuan_buku}/show" class="btn btn-sm btn-info">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });
            });

            function getBadgeClass(status) {
                switch (status) {
                    case 'diproses': return 'warning';
                    case 'disetujui': return 'success';
                    case 'ditolak': return 'danger';
                    default: return 'secondary';
                }
            }
        });
    </script>
@endsection
