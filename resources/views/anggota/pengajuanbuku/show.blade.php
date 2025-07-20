@extends('anggota.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Data Pengajuan /</span> Detail Pengajuan Buku
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">ID Pengajuan</label>
                    <div class="form-control bg-light" id="id_pengajuan">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <div class="form-control bg-light" id="nama_lengkap">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat</label>
                    <div class="form-control bg-light" id="alamat">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <div class="form-control bg-light" id="status">-</div>
                </div>

                <div class="mb-3" id="catatanWrapper" style="display: none;">
                    <label class="form-label fw-semibold">Catatan Penolakan</label>
                    <div class="form-control bg-light" id="catatan">-</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Daftar Buku</label>
                    <ul class="list-group" id="daftarBuku">
                        <li class="list-group-item">Memuat data...</li>
                    </ul>
                </div>

                <div class="text-end">
                    <a href="{{ route('anggota.pengajuanbuku.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const idPengajuan = `{{ request()->segment(3) }}`;
        const token = localStorage.getItem("auth_token");

        async function loadDetailPengajuan() {
            try {
                const res = await fetch(`/api/pengajuanbuku/${idPengajuan}`, {
                    headers: { 'Authorization': token }
                });

                const result = await res.json();

                if (!result.success) {
                    Swal.fire('Gagal', result.message || 'Data tidak ditemukan', 'error');
                    return;
                }

                const data = result.data;
                document.getElementById('id_pengajuan').textContent = data.id_pengajuan_buku ?? '-';
                document.getElementById('nama_lengkap').textContent = data.nama_lengkap ?? '-';
                document.getElementById('alamat').textContent = data.alamat ?? '-';
                document.getElementById('status').textContent = data.status ?? '-';

                if (data.status === 'ditolak') {
                    document.getElementById('catatanWrapper').style.display = 'block';
                    document.getElementById('catatan').textContent = data.catatan ?? '(Tidak ada catatan)';
                } else {
                    document.getElementById('catatanWrapper').style.display = 'none';
                }

            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Gagal memuat data pengajuan.', 'error');
            }
        }

        async function loadDetailBuku() {
            try {
                const res = await fetch(`/api/detailpengajuanbuku/${idPengajuan}`, {
                    headers: { 'Authorization': token }
                });

                const result = await res.json();
                const list = document.getElementById('daftarBuku');
                list.innerHTML = '';

                if (!result.success || !result.data.length) {
                    list.innerHTML = '<li class="list-group-item text-muted">Tidak ada buku.</li>';
                    return;
                }

                result.data.forEach(detail => {
                    list.innerHTML += `
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">${detail.buku?.judul ?? '(Judul tidak ditemukan)'}</div>
                                Jumlah: ${detail.jumlah}
                            </div>
                        </li>
                    `;
                });

            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Gagal memuat daftar buku.', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadDetailPengajuan();
            loadDetailBuku();
        });
    </script>
@endsection
