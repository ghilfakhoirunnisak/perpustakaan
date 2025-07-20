@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Data Umum / Fasilitas /</span> Detail Fasilitas
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">ID Fasilitas</label>
                    <div class="form-control bg-light" id="id_fasilitas">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Fasilitas</label>
                    <div class="form-control bg-light" id="nama_fasilitas">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <div class="form-control bg-light" id="deskripsi" style="white-space: pre-wrap;">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <div class="form-control bg-light" id="status">-</div>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.fasilitas.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const idFasilitas = `{{ request()->segment(3) }}`;
        const token = localStorage.getItem('auth_token');

        async function loadFasilitasDetail() {
            try {
                const res = await fetch(`/api/fasilitas/${idFasilitas}`, {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });

                const result = await res.json();

                if (!result.success) {
                    Swal.fire('Gagal', result.message || 'Data tidak ditemukan', 'error');
                    return;
                }

                const data = result.data;

                document.getElementById('id_fasilitas').textContent = data.id_fasilitas ?? '-';
                document.getElementById('nama_fasilitas').textContent = data.nama_fasilitas ?? '-';
                document.getElementById('deskripsi').textContent = data.deskripsi ?? '-';
                document.getElementById('status').textContent = data.status === 'aktif' ? 'Aktif' : 'Nonaktif';

            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Gagal memuat data fasilitas.', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', loadFasilitasDetail);
    </script>
@endsection
