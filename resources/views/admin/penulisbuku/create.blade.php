@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Data Buku / Data Penulis Buku /</span> Tambah Penulis Buku
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <form id="formPenulisBuku">
                    <div class="mb-3">
                        <label for="nama_penulis" class="form-label">Nama Penulis</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <input type="text" class="form-control" id="nama_penulis" name="nama_penulis"
                                placeholder="Masukkan nama penulis" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="negara" class="form-label">Negara</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-globe"></i></span>
                            <input type="text" class="form-control" id="negara" name="negara"
                                placeholder="Masukkan negara asal" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Simpan
                        </button>
                        <a href="{{ route('admin.penulisbuku.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('formPenulisBuku').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const token = localStorage.getItem('auth_token');

            try {
                const res = await fetch('/api/penulisbuku', {
                    method: 'POST',
                    headers: {
                        'Authorization': token
                    },
                    body: formData
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
                        text: result.message || 'Terjadi kesalahan saat menyimpan.'
                    });
                }

            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan server.'
                });
            }
        });
    </script>
@endsection
