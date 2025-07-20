@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Reservasi Fasilitas /</span> Detail Reservasi
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Reservasi</label>
                    <div class="form-control bg-light" id="kode_reservasi"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Pengguna</label>
                    <div class="form-control bg-light" id="nama_user"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Fasilitas</label>
                    <div class="form-control bg-light" id="nama_fasilitas"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Kegiatan</label>
                    <div class="form-control bg-light" id="tanggal_kegiatan"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Selesai</label>
                    <div class="form-control bg-light" id="tanggal_selesai"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <div class="form-control bg-light" id="keterangan"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Dokumen Terkait</label>
                    <div id="dokumen_list" class="form-control bg-light" style="min-height: 40px;">
                        <em>Memuat...</em>
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.reservasifasilitas.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const idReservasi = `{{ request()->segment(3) }}`;
        const token = localStorage.getItem('auth_token');

        async function loadDetail() {
            try {
                const res = await fetch(`/api/reservasifasilitas/${idReservasi}`, {
                    headers: {
                        'Authorization': token
                    }
                });
                const result = await res.json();

                if (!result.success) {
                    Swal.fire('Gagal', result.message, 'error');
                    return;
                }

                const data = result.data;
                document.getElementById('kode_reservasi').textContent = data.kode_reservasi;
                document.getElementById('nama_user').textContent = data.user?.nama ?? '-';
                document.getElementById('nama_fasilitas').textContent = data.fasilitas?.nama_fasilitas ?? '-';
                document.getElementById('tanggal_kegiatan').textContent = data.tanggal_kegiatan;
                document.getElementById('tanggal_selesai').textContent = data.tanggal_selesai;
                document.getElementById('keterangan').textContent = data.keterangan || '-';

                const dokumenBox = document.getElementById('dokumen_list');
                dokumenBox.innerHTML = '';

                if (data.dokumen && data.dokumen.length) {
                    data.dokumen.forEach(d => {
                        const link = document.createElement('a');
                        link.href = `/storage/${d.path_file}`;
                        link.textContent = d.nama_file;
                        link.classList.add('d-block');
                        link.target = '_blank';
                        dokumenBox.appendChild(link);
                    });
                } else {
                    dokumenBox.innerHTML = '<em>Tidak ada dokumen</em>';
                }

            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Gagal memuat data detail.', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', loadDetail);
    </script>
@endsection
