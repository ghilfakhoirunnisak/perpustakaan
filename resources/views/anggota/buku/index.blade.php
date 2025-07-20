@extends('anggota.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Daftar Buku</h4>

        <div class="row" id="buku-container">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch("/api/buku", {
                headers: {
                    Accept: "application/json"
                }
            })
            .then(res => res.json())
            .then(res => {
                const container = document.getElementById('buku-container');
                container.innerHTML = '';

                if (!res.data || res.data.length === 0) {
                    container.innerHTML = `
                        <div class="text-center my-5">
                            <img src="{{ asset('images/empty.svg') }}" alt="Tidak ada data" style="max-width: 180px;" class="mb-3">
                            <h5 class="mb-2 text-dark fw-bold">Belum ada buku tersedia</h5>
                        </div>`;
                    return;
                }

                res.data.forEach(item => {
                    const card = document.createElement('div');
                    card.className = 'col-md-3 col-sm-6 mb-4';

                    card.innerHTML = `
                        <div class="card h-100 shadow-sm text-center">
                            <img src="${item.cover ? '/storage/' + item.cover : '/images/default_cover.jpg'}"
                                 alt="Cover Buku"
                                 class="card-img-top object-fit-cover mx-auto mt-3"
                                 style="width: 120px; height: 170px; object-fit: cover;">

                            <div class="card-body d-flex flex-column justify-content-between">
                                <small class="text-muted mb-1">${item.penulis_buku?.nama_penulis ?? '-'}</small>
                                <h6 class="card-title text-truncate mb-2">${item.judul}</h6>

                                <div class="d-flex justify-content-center gap-2 mt-2">
                                    <a href="/anggota/buku/${item.id_buku}/show" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show"></i> Detail
                                    </a>
                                    <a href="/anggota/pengajuanbuku/create" class="btn btn-sm btn-outline-success">
                                        <i class="bx bx-book-add"></i> Pinjam
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(card);
                });
            })
            .catch(err => {
                console.error("Gagal memuat data buku:", err);
                document.getElementById('buku-container').innerHTML = `
                    <div class="text-center text-danger mt-5">
                        <i class="bx bx-error-circle fs-1"></i>
                        <p class="mt-2 fw-bold">Gagal memuat data buku.</p>
                    </div>`;
            });
        });
    </script>
@endsection
