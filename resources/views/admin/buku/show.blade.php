@extends('admin.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Detail Buku</h4>

    <div class="card shadow">
        <div class="card-body">
            <div class="row">
                <!-- Cover -->
                <div class="col-md-4 text-center mb-4">
                    <img id="cover-preview" src="{{ asset('no-cover.png') }}"
                         class="img-fluid rounded shadow"
                         style="max-height: 300px;"
                         alt="Cover Buku">
                </div>

                <!-- Detail Buku -->
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 150px;">Judul</th>
                            <td id="judul"></td>
                        </tr>
                        <tr>
                            <th>ISBN</th>
                            <td id="isbn"></td>
                        </tr>
                        <tr>
                            <th>Penulis</th>
                            <td id="penulis"></td>
                        </tr>
                        <tr>
                            <th>Penerbit</th>
                            <td id="penerbit"></td>
                        </tr>
                        <tr>
                            <th>Genre</th>
                            <td id="genre"></td>
                        </tr>
                        <tr>
                            <th>Tahun Terbit</th>
                            <td id="tahun_terbit"></td>
                        </tr>
                        <tr>
                            <th>Stok</th>
                            <td id="stok"></td>
                        </tr>
                        <tr>
                            <th>Sinopsis</th>
                            <td id="sinopsis" style="white-space: pre-line;"></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4 text-end">
                <a href="{{ route('admin.buku.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const parts = window.location.pathname.split('/');
    const id = parts[parts.length - 2]; // karena /admin/buku/{id}/show

    fetch(`/api/buku/${id}`)
        .then(res => res.json())
        .then(res => {
            if (!res.success) {
                Swal.fire('Gagal', res.message || 'Data tidak ditemukan', 'error');
                return;
            }

            const buku = res.data;

            document.getElementById('judul').textContent = buku.judul;
            document.getElementById('isbn').textContent = buku.isbn ?? '-';
            document.getElementById('penulis').textContent = buku.penulis_buku?.nama_penulis ?? '-';
            document.getElementById('penerbit').textContent = buku.penerbit_buku?.nama_penerbit ?? '-';
            document.getElementById('genre').textContent = buku.genre;
            document.getElementById('tahun_terbit').textContent = buku.tahun_terbit;
            document.getElementById('stok').textContent = buku.stok;
            document.getElementById('sinopsis').textContent = buku.sinopsis;

            const coverPath = buku.cover ? `/storage/${buku.cover}` : '{{ asset('no-cover.png') }}';
            document.getElementById('cover-preview').src = coverPath;

        }).catch(err => {
            console.error(err);
            Swal.fire('Error', 'Gagal memuat data', 'error');
        });
});
</script>
@endsection
