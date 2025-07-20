@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Data Buku / Data Penerbit Buku /</span> Detail Penerbit Buku
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">ID Penerbit Buku</label>
                    <div class="form-control bg-light" id="id_penerbit_buku"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Penerbit</label>
                    <div class="form-control bg-light" id="nama_penerbit"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Telepon</label>
                    <div class="form-control bg-light" id="telp"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat</label>
                    <div class="form-control bg-light" id="alamat"></div>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.penerbitbuku.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const idPenerbit = `{{ request()->segment(3) }}`;
        const token = localStorage.getItem('auth_token'); // Hapus jika tidak pakai token

        async function loadDetail() {
            try {
                const res = await fetch(`/api/penerbitbuku/${idPenerbit}`, {
                    headers: {
                        'Authorization': token // Hapus baris ini jika tidak pakai token
                    }
                });

                const result = await res.json();

                if (!result.success) {
                    Swal.fire('Gagal', result.message, 'error');
                    return;
                }

                const data = result.data;
                document.getElementById('id_penerbit_buku').textContent = data.id_penerbit_buku ?? '-';
                document.getElementById('nama_penerbit').textContent = data.nama_penerbit ?? '-';
                document.getElementById('telp').textContent = data.telp ?? '-';
                document.getElementById('alamat').textContent = data.alamat ?? '-';

            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Gagal memuat data detail.', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', loadDetail);
    </script>
@endsection
