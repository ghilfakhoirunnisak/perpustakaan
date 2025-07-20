@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Data Fasilitas /</span> Tambah Fasilitas
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <form id="formFasilitas">
                    {{-- Nama Fasilitas --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Fasilitas</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-home-alt"></i></span>
                            <input type="text" class="form-control" id="nama_fasilitas" name="nama_fasilitas"
                                placeholder="Masukkan nama fasilitas" required>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-text"></i></span>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Masukkan deskripsi fasilitas"
                                required></textarea>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-toggle-left"></i></span>
                            <select class="form-select" id="status" name="status" required>
                                <option value="aktif" selected>Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Simpan
                        </button>
                        <a href="{{ route('admin.fasilitas.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        document.getElementById('formFasilitas').addEventListener('submit', async function(e) {
            e.preventDefault();

            const token = localStorage.getItem('auth_token');
            const data = {
                nama_fasilitas: document.getElementById('nama_fasilitas').value,
                deskripsi: document.getElementById('deskripsi').value,
                status: document.getElementById('status').value
            };

            try {
                const res = await fetch('/api/fasilitas', {
                    method: 'POST',
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (res.ok && result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: result.message,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        window.location.href = "{{ route('admin.fasilitas.index') }}";
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
                    title: 'Kesalahan',
                    text: 'Gagal terhubung ke server.'
                });
            }
        });
    </script>
@endsection
