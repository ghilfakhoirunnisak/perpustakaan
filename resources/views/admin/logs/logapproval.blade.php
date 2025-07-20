@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Log Approval Reservasi</h4>

        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">Daftar Log Approval Reservasi Fasilitas</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 50px;">No</th>
                                <th style="width: 100px;">ID Reservasi</th>
                                <th>Verifikator</th>
                                <th style="width: 120px;">Status</th>
                                <th>Catatan</th>
                                <th style="width: 160px;">Tanggal Approval</th>
                            </tr>
                        </thead>
                        <tbody id="logApprovalTableBody">
                            <tr>
                                <td colspan="6" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="logApprovalCards" class="d-md-none"></div>

                <div id="pagination" class="mt-3 d-flex justify-content-end flex-wrap gap-2"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const token = localStorage.getItem('token'); // Pastikan token ada di localStorage
            let currentPage = 1;

            const logApprovalTableBody = document.getElementById('logApprovalTableBody');
            const logApprovalCards = document.getElementById('logApprovalCards');
            const paginationContainer = document.getElementById('pagination');

            const API_URL = '/api/logs/approval'; // Endpoint API Anda

            function fetchLogs(page = 1) {
                logApprovalTableBody.innerHTML = '<tr><td colspan="6" class="text-center">Memuat data...</td></tr>';
                logApprovalCards.innerHTML = '';
                paginationContainer.innerHTML = '';

                fetch(`${API_URL}?page=${page}`, {
                        headers: {
                            'Authorization': 'Bearer ' + token, // Penting untuk otentikasi
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        // Periksa apakah respons HTTP berhasil (status 2xx)
                        if (!response.ok) {
                            // Jika tidak berhasil, coba baca respons error dari server
                            return response.json().then(errorData => {
                                console.error('API Error Response:', errorData);
                                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                            }).catch(() => {
                                // Jika respons tidak JSON atau tidak bisa diparse
                                throw new Error(`HTTP error! status: ${response.status}`);
                            });
                        }
                        return response.json();
                    })
                    .then(res => { // Menggunakan 'res' karena ini adalah seluruh objek respons dari API
                        renderLogs(res.data, res.from); // Kirim data log dan nomor awal untuk 'No'
                        renderPagination(res); // Kirim seluruh objek respons untuk pagination
                    })
                    .catch(error => {
                        console.error('Error fetching logs:', error);
                        logApprovalTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Gagal memuat data log.</td></tr>';
                        logApprovalCards.innerHTML = '<div class="alert alert-danger">Gagal memuat data log.</div>';
                    });
            }

            function renderLogs(logs, startNumber) {
                logApprovalTableBody.innerHTML = '';
                logApprovalCards.innerHTML = '';

                if (!logs || logs.length === 0) {
                    logApprovalTableBody.innerHTML = '<tr><td colspan="6" class="text-center">Tidak ada data log approval.</td></tr>';
                    logApprovalCards.innerHTML = '<div class="alert alert-info text-center">Tidak ada data log approval.</div>';
                    return;
                }

                logs.forEach((log, index) => {
                    const no = startNumber + index;
                    const verifikatorName = log.verifikator && log.verifikator.user
                                            ? (log.verifikator.user.nama_lengkap || log.verifikator.user.name || '-')
                                            : '-';
                    const statusBadgeClass = log.status === 'disetujui' ? 'bg-label-success' : 'bg-label-danger';
                    const formattedDate = new Date(log.created_at).toLocaleString('id-ID', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });

                    const row = `
                        <tr class="text-center">
                            <td>${no}</td>
                            <td>${log.id_reservasi}</td>
                            <td>${verifikatorName}</td>
                            <td>
                                <span class="badge ${statusBadgeClass}">
                                    ${log.status.charAt(0).toUpperCase() + log.status.slice(1)}
                                </span>
                            </td>
                            <td>${log.catatan || '-'}</td>
                            <td>${formattedDate}</td>
                        </tr>
                    `;
                    logApprovalTableBody.insertAdjacentHTML('beforeend', row);

                    const card = `
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">No: ${no}</h6>
                                <p class="card-text"><strong>ID Reservasi:</strong> ${log.id_reservasi}</p>
                                <p class="card-text"><strong>Verifikator:</strong> ${verifikatorName}</p>
                                <p class="card-text"><strong>Status:</strong>
                                    <span class="badge ${statusBadgeClass}">
                                        ${log.status.charAt(0).toUpperCase() + log.status.slice(1)}
                                    </span>
                                </p>
                                <p class="card-text"><strong>Catatan:</strong> ${log.catatan || '-'}</p>
                                <p class="card-text"><small class="text-muted">Tanggal Approval: ${formattedDate}</small></p>
                            </div>
                        </div>
                    `;
                    logApprovalCards.insertAdjacentHTML('beforeend', card);
                });
            }

            // Menerima seluruh objek respons (res)
            function renderPagination(res) {
                if (!res || res.last_page <= 1) { // Pastikan res dan res.last_page ada
                    paginationContainer.innerHTML = '';
                    return;
                }

                const ul = document.createElement('ul');
                ul.className = 'pagination justify-content-end mb-0';

                // Previous button
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${res.current_page === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" data-page="${res.current_page - 1}">Previous</a>`;
                ul.appendChild(prevLi);

                // Page numbers
                for (let i = 1; i <= res.last_page; i++) {
                    const li = document.createElement('li');
                    li.className = `page-item ${res.current_page === i ? 'active' : ''}`;
                    li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                    ul.appendChild(li);
                }

                // Next button
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${res.current_page === res.last_page ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#" data-page="${res.current_page + 1}">Next</a>`;
                ul.appendChild(nextLi);

                paginationContainer.appendChild(ul);

                ul.querySelectorAll('.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = parseInt(this.dataset.page);
                        if (!isNaN(page) && page >= 1 && page <= res.last_page) {
                            fetchLogs(page);
                        }
                    });
                });
            }

            // Initial fetch
            fetchLogs(currentPage);
        });
    </script>
@endsection