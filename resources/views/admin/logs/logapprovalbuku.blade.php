@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Log Approval Buku</h4>

        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">Daftar Log Approval Pengajuan Buku</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 50px;">No</th>
                                <th style="width: 120px;">ID Pengajuan Buku</th>
                                <th>ID Verifikator</th> {{-- UBAH INI --}}
                                <th style="width: 120px;">Status</th>
                                <th>Catatan</th>
                                <th style="width: 160px;">Tanggal Approval</th>
                            </tr>
                        </thead>
                        <tbody id="logApprovalBukuTableBody">
                            <tr>
                                <td colspan="6" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="logApprovalBukuCards" class="d-md-none"></div>

                <div id="pagination" class="mt-3 d-flex justify-content-end flex-wrap gap-2"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const token = localStorage.getItem('token');
            let currentPage = 1;

            const logApprovalBukuTableBody = document.getElementById('logApprovalBukuTableBody');
            const logApprovalBukuCards = document.getElementById('logApprovalBukuCards');
            const paginationContainer = document.getElementById('pagination');

            const API_URL = '/api/logs/approvalbuku';

            function fetchLogs(page = 1) {
                logApprovalBukuTableBody.innerHTML = '<tr><td colspan="6" class="text-center">Memuat data...</td></tr>';
                logApprovalBukuCards.innerHTML = '';
                paginationContainer.innerHTML = '';

                fetch(`${API_URL}?page=${page}`, {
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                            }).catch(() => {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            });
                        }
                        return response.json();
                    })
                    .then(res => {
                        renderLogs(res.data, res.from);
                    })
                    .catch(error => {
                        console.error('Error fetching logs:', error);
                        logApprovalBukuTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Gagal memuat data log.</td></tr>';
                        logApprovalBukuCards.innerHTML = '<div class="alert alert-danger">Gagal memuat data log.</div>';
                    });
            }

            function renderLogs(logs, startNumber) {
                logApprovalBukuTableBody.innerHTML = '';
                logApprovalBukuCards.innerHTML = '';

                if (!logs || logs.length === 0) {
                    logApprovalBukuTableBody.innerHTML = '<tr><td colspan="6" class="text-center">Tidak ada data log approval buku.</td></tr>';
                    logApprovalBukuCards.innerHTML = '<div class="alert alert-info text-center">Tidak ada data log approval buku.</div>';
                    return;
                }

                logs.forEach((log, index) => {
                    const no = startNumber + index;
                    // UBAH INI: Langsung pakai log.id_verifikator
                    const verifikatorId = log.id_verifikator || '-';

                    const statusBadgeClass = log.status === 'disetujui' ? 'bg-label-success' : 'bg-label-danger';
                    const formattedDate = new Date(log.created_at).toLocaleString('id-ID', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });

                    const row = `
                        <tr class="text-center">
                            <td>${no}</td>
                            <td>${log.id_pengajuan_buku}</td>
                            <td>${verifikatorId}</td> {{-- UBAH INI --}}
                            <td>
                                <span class="badge ${statusBadgeClass}">
                                    ${log.status.charAt(0).toUpperCase() + log.status.slice(1)}
                                </span>
                            </td>
                            <td>${log.catatan || '-'}</td>
                            <td>${formattedDate}</td>
                        </tr>
                    `;
                    logApprovalBukuTableBody.insertAdjacentHTML('beforeend', row);

                    const card = `
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">No: ${no}</h6>
                                <p class="card-text"><strong>ID Pengajuan Buku:</strong> ${log.id_pengajuan_buku}</p>
                                <p class="card-text"><strong>ID Verifikator:</strong> ${verifikatorId}</p> {{-- UBAH INI --}}
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
                    logApprovalBukuCards.insertAdjacentHTML('beforeend', card);
                });
            }

            function renderPagination(res) {
                if (!res || res.last_page <= 1) {
                    paginationContainer.innerHTML = '';
                    return;
                }

                const ul = document.createElement('ul');
                ul.className = 'pagination justify-content-end mb-0';

                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${res.current_page === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" data-page="${res.current_page - 1}">Previous</a>`;
                ul.appendChild(prevLi);

                for (let i = 1; i <= res.last_page; i++) {
                    const li = document.createElement('li');
                    li.className = `page-item ${res.current_page === i ? 'active' : ''}`;
                    li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                    ul.appendChild(li);
                }

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

            fetchLogs(currentPage);
        });
    </script>
@endsection