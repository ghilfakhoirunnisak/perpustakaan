@extends('admin.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Edit Pengajuan Buku</h4>

    <div class="card shadow">
        <div class="card-body">
            <form id="formEditPengajuan">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">ID User</label>
                        <input type="text" name="id_user" class="form-control" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" required>
                    </div>

                    {{-- Buku Section --}}
                    <div class="col-md-12 mt-4">
                        <label class="form-label">Buku</label>
                        <div id="buku-container"></div>

                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="btnTambahBuku">
                            <i class="bx bx-plus"></i> Tambah Buku
                        </button>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.pengajuanbuku.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const id = `{{ $id_pengajuan_buku }}`;

    const bukuRes = await fetch('/api/buku');
    const bukuData = await bukuRes.json();
    const bukuOptions = bukuData.data;

    function renderBukuSelect(selectedId = '') {
        return bukuOptions.map(b => {
            const selected = b.id_buku == selectedId ? 'selected' : '';
            return `<option value="${b.id_buku}" ${selected}>${b.judul}</option>`;
        }).join('');
    }

    // Fetch pengajuan utama
    const resPengajuan = await fetch(`/api/pengajuanbuku/${id}`);
    const pengajuan = (await resPengajuan.json()).data;
    document.querySelector('[name="id_user"]').value = pengajuan.id_user;
    document.querySelector('[name="nama_lengkap"]').value = pengajuan.nama_lengkap;
    document.querySelector('[name="alamat"]').value = pengajuan.alamat;

    // Fetch detail buku
    const detailRes = await fetch(`/api/detailpengajuanbuku/${id}`);
    const detailList = (await detailRes.json()).data;
    const container = document.getElementById('buku-container');
    container.innerHTML = '';

    detailList.forEach(detail => {
        container.appendChild(makeBukuItem(detail.id_buku, detail.jumlah, detail.id_detail_pengajuan_buku));
    });

    function makeBukuItem(id_buku = '', jumlah = '', id_detail = '') {
        const row = document.createElement('div');
        row.classList.add('row', 'g-2', 'mb-2', 'buku-item');
        row.dataset.detailId = id_detail;
        row.innerHTML = `
            <input type="hidden" name="detail_ids[]" value="${id_detail}">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-book"></i></span>
                    <select name="id_buku[]" class="form-select" required>
                        ${renderBukuSelect(id_buku)}
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-plus"></i></span>
                    <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" value="${jumlah}" required>
                </div>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-remove-buku"><i class="bx bx-trash"></i></button>
            </div>
        `;
        return row;
    }

    document.getElementById('btnTambahBuku').addEventListener('click', () => {
        container.appendChild(makeBukuItem());
    });

    container.addEventListener('click', async function (e) {
        if (e.target.closest('.btn-remove-buku')) {
            const item = e.target.closest('.buku-item');
            const detailId = item.dataset.detailId;
            if (detailId) {
                await fetch(`/api/detailpengajuanbuku/${detailId}`, { method: 'DELETE' });
            }
            item.remove();
        }
    });

    document.getElementById('formEditPengajuan').addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = e.target;

        // Update pengajuan utama
        const payloadUtama = {
            nama_lengkap: form.nama_lengkap.value,
            alamat: form.alamat.value,
        };

        await fetch(`/api/pengajuanbuku/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payloadUtama)
        });

        const detailIds = form.querySelectorAll('[name="detail_ids[]"]');
        const idBuku = form.querySelectorAll('[name="id_buku[]"]');
        const jumlah = form.querySelectorAll('[name="jumlah[]"]');

        for (let i = 0; i < idBuku.length; i++) {
            const detailPayload = {
                id_buku: idBuku[i].value,
                jumlah: jumlah[i].value,
            };

            const detailId = detailIds[i]?.value;
            if (detailId) {
                await fetch(`/api/detailpengajuanbuku/${detailId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(detailPayload)
                });
            } else {
                detailPayload.id_pengajuan_buku = id;
                await fetch(`/api/detailpengajuanbuku`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(detailPayload)
                });
            }
        }

        Swal.fire('Berhasil', 'Pengajuan berhasil diperbarui!', 'success').then(() => {
            window.location.href = "{{ route('admin.pengajuanbuku.index') }}";
        });
    });
});
</script>
@endsection
