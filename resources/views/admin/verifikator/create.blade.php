@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Account Settings / Data Verifikator /</span> Tambah Verifikator
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <form id="formVerifikator">
                    {{-- User --}}
                    <div class="mb-3">
                        <label for="id_user" class="form-label">Nama User</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <select class="form-select" id="id_user" name="id_user" required>
                                <option value=""> Pilih User Verifikator </option>
                            </select>
                        </div>
                        <div class="form-text text-danger" id="error-id_user"></div>
                    </div>

                    {{-- Level --}}
                    <div class="mb-3">
                        <label for="level" class="form-label">Level Verifikator</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-layer"></i></span>
                            <input type="number" class="form-control" id="level" name="level"
                                placeholder="Masukkan level" required>
                        </div>
                        <div class="form-text text-danger" id="error-level"></div>
                    </div>

                    {{-- Jabatan --}}
                    <div class="mb-3">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-briefcase"></i></span>
                            <input type="text" class="form-control" id="jabatan" name="jabatan"
                                placeholder="Masukkan jabatan" required>
                        </div>
                        <div class="form-text text-danger" id="error-jabatan"></div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-toggle-left"></i></span>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div class="form-text text-danger" id="error-status"></div>
                    </div>

                    {{-- Tombol --}}
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Simpan
                        </button>
                        <a href="{{ route('admin.verifikator.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('auth_token');
            const userSelect = document.getElementById('id_user');

            try {
                const res = await fetch('/api/user?role=verifikator', {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });

                const result = await res.json();

                if (result.success) {
                    result.data.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id_user;
                        option.textContent = `${user.id_user} - ${user.nama}`;
                        userSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Gagal memuat user:', error);
            }

            const form = document.getElementById('formVerifikator');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = {
                    id_user: form.id_user.value,
                    level: form.level.value,
                    jabatan: form.jabatan.value,
                    status: form.status.value
                };

                try {
                    const res = await fetch('/api/verifikator', {
                        method: 'POST',
                        headers: {
                            'Authorization': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    const result = await res.json();

                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.message || 'Data berhasil disimpan.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '/admin/verifikator';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: result.message || 'Terjadi kesalahan saat menyimpan.',
                        });
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan!',
                        text: 'Gagal menyimpan data. Periksa koneksi atau sistem Anda.',
                    });
                }
            });
        });
    </script>
@endsection
