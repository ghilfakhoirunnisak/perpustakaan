@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Data Buku /</span> Edit Data Buku
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <form id="form-edit-buku" enctype="multipart/form-data">
                    <div class="row g-3">

                        <!-- ID Buku (readonly) -->
                        <div class="col-md-4">
                            <label class="form-label">ID Buku</label>
                            <input type="text" name="id_buku" class="form-control" readonly>
                        </div>

                        <!-- Cover Preview -->
                        <div class="col-md-12">
                            <label class="form-label">Cover Saat Ini</label><br>
                            <img id="cover-preview" src="{{ asset('no-cover.png') }}" class="img-fluid rounded shadow mb-2"
                                style="max-height: 200px;" alt="Cover Buku">
                            <input type="file" name="cover" class="form-control mt-2" accept="image/*">
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

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.buku.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", async () => {
            const id = "{{ $id_buku }}";
            const form = document.getElementById('form-edit-buku');

            // Load data penulis
            const penulisRes = await fetch('/api/penulisbuku');
            const penulisData = await penulisRes.json();
            const penulisSelect = form.querySelector('[name="id_penulis_buku"]');
            penulisData.data.forEach(p => {
                penulisSelect.innerHTML +=
                    `<option value="${p.id_penulis_buku}">${p.nama_penulis}</option>`;
            });

            // Load data penerbit
            const penerbitRes = await fetch('/api/penerbitbuku');
            const penerbitData = await penerbitRes.json();
            const penerbitSelect = form.querySelector('[name="id_penerbit_buku"]');
            penerbitData.data.forEach(p => {
                penerbitSelect.innerHTML +=
                    `<option value="${p.id_penerbit_buku}">${p.nama_penerbit}</option>`;
            });

            // Load detail buku
            const bukuRes = await fetch(`/api/buku/${id}`);
            const result = await bukuRes.json();
            if (!result.success) {
                Swal.fire('Gagal', result.message, 'error');
                return;
            }

            const buku = result.data;
            form.id_buku.value = buku.id_buku;
            form.judul.value = buku.judul;
            form.isbn.value = buku.isbn ?? '';
            form.id_penulis_buku.value = buku.id_penulis_buku;
            form.id_penerbit_buku.value = buku.id_penerbit_buku;
            form.genre.value = buku.genre;
            form.tahun_terbit.value = buku.tahun_terbit;
            form.stok.value = buku.stok;
            form.sinopsis.value = buku.sinopsis;

            if (buku.cover) {
                document.getElementById('cover-preview').src = `/storage/${buku.cover}`;
            }

            // Submit handler
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);

                const res = await fetch(`/api/buku/${id}`, {
                    method: 'POST', // atau 'PUT' jika pakai method override
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: formData
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    Swal.fire('Berhasil', data.message, 'success').then(() => {
                        window.location.href = "{{ route('admin.buku.index') }}";
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
                }
            });
        });
    </script>
@endsection
