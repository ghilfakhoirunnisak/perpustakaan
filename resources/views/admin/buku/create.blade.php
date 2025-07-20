@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-4">Tambah Buku</h4>

        <div class="card shadow">
            <div class="card-body">
                <form id="form-buku" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Cover Buku</label>
                            <input type="file" name="cover" class="form-control" accept="image/*">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Judul Buku</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-book"></i></span>
                                <input type="text" name="judul" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">ISBN</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-barcode"></i></span>
                                <input type="text" name="isbn" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Penulis</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-user"></i></span>
                                <select name="id_penulis_buku" class="form-select" required></select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Penerbit</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-buildings"></i></span>
                                <select name="id_penerbit_buku" class="form-select" required></select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Genre</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-category"></i></span>
                                <input type="text" name="genre" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tahun Terbit</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="number" name="tahun_terbit" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Stok</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-box"></i></span>
                                <input type="number" name="stok" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Sinopsis</label>
                            <textarea name="sinopsis" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Simpan
                        </button>
                        <a href="{{ route('admin.buku.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", async () => {
            const token = localStorage.getItem('auth_token');

            // Fetch penulis
            try {
                const resPenulis = await fetch('/api/penulisbuku', {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });
                const penulis = await resPenulis.json();
                const selectPenulis = document.querySelector('[name="id_penulis_buku"]');
                penulis.data.forEach(p => {
                    selectPenulis.innerHTML +=
                        `<option value="${p.id_penulis_buku}">${p.id_penulis_buku} - ${p.nama_penulis}</option>`;
                });
            } catch (err) {
                console.error('Gagal memuat penulis:', err);
                Swal.fire('Gagal', 'Gagal memuat daftar penulis', 'error');
            }

            // Fetch penerbit
            try {
                const resPenerbit = await fetch('/api/penerbitbuku', {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });
                const penerbit = await resPenerbit.json();
                const selectPenerbit = document.querySelector('[name="id_penerbit_buku"]');
                penerbit.data.forEach(p => {
                    selectPenerbit.innerHTML +=
                        `<option value="${p.id_penerbit_buku}">${p.id_penerbit_buku} - ${p.nama_penerbit}</option>`;
                });
            } catch (err) {
                console.error('Gagal memuat penerbit:', err);
                Swal.fire('Gagal', 'Gagal memuat daftar penerbit', 'error');
            }

            // Submit form buku
            document.getElementById('form-buku').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                try {
                    const res = await fetch('/api/buku', {
                        method: 'POST',
                        headers: {
                            'Authorization': token
                        },
                        body: formData
                    });
                    const result = await res.json();

                    if (res.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: result.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href ='/admin/buku';
                        });
                    } else {
                        Swal.fire('Gagal', result.message || 'Terjadi kesalahan', 'error');
                    }

                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan jaringan/server.', 'error');
                }
            });
        });
    </script>
@endsection
