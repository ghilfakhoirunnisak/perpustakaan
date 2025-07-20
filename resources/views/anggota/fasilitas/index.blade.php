@extends('anggota.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-4">Data Fasilitas</h4>

        <div id="fasilitas-container">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
    </div>

    <style>
        .fasilitas-card {
            border: 1px solid #eee;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        .fasilitas-card .card-header {
            background-color: #6f42c1;
            color: white;
            padding: 12px 16px;
            font-weight: bold;
            font-size: 1rem;
            text-align: center;
        }

        .fasilitas-card .card-body {
            padding: 16px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
        }

        .fasilitas-card .status {
            margin: 10px 0;
            font-size: 0.9rem;
        }

        .btn-ajukan {
            font-size: 0.85rem;
            padding: 6px 12px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/fasilitas', {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    const container = document.getElementById('fasilitas-container');
                    container.innerHTML = '';

                    if (!res.data || res.data.length === 0) {
                        container.innerHTML = `
                    <div class="text-center my-5">
                        <img src="{{ asset('no_data.svg') }}" style="max-width: 250px;" class="mb-3">
                        <h5 class="mb-2">Belum ada fasilitas</h5>
                        <p class="text-muted">Fasilitas akan ditampilkan di sini jika tersedia.</p>
                    </div>
                `;
                        return;
                    }

                    let cards = '<div class="row g-3">';
                    res.data.forEach(item => {
                        cards += `
                    <div class="col-md-6 col-lg-4">
                        <div class="fasilitas-card h-100">
                            <div class="card-header">${item.nama_fasilitas}</div>
                            <div class="card-body">
                                <p class="small mb-3">${item.deskripsi}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="status mb-0">
                                        <span class="badge bg-${item.status === 'aktif' ? 'success' : 'secondary'}">
                                            ${item.status.toUpperCase()}
                                        </span>
                                    </div>
                                    ${item.status === 'aktif' ? `<a href="/anggota/reservasifasilitas/create" class="btn btn-outline-primary btn-sm"> <i class="bx bx-calendar-plus"></i> Ajukan Reservasi </a>` : `<span class="text-muted small fst-italic">Tidak bisa <span>`}
                                </div>

                            </div>
                        </div>
                    </div>
                `;
                    });
                    cards += '</div>';
                    container.innerHTML = cards;
                })
                .catch(() => {
                    document.getElementById('fasilitas-container').innerHTML = `
                <div class="text-center text-danger mt-5">
                    <i class="bx bx-error-circle fs-1"></i>
                    <p>Gagal memuat data fasilitas.</p>
                </div>
            `;
                });
        });
    </script>
@endsection
