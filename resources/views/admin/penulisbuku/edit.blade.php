@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Data Buku / Data Penulis Buku /</span> Edit Penulis Buku
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <form id="formEditPenulis">
                    {{-- ID Penulis --}}
                    <div class="mb-3">
                        <label for="id_penulis_buku" class="form-label">ID Penulis</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-barcode'></i></span>
                            <input type="text" class="form-control" id="id_penulis_buku" name="id_penulis_buku" readonly>
                        </div>
                    </div>

                    {{-- Nama Penulis --}}
                    <div class="mb-3">
                        <label for="nama_penulis" class="form-label">Nama Penulis</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-user'></i></span>
                            <input type="text" class="form-control" id="nama_penulis" name="nama_penulis" required>
                        </div>
                    </div>

                    {{-- Negara --}}
                    <div class="mb-3">
                        <label for="negara" class="form-label">Negara</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-globe'></i></span>
                            <input type="text" class="form-control" id="negara" name="negara" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.penulisbuku.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const segments = window.location.pathname.split('/');
            const id = segments[segments.length - 2];
            const token = localStorage.getItem('auth_token');

            try {
                const res = await fetch(`/api/penulisbuku/${id}`, {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });

                const result = await res.json();

                if (res.ok && result.success) {
                    const data = result.data;
                    document.getElementById('id_penulis_buku').value = data.id_penulis_buku;
                    document.getElementById('nama_penulis').value = data.nama_penulis;
                    document.getElementById('negara').value = data.negara;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: result.message || 'Data tidak ditemukan.'
                    });
                }

            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengambil data.'
                });
            }
        });

        // Submit Form
        document.getElementById('formEditPenulis').addEventListener('submit', async function(e) {
            e.preventDefault();

            const id = document.getElementById('id_penulis_buku').value;
            const token = localStorage.getItem('auth_token');

            const payload = {
                nama_penulis: document.getElementById('nama_penulis').value,
                negara: document.getElementById('negara').value
            };

            try {
                const res = await fetch(`/api/penulisbuku/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await res.json();

                if (res.ok && result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: result.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = "{{ route('admin.penulisbuku.index') }}";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: result.message || 'Gagal menyimpan data.'
                    });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengirim data.'
                });
            }
        });
    </script>
@endsection
