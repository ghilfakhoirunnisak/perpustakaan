@extends('verifikator.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Verifikasi /</span> Detail Pengajuan Buku
        </h4>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">ID Pengajuan</label>
                    <div class="form-control bg-light" id="id_pengajuan">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">ID User</label>
                    <div class="form-control bg-light" id="id_user">-</div>
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

                <div class="mb-4">
                    <label class="form-label fw-semibold">Daftar Buku</label>
                    <ul class="list-group" id="daftarBuku">
                        <li class="list-group-item">Memuat data...</li>
                    </ul>
                </div>

                <div class="text-end">
                    <a href="{{ route('verifikator.pengajuanbuku.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow" id="approvalSection">
            <div class="card-body" id="approvalBody">
                <h5 class="fw-bold mb-3">Verifikasi Pengajuan</h5>
                <form id="approvalForm">
                    <div class="mb-3">
                        <label for="statusApproval" class="form-label">Keputusan</label>
                        <select name="status" id="statusApproval" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea name="catatan" id="catatan" rows="3" class="form-control"
                            placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Kirim Keputusan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const pathSegments = window.location.pathname.split('/');
            const id_pengajuan_buku = pathSegments[pathSegments.length - 2];

            const approvalSection = document.getElementById('approvalSection');
            const approvalBody = document.getElementById('approvalBody');

            if (!id_pengajuan_buku) {
                document.getElementById('daftarBuku').innerHTML = '<li class="list-group-item text-danger">ID Pengajuan tidak ditemukan di URL.</li>';
                if (approvalSection) approvalSection.style.display = 'none';
                return;
            }

            function fetchDetailPengajuan() {
                document.getElementById('daftarBuku').innerHTML = '<li class="list-group-item">Memuat data buku...</li>';

                fetch(`/api/pengajuanbuku/${id_pengajuan_buku}`)
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error("API Response (Error):", text);
                                throw new Error(`HTTP Status: ${response.status}. Expected JSON, got HTML or unexpected content.`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.data) {
                            const pengajuan = data.data;
                            const verifikatorStatus = data.verifikator_status;

                            document.getElementById('id_pengajuan').textContent = pengajuan.id_pengajuan_buku || '-';
                            document.getElementById('id_user').textContent = pengajuan.id_user || '-';
                            document.getElementById('nama_lengkap').textContent = pengajuan.nama_lengkap || '-';
                            document.getElementById('alamat').textContent = pengajuan.alamat || '-';
                            document.getElementById('status').textContent = pengajuan.status || '-';

                            const daftarBukuUl = document.getElementById('daftarBuku');
                            daftarBukuUl.innerHTML = '';

                            if (pengajuan.detail_pengajuan_buku && pengajuan.detail_pengajuan_buku.length > 0) {
                                pengajuan.detail_pengajuan_buku.forEach(detail => {
                                    const li = document.createElement('li');
                                    li.className = 'list-group-item';
                                    const judulBuku = detail.buku?.judul ?? 'Judul tidak ditemukan';
                                    li.textContent = `${judulBuku} (${detail.jumlah} buku)`;
                                    daftarBukuUl.appendChild(li);
                                });
                            } else {
                                const li = document.createElement('li');
                                li.className = 'list-group-item';
                                li.textContent = 'Tidak ada buku yang diajukan.';
                                daftarBukuUl.appendChild(li);
                            }

                            if (verifikatorStatus && (verifikatorStatus.has_approved_by_current_user || verifikatorStatus.is_decision_made_globally)) {
                                approvalBody.innerHTML = `
                                <h5 class="fw-bold mb-3">Verifikasi Pengajuan</h5>
                                <div class="alert alert-info" role="alert">
                                    Anda sudah memberikan keputusan untuk pengajuan ini:
                                    <strong>${verifikatorStatus.current_user_decision === 'disetujui' ? 'Disetujui' : 'Ditolak'}</strong>.
                                    <br>
                                    ${pengajuan.status !== 'diproses' && verifikatorStatus.is_decision_made_globally ?
                                        `Status pengajuan saat ini: <strong>${pengajuan.status}</strong>.` :
                                        ''
                                    }
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('verifikator.pengajuanbuku.index') }}" class="btn btn-secondary">
                                        Kembali ke Daftar Pengajuan
                                    </a>
                                </div>
                                `;
                            } else {
                                approvalSection.style.display = 'block';
                                approvalBody.innerHTML = `
                                <h5 class="fw-bold mb-3">Verifikasi Pengajuan</h5>
                                <form id="approvalForm">
                                    <div class="mb-3">
                                        <label for="statusApproval" class="form-label">Keputusan</label>
                                        <select name="status" id="statusApproval" class="form-select" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="disetujui">Disetujui</option>
                                            <option value="ditolak">Ditolak</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="catatan" class="form-label">Catatan</label>
                                        <textarea name="catatan" id="catatan" rows="3" class="form-control"
                                            placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success">Kirim Keputusan</button>
                                    </div>
                                </form>
                                `;
                                attachApprovalFormListener();
                            }
                        } else {
                            Swal.fire('Gagal!', data.message || 'Gagal mengambil detail pengajuan.', 'error');
                            document.getElementById('daftarBuku').innerHTML =
                                `<li class="list-group-item text-danger">${data.message || 'Data tidak berhasil dimuat.'}</li>`;
                            if (approvalSection) approvalSection.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error saat fetch detail pengajuan:', error);
                        Swal.fire('Error!', error.message || 'Terjadi kesalahan saat mengambil detail pengajuan.', 'error');
                        document.getElementById('daftarBuku').innerHTML =
                            '<li class="list-group-item text-danger">Gagal memuat data buku. Periksa konsol browser atau log server.</li>';
                        if (approvalSection) approvalSection.style.display = 'none';
                    });
            }

            function attachApprovalFormListener() {
                const approvalForm = document.getElementById('approvalForm');
                if (approvalForm) {
                    approvalForm.addEventListener('submit', function(event) {
                        event.preventDefault();

                        const status = document.getElementById('statusApproval').value;
                        const catatan = document.getElementById('catatan').value;

                        if (!status) {
                            Swal.fire('Peringatan', 'Silakan pilih keputusan (Disetujui/Ditolak).', 'warning');
                            return;
                        }

                        Swal.fire({
                            title: 'Konfirmasi Keputusan',
                            text: `Anda yakin ingin ${status === 'disetujui' ? 'menyetujui' : 'menolak'} pengajuan ini?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Kirim!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch(`/api/approvalbuku/${id_pengajuan_buku}/approve`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            status: status,
                                            catatan: catatan
                                        })
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            return response.json().then(err => {
                                                throw new Error(err.message || 'Terjadi kesalahan pada server.');
                                            });
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire('Berhasil!', data.message, 'success')
                                                .then(() => window.location.href = "{{ route('verifikator.pengajuanbuku.index') }}");
                                        } else {
                                            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan saat mengirim keputusan.', 'error');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        Swal.fire('Error!', error.message || 'Terjadi kesalahan jaringan atau server.', 'error');
                                    });
                            }
                        });
                    });
                }
            }

            fetchDetailPengajuan();
            attachApprovalFormListener();
        });
    </script>
@endsection
