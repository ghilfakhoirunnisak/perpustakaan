@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Account Settings / Verifikator /</span> Edit Verifikator
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <form id="formEditVerifikator">
                    {{-- Nama User (readonly) --}}
                    <div class="mb-3">
                        <label for="id_user_display" class="form-label fw-semibold">Nama User</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <input type="text" id="id_user_display" class="form-control bg-light" readonly>
                        </div>
                    </div>

                    {{-- Level --}}
                    <div class="mb-3">
                        <label for="level" class="form-label fw-semibold">Level Verifikator</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-layer"></i></span>
                            <input type="number" class="form-control" id="level" name="level"
                                placeholder="Contoh: 1">
                        </div>
                    </div>

                    {{-- Jabatan --}}
                    <div class="mb-3">
                        <label for="jabatan" class="form-label fw-semibold">Jabatan</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-briefcase"></i></span>
                            <input type="text" class="form-control" id="jabatan" name="jabatan"
                                placeholder="Contoh: Ketua Tim">
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-toggle-left"></i></span>
                            <select class="form-select" id="status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.verifikator.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const id_verifikator = `{{ request()->segment(3) }}`;
        const token = localStorage.getItem('auth_token');

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const res = await fetch(`/api/verifikator/${id_verifikator}`, {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });

                const result = await res.json();

                if (res.ok && result.success) {
                    const data = result.data;
                    const userDisplay = `${data.id_user} - ${data.user?.nama ?? '-'}`;
                    document.getElementById('id_user_display').value = userDisplay;
                    document.getElementById('level').value = data.level;
                    document.getElementById('jabatan').value = data.jabatan;
                    document.getElementById('status').value = data.status;
                } else {
                    alert(result.message || 'Gagal memuat data verifikator');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat memuat data');
            }
        });

        document.getElementById('formEditVerifikator').addEventListener('submit', async (e) => {
            e.preventDefault();

            const payload = {
                level: document.getElementById('level').value,
                jabatan: document.getElementById('jabatan').value,
                status: document.getElementById('status').value
            };

            try {
                const res = await fetch(`/api/verifikator/${id_verifikator}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await res.json();

                if (res.ok && result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: result.message,
                    }).then(() => {
                        window.location.href = `/admin/verifikator`;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: result.message || 'Terjadi kesalahan.',
                    });
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengirim data.',
                });
            }
        });
    </script>
@endsection
