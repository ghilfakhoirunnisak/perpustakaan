@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Data Buku / Data Penerbit Buku /</span> Edit Penerbit Buku
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <form id="formEditPenerbit">
                    {{-- ID Penerbit --}}
                    <div class="mb-3">
                        <label for="id_penerbit_buku" class="form-label">ID Penerbit</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-barcode'></i></span>
                            <input type="text" class="form-control" id="id_penerbit_buku" name="id_penerbit_buku"
                                readonly>
                        </div>
                    </div>

                    {{-- Nama Penerbit --}}
                    <div class="mb-3">
                        <label for="nama_penerbit" class="form-label">Nama Penerbit</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-book'></i></span>
                            <input type="text" class="form-control" id="nama_penerbit" name="nama_penerbit" required>
                        </div>
                    </div>

                    {{-- Nomor Telepon --}}
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
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.penerbitbuku.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const id = window.location.pathname.split('/').slice(-2, -1)[0];
            const token = localStorage.getItem('auth_token');

            try {
                const res = await fetch(`/api/penerbitbuku/${id}`, {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });

                const result = await res.json();

                if (res.ok && result.success) {
                    const data = result.data;
                    document.getElementById('id_penerbit_buku').value = data.id_penerbit_buku;
                    document.getElementById('nama_penerbit').value = data.nama_penerbit;
                    document.getElementById('telp').value = data.telp;
                    document.getElementById('alamat').value = data.alamat;
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
                    text: 'Gagal mengambil data penerbit.'
                });
            }
        });

        document.getElementById('formEditPenerbit').addEventListener('submit', async function(e) {
            e.preventDefault();

            const id = document.getElementById('id_penerbit_buku').value;
            const token = localStorage.getItem('auth_token');

            const payload = {
                nama_penerbit: document.getElementById('nama_penerbit').value,
                telp: document.getElementById('telp').value,
                alamat: document.getElementById('alamat').value
            };

            try {
                const res = await fetch(`/api/penerbitbuku/${id}`, {
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
                        text: result.message
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
