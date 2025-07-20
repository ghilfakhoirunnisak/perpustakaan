@extends('anggota.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Data Reservasi Fasilitas</h4>

        {{-- ALERT --}}
        <div id="alert-container" class="alert alert-info d-flex align-items-center gap-2 mb-4" style="display: none;"
            role="alert">
            <i class="bx bx-info-circle fs-4"></i>
            <div class="text-dark">
                Sudah reservasi tapi belum muncul? Silakan masuk ke halaman ini kembali atau hubungi kontak support jika
                masih bermasalah.
            </div>
        </div>

        {{-- NO DATA --}}
        <div id="no-data" class="text-center d-none my-5">
            <img src="{{ asset('no_data.svg') }}" alt="Tidak ada data" class="img-fluid mb-4" style="max-width: 180px;">
            <h5 class="fw-semibold mb-2" style="font-size: 1.3rem;">Kamu belum memiliki reservasi fasilitas</h5>
            <p class="text-muted mb-4" style="font-size: 1.05rem;">Ajukan reservasi terlebih dahulu untuk melihat data di
                sini.</p>
            <a href="{{ route('anggota.reservasifasilitas.create') }}" class="btn btn-primary px-4">
                <i class="bx bx-calendar-plus me-1"></i> Ajukan Reservasi Sekarang
            </a>
        </div>

        {{-- TABEL --}}
        <div class="card shadow d-none" id="card-table">
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Kode Reservasi</th>
                                <th>Fasilitas</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="reservasiTbody">
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
            fetch('/api/reservasifasilitas', {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    const tbody = document.getElementById('reservasiTbody');
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
                    noData.classList.add('d-none');
                    tableCard.classList.remove('d-none');

                    res.data.forEach((item, index) => {
                        const tanggal = `${item.tanggal_kegiatan} s/d ${item.tanggal_selesai}`;
                        const badge = getBadge(item.status);

                        tbody.innerHTML += `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td class="text-center">${item.kode_reservasi}</td>
                        <td>${item.fasilitas?.nama_fasilitas || '-'}</td>
                        <td class="text-center">${tanggal}</td>
                        <td>${item.keterangan || '-'}</td>
                        <td class="text-center">
                            <span class="badge bg-${badge} text-capitalize">${item.status}</span>
                        </td>
                        <td class="text-center">
                            <a href="/anggota/reservasifasilitas/${item.id_reservasi}/show" class="text-info me-2" title="Lihat">
                                <i class="bx bx-show bx-sm"></i>
                            </a>
                            ${item.status !== 'dibatalkan' ? `
                                        <a href="javascript:void(0)" onclick="batalkanReservasi(${item.id_reservasi})" class="text-danger" title="Batalkan">
                                            <i class="bx bx-x-circle bx-sm"></i>
                                        </a>` : ''}
                        </td>
                    </tr>
                `;
                    });
                });

            function getBadge(status) {
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

            window.batalkanReservasi = function(id) {
                Swal.fire({
                    title: 'Batalkan Reservasi?',
                    text: 'Reservasi ini akan dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/api/reservasifasilitas/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                Swal.fire('Berhasil', data.message, 'success').then(() => location
                                    .reload());
                            })
                            .catch(() => {
                                Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
                            });
                    }
                });
            }
        });
    </script>
@endsection
