@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Reservasi Fasilitas /</span> Edit Reservasi
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <form id="formEditReservasi" enctype="multipart/form-data">
                    <input type="hidden" id="id_reservasi">

                    {{-- Kode Reservasi --}}
                    <div class="mb-3">
                        <label class="form-label">Kode Reservasi</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-barcode"></i></span>
                            <input type="text" id="kode_reservasi" class="form-control bg-light text-dark" readonly />
                        </div>
                    </div>

                    {{-- Nama User --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Pengguna</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <input type="text" id="nama_user" class="form-control bg-light text-dark" readonly />
                        </div>
                    </div>

                    {{-- Fasilitas --}}
                    <div class="mb-3">
                        <label class="form-label">Fasilitas</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-building-house"></i></span>
                            <select id="id_fasilitas" class="form-select" required></select>
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kegiatan</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                            <input type="date" id="tanggal_kegiatan" class="form-control" required />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-calendar-check"></i></span>
                            <input type="date" id="tanggal_selesai" class="form-control" required />
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-note"></i></span>
                            <textarea id="keterangan" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    {{-- Dokumen Sebelumnya --}}
                    <div class="mb-3">
                        <label class="form-label">Dokumen Sebelumnya</label>
                        <div id="dokumenLama" class="form-control bg-light text-muted" style="min-height: 40px;">
                            <em>Memuat...</em>
                        </div>
                    </div>

                    {{-- Upload Baru --}}
                    <div class="mb-3">
                        <label class="form-label">Dokumen Baru</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-upload"></i></span>
                            <input type="file" name="dokumen" id="dokumen" class="form-control"
                                accept=".pdf,.jpg,.jpeg,.png" />
                        </div>
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti dokumen.</small>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.reservasifasilitas.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        const idReservasi = `{{ request()->segment(3) }}`;
        const token = localStorage.getItem('auth_token');

        async function loadFasilitas() {
            const res = await fetch(`/api/fasilitas`, {
                headers: {
                    'Authorization': token
                }
            });
            const result = await res.json();
            const select = document.getElementById('id_fasilitas');
            select.innerHTML = '';
            result.data.forEach(f => {
                const opt = document.createElement('option');
                opt.value = f.id_fasilitas;
                opt.textContent = f.nama_fasilitas;
                select.appendChild(opt);
            });
        }

        async function loadDetailReservasi() {
            const res = await fetch(`/api/reservasifasilitas/${idReservasi}`, {
                headers: {
                    'Authorization': token
                }
            });
            const result = await res.json();
            if (result.success) {
                const r = result.data;
                document.getElementById('id_reservasi').value = r.id_reservasi;
                document.getElementById('kode_reservasi').value = r.kode_reservasi;
                document.getElementById('nama_user').value = r.user?.nama ?? '-';
                document.getElementById('tanggal_kegiatan').value = r.tanggal_kegiatan;
                document.getElementById('tanggal_selesai').value = r.tanggal_selesai;
                document.getElementById('keterangan').value = r.keterangan || '';
                document.getElementById('id_fasilitas').value = r.id_fasilitas;
            } else {
                Swal.fire('Gagal', result.message, 'error');
            }
        }

        async function loadDokumenLama() {
            const res = await fetch(`/api/dokumen?id_reservasi=${idReservasi}`, {
                headers: {
                    'Authorization': token
                }
            });
            const result = await res.json();
            const container = document.getElementById('dokumenLama');
            container.innerHTML = '';
            if (result.success && result.data.length) {
                result.data.forEach(d => {
                    const link = document.createElement('a');
                    link.href = `/storage/${d.path_file}`;
                    link.textContent = d.nama_file;
                    link.target = '_blank';
                    link.classList.add('d-block');
                    container.appendChild(link);
                });
            } else {
                container.innerHTML = '<em>Tidak ada dokumen</em>';
            }
        }

        document.getElementById('formEditReservasi').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('id_reservasi').value;

            try {
                // Update reservasi fasilitas
                const data = {
                    id_fasilitas: document.getElementById('id_fasilitas').value,
                    tanggal_kegiatan: document.getElementById('tanggal_kegiatan').value,
                    tanggal_selesai: document.getElementById('tanggal_selesai').value,
                    keterangan: document.getElementById('keterangan').value
                };

                const res1 = await fetch(`/api/reservasifasilitas/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result1 = await res1.json();
                if (!result1.success) throw new Error(result1.message);

                // Jika ada dokumen baru diinput, ganti semua dokumen lama
                const file = document.getElementById('dokumen').files[0];
                if (file) {
                    const fd = new FormData();
                    fd.append('dokumen[]', file); // tetap array karena multiple bisa diaktifkan nanti

                    const res2 = await fetch(`/api/dokumen/update-by-reservasi/${id}`, {
                        method: 'POST',
                        headers: {
                            'Authorization': token,
                            'X-HTTP-Method-Override': 'PUT'
                        },
                        body: fd
                    });

                    const result2 = await res2.json();
                    if (!result2.success) throw new Error(result2.message);
                }

                Swal.fire('Berhasil', 'Reservasi berhasil diperbarui.', 'success')
                    .then(() => window.location.href = '/admin/reservasifasilitas');
            } catch (err) {
                Swal.fire('Error', err.message || 'Terjadi kesalahan saat menyimpan.', 'error');
            }
        });

        document.addEventListener('DOMContentLoaded', async () => {
            await loadFasilitas();
            await loadDetailReservasi();
            await loadDokumenLama();
        });
    </script>
@endsection
