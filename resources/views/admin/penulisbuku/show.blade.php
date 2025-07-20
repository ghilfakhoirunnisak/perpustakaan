@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Data Buku / Data Penulis Buku /</span> Detail Penulis Buku
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">ID Penulis Buku</label>
                    <div class="form-control bg-light" id="id_penulis_buku"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Penulis</label>
                    <div class="form-control bg-light" id="nama_penulis"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Negara</label>
                    <div class="form-control bg-light" id="negara"></div>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.penulisbuku.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const idPenulis = `{{ request()->segment(3) }}`;
        const token = localStorage.getItem('auth_token'); // optional, remove if not using token

        async function loadDetail() {
            try {
                const res = await fetch(`/api/penulisbuku/${idPenulis}`, {
                    headers: {
                        'Authorization': token // hapus jika tidak pakai auth token
                    }
                });

                const result = await res.json();

                if (!result.success) {
                    Swal.fire('Gagal', result.message, 'error');
                    return;
                }

                const data = result.data;

                document.getElementById('id_penulis_buku').textContent = data.id_penulis_buku ?? '-';
                document.getElementById('nama_penulis').textContent = data.nama_penulis ?? '-';
                document.getElementById('negara').textContent = data.negara ?? '-';

            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Gagal memuat data detail.', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', loadDetail);
    </script>
@endsection
