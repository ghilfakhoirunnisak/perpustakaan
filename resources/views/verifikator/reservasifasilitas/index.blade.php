@extends('verifikator.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Reservasi Fasilitas /</span> Verifikasi Reservasi
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Reservasi</h5>
                    <select id="filterStatus" class="form-select w-auto">
                        <option value="">Semua Status</option>
                        <option value="diproses">Hanya Diproses</option>
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="reservasiTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Pengguna</th>
                                <th>Fasilitas</th>
                                <th>Tanggal Kegiatan</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
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
        const token = localStorage.getItem('auth_token');

        async function loadReservasi(status = '') {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = `<tr><td colspan="7" class="text-center">Memuat data...</td></tr>`;

            const url = status ? `/api/approval?status=${status}` : `/api/approval`;

            try {
                const response = await fetch(url, {
                    headers: {
                        Authorization: token
                    }
                });

                const result = await response.json();
                if (!result.success || !result.data.length) {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">Tidak ada data.</td></tr>`;
                    return;
                }

                tbody.innerHTML = '';
                result.data.forEach((item, index) => {
                    tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.user?.nama || '-'}</td>
                        <td>${item.fasilitas?.nama_fasilitas || '-'}</td>
                        <td>${item.tanggal_kegiatan}</td>
                        <td>${item.tanggal_selesai}</td>
                        <td><span class="badge bg-${getBadge(item.status)}">${item.status}</span></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-icon btn-info" title="Detail"
                                    onclick="window.location.href='/verifikator/reservasifasilitas/${item.id_reservasi}/show'">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                });
            } catch (err) {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Gagal memuat data</td></tr>`;
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
            loadReservasi();

            filter.addEventListener('change', function() {
                loadReservasi(this.value);
            });
        });
    </script>
@endsection
