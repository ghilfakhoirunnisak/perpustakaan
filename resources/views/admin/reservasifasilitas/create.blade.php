@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Reservasi Fasilitas /</span> Tambah Reservasi
        </h4>

        <div class="card shadow">
            <div class="card-body">
                <form id="formReservasi">

                    {{-- Pilih User --}}
                    <div class="mb-3">
                        <label for="id_user" class="form-label">Pilih User (Anggota)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <select class="form-select" id="id_user" name="id_user" required>
                                <option value="">-- Pilih User --</option>
                            </select>
                        </div>
                    </div>

                    {{-- Pilih Fasilitas --}}
                    <div class="mb-3">
                        <label for="id_fasilitas" class="form-label">Pilih Fasilitas</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-building-house"></i></span>
                            <select class="form-select" id="id_fasilitas" name="id_fasilitas" required>
                                <option value="">-- Pilih Fasilitas --</option>
                            </select>
                        </div>
                    </div>

                    {{-- Tanggal Kegiatan --}}
                    <div class="mb-3">
                        <label for="tanggal_kegiatan" class="form-label">Tanggal Kegiatan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                            <input type="date" class="form-control" id="tanggal_kegiatan" name="tanggal_kegiatan"
                                required>
                        </div>
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div class="mb-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-calendar-alt"></i></span>
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-note"></i></span>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Opsional..."></textarea>
                        </div>
                    </div>

                    {{-- Upload Dokumen --}}
                    <div class="mb-3">
                        <label for="dokumen" class="form-label">Upload Dokumen</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-file"></i></span>
                            <input type="file" class="form-control" id="dokumen" name="dokumen[]" multiple
                                accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <small class="text-muted">Format: PDF, JPG, PNG. Maksimal 2MB per file.</small>
                    </div>

                    {{-- Tombol --}}
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Simpan
                        </button>
                        <a href="{{ route('admin.reservasifasilitas.index') }}" class="btn btn-secondary">Batal</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Script --}}
    <script>
        const token = localStorage.getItem('auth_token');

        fetch('/api/user?role=anggota', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('id_user');
                    data.data.forEach(user => {
                        select.innerHTML +=
                            `<option value="${user.id_user}">${user.id_user} - ${user.nama}</option>`;
                    });
                }
            });

        // Load fasilitas
        fetch('/api/fasilitas', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('id_fasilitas');
                    data.data.forEach(fasilitas => {
                        select.innerHTML +=
                            `<option value="${fasilitas.id_fasilitas}">${fasilitas.id_fasilitas} - ${fasilitas.nama_fasilitas}</option>`;
                    });
                }
            });

        // Submit form
        document.getElementById('formReservasi').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const dokumenInput = document.getElementById('dokumen');

            try {
                // Simpan reservasi
                const res = await fetch('/api/reservasifasilitas', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await res.json();
                if (!res.ok || !result.success) {
                    throw new Error(result.message || 'Gagal menyimpan reservasi.');
                }

                const id_reservasi = result.data.id_reservasi;

                // Upload dokumen jika ada
                if (dokumenInput.files.length > 0) {
                    const dokumenForm = new FormData();
                    dokumenForm.append('id_reservasi', id_reservasi);
                    for (const file of dokumenInput.files) {
                        dokumenForm.append('dokumen[]', file);
                    }

                    const resDoc = await fetch('/api/dokumen', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        },
                        body: dokumenForm
                    });

                    const resultDoc = await resDoc.json();
                    if (!resDoc.ok || !resultDoc.success) {
                        throw new Error(resultDoc.message || 'Gagal upload dokumen.');
                    }
                }

                Swal.fire('Berhasil', 'Reservasi dan dokumen berhasil disimpan.', 'success')
                    .then(() => window.location.href = "{{ route('admin.reservasifasilitas.index') }}");

            } catch (err) {
                console.error(err);
                Swal.fire('Error', err.message || 'Terjadi kesalahan saat menyimpan.', 'error');
            }
        });
    </script>
@endsection
