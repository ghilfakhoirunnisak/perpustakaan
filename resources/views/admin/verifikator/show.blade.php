@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Account Settings / Data Verifikator /</span> Detail Verifikator
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">ID Verifikator</label>
                    <div class="form-control bg-light" id="id_verifikator">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">ID User</label>
                    <div class="form-control bg-light" id="id_user">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama</label>
                    <div class="form-control bg-light" id="nama">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Level</label>
                    <div class="form-control bg-light" id="level">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Jabatan</label>
                    <div class="form-control bg-light" id="jabatan">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <div class="form-control bg-light" id="status">-</div>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.verifikator.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const id = `{{ request()->segment(3) }}`;
        const token = localStorage.getItem('auth_token');

        async function loadVerifikatorDetail() {
            try {
                const res = await fetch(`/api/verifikator/${id}`, {
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
                const user = data.user ?? {};

                document.getElementById('id_verifikator').textContent = data.id_verifikator ?? '-';
                document.getElementById('id_user').textContent = user.id_user ?? '-';
                document.getElementById('nama').textContent = user.nama ?? '-';
                document.getElementById('level').textContent = data.level ? `Level ${data.level}` : '-';
                document.getElementById('jabatan').textContent = data.jabatan ?? '-';
                document.getElementById('status').textContent = data.status ?? '-';

            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Gagal memuat data verifikator.', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', loadVerifikatorDetail);
    </script>
@endsection
