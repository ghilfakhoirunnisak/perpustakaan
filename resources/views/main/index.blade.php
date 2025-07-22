@extends('main.app')

@section('content')
    <section id="hero-section" class="d-flex align-items-center text-center text-md-start py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 order-md-1 order-2">
                    <h1 class="display-3 fw-bold mb-3 text-dark">Jelajahi Dunia Pengetahuan di Perpustakaan</h1>
                    <p class="lead mb-4 text-dark">Temukan ribuan buku, jurnal, dan sumber daya digital yang siap membantu Anda dalam belajar, meneliti, dan berinovasi.</p>
                    <a href="/login" class="btn btn-primary btn-lg px-4 py-2 fw-bold">Masuk Sekarang</a>
                </div>
                <div class="col-md-6 order-md-2 order-1 d-flex justify-content-center align-items-center">
                    <img src="{{asset('img/landing.png')}}" class="img-fluid rounded mb-4 mb-md-0" alt="Perpustakaan Modern">
                </div>
            </div>
        </div>
    </section>

    <style>
        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #343a40;
            margin: 0;
            overflow-x: hidden;
        }

        #hero-section {
            min-height: calc(100vh - 76px); /* Tinggi penuh layar dikurangi navbar */
            margin-top: -76px; /* Kompensasi tinggi navbar fixed */
            position: relative;
            z-index: 1;
        }

        #hero-section h1,
        #hero-section p {
            /* Tidak perlu text-shadow jika latar belakang polos */
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .img-fluid {
            max-height: 80vh; /* Batasi tinggi gambar agar tidak terlalu besar */
            object-fit: contain; /* Pastikan gambar terlihat penuh tanpa terpotong */
        }

        @media (max-width: 991.98px) {
            #hero-section {
                margin-top: -56px;
                min-height: calc(100vh - 56px);
            }
        }

        @media (max-width: 767.98px) {
            #hero-section .col-md-6 {
                text-align: center; /* Pusatkan teks di mobile */
            }
            .btn-lg {
                font-size: 1rem;
                padding: .5rem 1rem !important;
            }
            /* Ubah urutan di mobile: gambar di atas teks */
            .order-1 { order: 1; }
            .order-2 { order: 2; }
        }
    </style>
@endsection
