@extends('admin.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Edit Fasilitas</h4>

    <div class="card shadow">
        <div class="card-body">
            <form id="formEditFasilitas">
                <div class="mb-3">
                    <label class="form-label">ID Fasilitas</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-barcode"></i></span>
                        <input type="text" id="id_fasilitas" class="form-control" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Fasilitas</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-home-alt"></i></span>
                        <input type="text" id="nama_fasilitas" class="form-control" placeholder="Masukkan nama fasilitas">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-text"></i></span>
                        <textarea id="deskripsi" class="form-control" rows="4" placeholder="Deskripsi fasilitas"></textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-toggle-left"></i></span>
                        <select id="status" class="form-select">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                    <a href="{{ route('admin.fasilitas.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const id_fasilitas = `{{ request()->segment(3) }}`;
        const token = localStorage.getItem('auth_token');

        try {
            const res = await fetch(`/api/fasilitas/${id_fasilitas}`, {
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json'
                }
            });

            const result = await res.json();

            if (res.ok && result.success) {
                const data = result.data;
                document.getElementById('id_fasilitas').value = data.id_fasilitas;
                document.getElementById('nama_fasilitas').value = data.nama_fasilitas;
                document.getElementById('deskripsi').value = data.deskripsi;
                document.getElementById('status').value = data.status;
            } else {
                Swal.fire('Gagal', result.message || 'Data tidak ditemukan', 'error');
            }
        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'Gagal mengambil data fasilitas', 'error');
        }

        // Submit form update
        document.getElementById('formEditFasilitas').addEventListener('submit', async (e) => {
            e.preventDefault();

            const data = {
                nama_fasilitas: document.getElementById('nama_fasilitas').value,
                deskripsi: document.getElementById('deskripsi').value,
                status: document.getElementById('status').value
            };

            try {
                const res = await fetch(`/api/fasilitas/${id_fasilitas}`, {
                    method: 'PUT',
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
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                    }).then(() => {
                        window.location.href = "{{ route('admin.fasilitas.index') }}";
                    });
                } else {
                    Swal.fire('Gagal', result.message || 'Gagal memperbarui fasilitas', 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan saat update', 'error');
            }
        });
    });
</script>
@endsection
