@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Account Settings / Data User /</span> Detail User
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">ID User</label>
                    <div class="form-control bg-light" id="id_user">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">ID Role</label>
                    <div class="form-control bg-light" id="id_role">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Role</label>
                    <div class="form-control bg-light" id="nama_role">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama</label>
                    <div class="form-control bg-light" id="nama">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Telepon</label>
                    <div class="form-control bg-light" id="telp">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <div class="form-control bg-light" id="email">-</div>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const idUser = `{{ request()->segment(3) }}`;
        const token = localStorage.getItem('auth_token');

        async function loadUserDetail() {
            try {
                const res = await fetch(`/api/user/${idUser}`, {
                    headers: {
                        'Authorization': token,
                        'Accept': 'application/json'
                    }
                });

                const result = await res.json();

                if (!result.success) {
                    Swal.fire('Gagal', result.message || 'Data tidak ditemukan', 'error');
                    return;
                }

                const data = result.data;

                document.getElementById('id_user').textContent = data.id_user ?? '-';
                document.getElementById('id_role').textContent = data.id_role ?? '-';
                document.getElementById('nama_role').textContent = data.role?.nama_role ?? '-';
                document.getElementById('nama').textContent = data.nama ?? '-';
                document.getElementById('telp').textContent = data.telp ?? '-';
                document.getElementById('email').textContent = data.email ?? '-';

            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Gagal memuat data user.', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', loadUserDetail);
    </script>
@endsection
