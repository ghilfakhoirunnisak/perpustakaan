@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Peminjaman Buku /</span> Detail Peminjaman
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama</label>
                    <div class="form-control bg-light" id="nama_user"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Buku</label>
                    <div class="form-control bg-light" id="judul_buku"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Peminjaman</label>
                    <div class="form-control bg-light" id="tanggal_peminjaman"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Kembali</label>
                    <div class="form-control bg-light" id="tanggal_kembali"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <div class="form-control bg-light" id="status"></div>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.peminjamanbuku.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const idPeminjaman = `{{ request()->segment(3) }}`;
        const token = localStorage.getItem('auth_token');

        async function loadDetail() {
            try {
                const res = await fetch(`/api/peminjamanbuku/${idPeminjaman}`, {
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
                document.getElementById('nama_user').textContent = data.user?.nama ?? '-';
                document.getElementById('judul_buku').textContent = data.buku?.judul ?? '-';
                document.getElementById('tanggal_peminjaman').textContent = data.tanggal_peminjaman ?? '-';
                document.getElementById('tanggal_kembali').textContent = data.tanggal_kembali ?? '-';
                document.getElementById('status').textContent = data.status ?? '-';

            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Gagal memuat data detail.', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', loadDetail);
    </script>
@endsection
