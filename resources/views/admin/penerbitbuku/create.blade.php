@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Data Buku / Data Penerbit Buku /</span> Tambah Penerbit Buku
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <form id="formPenerbit">
                    {{-- Nama Penerbit --}}
                    <div class="mb-3">
                        <label for="nama_penerbit" class="form-label">Nama Penerbit</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-book'></i></span>
                            <input type="text" class="form-control" id="nama_penerbit" name="nama_penerbit" required>
                        </div>
                    </div>

                    {{-- No Telp --}}
                    <div class="mb-3">
                        <label for="telp" class="form-label">Nomor Telepon</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-phone'></i></span>
                            <input type="text" class="form-control" id="telp" name="telp" maxlength="12"
                                required>
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-map'></i></span>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Simpan
                        </button>
                        <a href="{{ route('admin.penerbitbuku.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('formPenerbit').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const token = localStorage.getItem('auth_token');

            try {
                const res = await fetch("/api/penerbitbuku", {
                    method: "POST",
                    headers: {
                        "Authorization": token,
                        "Accept": "application/json"
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
                        window.location.href = "{{ route('admin.penerbitbuku.index') }}";
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
                    text: 'Terjadi kesalahan saat menyimpan data.'
                });
            }
        });
    </script>
@endsection
