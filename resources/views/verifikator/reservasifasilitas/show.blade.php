@extends('verifikator.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Reservasi Fasilitas /</span> Verifikasi Reservasi
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <!-- Kode Reservasi -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-hash me-1"></i> Kode Reservasi
                    </label>
                    <div class="form-control bg-light" id="kode_reservasi">-</div>
                </div>

                <!-- Nama Pengguna -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-person-circle me-1"></i> Nama Pengguna
                    </label>
                    <div class="form-control bg-light" id="nama_user">-</div>
                </div>

                <!-- Nama Fasilitas -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-building me-1"></i> Fasilitas
                    </label>
                    <div class="form-control bg-light" id="nama_fasilitas">-</div>
                </div>

                <!-- Tanggal Kegiatan -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-calendar-event me-1"></i> Tanggal Kegiatan
                    </label>
                    <div class="form-control bg-light" id="tanggal_kegiatan">-</div>
                </div>

                <!-- Tanggal Selesai -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-calendar-check me-1"></i> Tanggal Selesai
                    </label>
                    <div class="form-control bg-light" id="tanggal_selesai">-</div>
                </div>

                <!-- Keterangan -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-chat-left-text me-1"></i> Keterangan
                    </label>
                    <div class="form-control bg-light" id="keterangan">-</div>
                </div>

                <!-- Dokumen -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-paperclip me-1"></i> Dokumen Terkait
                    </label>
                    <div id="dokumen_list" class="form-control bg-light" style="min-height: 40px;">
                        <em>Memuat...</em>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-pencil-square me-1"></i> Catatan Verifikator
                    </label>
                    <textarea class="form-control" id="catatan" rows="3" placeholder="Tulis catatan jika perlu..."></textarea>
                </div>

                <!-- Aksi -->
                <div class="text-end">
                    <button class="btn btn-success me-2" onclick="kirimApproval('disetujui')">
                        <i class="bi bi-check-circle me-1"></i> Setujui
                    </button>
                    <button class="btn btn-danger me-2" onclick="kirimApproval('ditolak')">
                        <i class="bi bi-x-circle me-1"></i> Tolak
                    </button>
                    <a href="{{ route('verifikator.reservasifasilitas.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
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

        async function kirimApproval(status) {
            const catatan = document.getElementById('catatan').value;

            const konfirmasi = await Swal.fire({
                title: 'Konfirmasi',
                text: `Yakin ingin ${status === 'disetujui' ? 'menyetujui' : 'menolak'} reservasi ini?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal'
            });

            if (!konfirmasi.isConfirmed) return;

            try {
                const res = await fetch(`/api/approval/${idReservasi}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': token
                    },
                    body: JSON.stringify({
                        status,
                        catatan
                    })
                });

                const result = await res.json();
                if (res.ok) {
                    Swal.fire('Berhasil', result.message, 'success')
                        .then(() => window.location.href = "{{ route('verifikator.reservasifasilitas.index') }}");
                } else {
                    Swal.fire('Gagal', result.message, 'error');
                }

            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Terjadi kesalahan saat mengirim keputusan.', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', loadDetail);
    </script>
@endsection
