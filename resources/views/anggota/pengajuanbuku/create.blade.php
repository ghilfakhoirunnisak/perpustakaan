@extends('anggota.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-4">Ajukan Peminjaman Buku</h4>

        <div class="card shadow">
            <div class="card-body">
                <form id="formPengajuan">
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Buku yang Diajukan</label>
                        <div id="buku-container">
                            <div class="row g-2 align-items-end buku-item">
                                <div class="col-md-7">
                                    <label class="form-label">Judul Buku</label>
                                    <select name="id_buku[]" class="form-select" required></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="jumlah[]" class="form-control" required>
                                </div>
                                <div class="col-md-2 d-grid">
                                    <button type="button" class="btn btn-danger btn-remove-buku mt-4">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary mt-3" id="btnTambahBuku">
                            <i class="bx bx-plus"></i> Tambah Buku
                        </button>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Simpan
                        </button>
                        <a href="{{ route('anggota.pengajuanbuku.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const bukuSelect = document.querySelector('[name="id_buku[]"]');

            async function loadBukuOptions() {
                const res = await fetch('/api/buku');
                const data = await res.json();
                return `<option disabled selected>-- Pilih Buku --</option>` +
                    data.data.map(b => `<option value="${b.id_buku}">${b.id_buku} - ${b.judul}</option>`)
                    .join('');
            }


            bukuSelect.innerHTML = await loadBukuOptions();

            document.getElementById('btnTambahBuku').addEventListener('click', async () => {
                const options = await loadBukuOptions();
                const item = document.createElement('div');
                item.classList.add('row', 'g-2', 'align-items-end', 'buku-item', 'mt-2');
                item.innerHTML = `
            <div class="col-md-7">
                <label class="form-label">Judul Buku</label>
                <select name="id_buku[]" class="form-select" required>${options}</select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jumlah</label>
                <input type="number" name="jumlah[]" class="form-control" required>
            </div>
            <div class="col-md-2 d-grid">
                <button type="button" class="btn btn-danger btn-remove-buku mt-4"><i class="bx bx-trash"></i></button>
            </div>
        `;
                document.getElementById('buku-container').appendChild(item);
            });

            document.getElementById('buku-container').addEventListener('click', e => {
                if (e.target.closest('.btn-remove-buku')) {
                    e.target.closest('.buku-item').remove();
                }
            });

            document.getElementById('formPengajuan').addEventListener('submit', async function(e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);

                const pengajuanRes = await fetch('/api/pengajuanbuku/anggota', {
                    method: 'POST',
                    body: formData
                });
                const pengajuanData = await pengajuanRes.json();
                if (!pengajuanData.success) {
                    Swal.fire('Gagal', pengajuanData.message || 'Terjadi kesalahan.', 'error');
                    return;
                }

                const id_pengajuan_buku = pengajuanData.data.id_pengajuan_buku;

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

                Swal.fire('Berhasil', 'Pengajuan berhasil dikirim!', 'success').then(() => {
                    window.location.href = "{{ route('anggota.pengajuanbuku.index') }}";
                });
            });
        });
    </script>
@endsection
