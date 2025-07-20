@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Data Reservasi Fasilitas</h4>

        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Reservasi Fasilitas</h5>
                <a href="{{ route('admin.reservasifasilitas.create') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus"></i> Tambah
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Fasilitas</th>
                                <th>Tgl Kegiatan</th>
                                <th>Tgl Selesai</th>
                                <th>Ket</th>
                                <th>Status</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="reservasifasilitasTableBody">
                            <tr>
                                <td colspan="8" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const tbody = document.getElementById('reservasifasilitasTableBody');
            const token = localStorage.getItem('auth_token');

            try {
                const res = await fetch('/api/reservasifasilitas', {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });

                const result = await res.json();

                if (!res.ok || !result.success) {
                    tbody.innerHTML =
                        `<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                    return;
                }

                const data = result.data;
                if (data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="8" class="text-center">Belum ada data reservasi.</td></tr>`;
                    return;
                }

                tbody.innerHTML = '';
                data.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                    <td class="text-center">${item.kode_reservasi}</td>
                    <td>${item.user?.nama || '-'}</td>
                    <td style="white-space: normal;">${item.fasilitas?.nama_fasilitas || '-'}</td>
                    <td class="text-center">${item.tanggal_kegiatan}</td>
                    <td class="text-center">${item.tanggal_selesai}</td>
                    <td >${item.keterangan ?? '-'}</td>
                    <td class="text-center">
                        <span class="badge bg-${statusColor(item.status)}">${item.status}</span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info me-1" title="Detail"
                            onclick="window.location.href='/admin/reservasifasilitas/${item.id_reservasi}/show'">
                            <i class="bx bx-show"></i>
                        </button>
                        <button class="btn btn-sm btn-warning me-1" title="Edit"
                            onclick="window.location.href='/admin/reservasifasilitas/${item.id_reservasi}/edit'">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" title="Hapus"
                            onclick="hapusReservasi(${item.id_reservasi})">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                `;
                    tbody.appendChild(tr);
                });

            } catch (err) {
                console.error(err);
                tbody.innerHTML =
                    `<tr><td colspan="8" class="text-center text-danger">Terjadi kesalahan saat mengambil data.</td></tr>`;
            }
        });

        function statusColor(status) {
            switch (status) {
                case 'diproses':
                    return 'warning';
                case 'disetujui':
                    return 'success';
                case 'ditolak':
                    return 'danger';
                case 'dibatalkan':
                    return 'secondary';
                default:
                    return 'light';
            }
        }

        function hapusReservasi(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data reservasi akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const token = localStorage.getItem('auth_token');

                    fetch(`/api/reservasifasilitas/${id}`, {
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
                                    text: 'Data berhasil dihapus!',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire('Gagal', result.message || 'Gagal menghapus data.', 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus.', 'error');
                        });
                }
            });
        }
    </script>
@endsection
