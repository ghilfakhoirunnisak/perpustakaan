@extends('admin.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Tambah Pengajuan Buku</h4>

    <div class="card shadow">
        <div class="card-body">
            <form id="formPengajuan">
                @csrf
                <div class="row g-3">
                    {{-- User --}}
                    <div class="col-md-6">
                        <label class="form-label">User</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-user-circle"></i></span>
                            <select name="id_user" class="form-select" required></select>
                        </div>
                    </div>

                    {{-- Nama Lengkap --}}
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <input type="text" name="nama_lengkap" class="form-control" required>
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="col-md-12">
                        <label class="form-label">Alamat</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-home"></i></span>
                            <textarea name="alamat" class="form-control" rows="2" required></textarea>
                        </div>
                    </div>

                    {{-- Buku --}}
                    <div class="col-md-12">
                        <label class="form-label">Buku</label>
                        <div id="buku-container">
                            <div class="row g-2 mb-2 buku-item">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-book"></i></span>
                                        <select name="id_buku[]" class="form-select" required></select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-plus"></i></span>
                                        <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" required>
                                    </div>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-danger btn-remove-buku"><i class="bx bx-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="btnTambahBuku">
                            <i class="bx bx-plus"></i> Tambah Buku
                        </button>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save"></i> Simpan
                    </button>
                    <a href="{{ route('admin.pengajuanbuku.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const userSelect = document.querySelector('[name="id_user"]');
    const firstBukuSelect = document.querySelector('[name="id_buku[]"]');

    // Load user (anggota)
    const userRes = await fetch('/api/user?role=anggota');
    const userData = await userRes.json();
    userSelect.innerHTML = `<option value="">-- Pilih User --</option>`;
    userData.data.forEach(u => {
        userSelect.innerHTML += `<option value="${u.id_user}">${u.nama}</option>`;
    });

    // Fungsi untuk load buku
    async function loadBukuOptions() {
        const bukuRes = await fetch('/api/buku');
        const bukuData = await bukuRes.json();
        const options = [`<option value="">-- Pilih Buku --</option>`];
        bukuData.data.forEach(b => {
            options.push(`<option value="${b.id_buku}">${b.id_buku} - ${b.judul}</option>`);
        });
        return options.join('');
    }

    // Isi select pertama
    if (firstBukuSelect) {
        firstBukuSelect.innerHTML = await loadBukuOptions();
    }

    // Tambah buku baru
    document.getElementById('btnTambahBuku').addEventListener('click', async () => {
        const options = await loadBukuOptions();
        const newItem = document.createElement('div');
        newItem.classList.add('row', 'g-2', 'mb-2', 'buku-item');
        newItem.innerHTML = `
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-book"></i></span>
                    <select name="id_buku[]" class="form-select" required>
                        ${options}
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-sort"></i></span>
                    <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" required>
                </div>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-remove-buku"><i class="bx bx-trash"></i></button>
            </div>`;
        document.getElementById('buku-container').appendChild(newItem);
    });

    // Hapus buku-item
    document.getElementById('buku-container').addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-buku')) {
            e.target.closest('.buku-item').remove();
        }
    });

    // Submit form
    document.getElementById('formPengajuan').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        // Validasi manual user
        if (!form.id_user.value) {
            Swal.fire('Peringatan', 'Silakan pilih user terlebih dahulu.', 'warning');
            return;
        }

        // 1. Kirim ke pengajuan buku
        const pengajuanRes = await fetch('/api/pengajuanbuku', {
            method: 'POST',
            body: formData
        });
        const pengajuanData = await pengajuanRes.json();
        if (!pengajuanData.success) {
            Swal.fire('Gagal', pengajuanData.message || 'Terjadi kesalahan.', 'error');
            return;
        }

        const id_pengajuan_buku = pengajuanData.data.id_pengajuan_buku;

        // 2. Kirim semua detail
        const id_buku_list = form.querySelectorAll('[name="id_buku[]"]');
        const jumlah_list = form.querySelectorAll('[name="jumlah[]"]');

        for (let i = 0; i < id_buku_list.length; i++) {
            const id_buku = id_buku_list[i].value;
            const jumlah = jumlah_list[i].value;

            if (!id_buku || !jumlah) continue;

            await fetch('/api/detailpengajuanbuku', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id_pengajuan_buku,
                    id_buku,
                    jumlah
                })
            });
        }

        Swal.fire('Berhasil', 'Pengajuan berhasil disimpan!', 'success').then(() => {
            window.location.href = "{{ route('admin.pengajuanbuku.index') }}";
        });
    });
});
</script>
@endsection
