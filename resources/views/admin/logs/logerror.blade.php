@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Log Error</h4>

        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">Daftar Log Error</h5>
            </div>

            <div class="card-body">
                <!-- TABEL (desktop) -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 50px;">No</th>
                                <th style="width: 80px;">Method</th>
                                <th>User Agent</th>
                                <th style="width: 120px;">IP</th>
                                <th style="width: 160px;">Tanggal</th>
                                <th>Path</th>
                                <th>List</th>
                                <th>Error</th>
                            </tr>
                        </thead>
                        <tbody id="logErrorBody">
                            <tr>
                                <td colspan="8" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- CARD (mobile) -->
                <div id="logErrorCards" class="d-md-none"></div>

                <!-- Pagination -->
                <div id="pagination" class="mt-3 d-flex justify-content-end flex-wrap gap-2"></div>
            </div>
        </div>
    </div>

    <style>
        code {
            display: block;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 13px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentPage = 1;

            function loadErrors(page = 1) {
                fetch(`/api/logs/error?page=${page}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        const tbody = document.getElementById('logErrorBody');
                        const cardContainer = document.getElementById('logErrorCards');
                        const pagination = document.getElementById('pagination');

                        tbody.innerHTML = '';
                        cardContainer.innerHTML = '';
                        pagination.innerHTML = '';

                        const data = res.data;
                        if (!data || data.length === 0) {
                            tbody.innerHTML =
                                `<tr><td colspan="8" class="text-center">Tidak ada data log error.</td></tr>`;
                            return;
                        }

                        data.forEach((log, index) => {
                            // Tabel
                            tbody.innerHTML += `
                        <tr>
                            <td class="text-center">${res.from + index}</td>
                            <td class="text-center"><span class="badge bg-label-${getBadgeColor(log.method)}">${log.method}</span></td>
                            <td><span title="${log.agent}">${log.agent.slice(0, 40)}...</span></td>
                            <td class="text-center">${log.ip}</td>
                            <td class="text-center">${formatTanggal(log.tanggal)}</td>
                            <td>${log.path}</td>
                            <td>${log.list}</td>
                            <td><code>${log.error}</code></td>
                        </tr>
                    `;

                            // Card
                            cardContainer.innerHTML += `
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>#${res.from + index}</strong>
                                    <span class="badge bg-label-${getBadgeColor(log.method)}">${log.method}</span>
                                </div>
                                <div><strong>User Agent:</strong> ${log.agent.slice(0, 60)}...</div>
                                <div><strong>IP:</strong> ${log.ip}</div>
                                <div><strong>Tanggal:</strong> ${formatTanggal(log.tanggal)}</div>
                                <div><strong>Path:</strong> ${log.path}</div>
                                <div><strong>List:</strong> ${log.list}</div>
                                <div><strong>Error:</strong><code>${log.error}</code></div>
                            </div>
                        </div>
                    `;
                        });

                        for (let i = 1; i <= res.last_page; i++) {
                            pagination.innerHTML += `
                        <button class="btn btn-sm ${i === res.current_page ? 'btn-primary' : 'btn-outline-primary'}"
                            onclick="loadErrors(${i})">${i}</button>
                    `;
                        }
                    })
                    .catch(error => {
                        console.error('Gagal memuat log error:', error);
                        document.getElementById('logErrorBody').innerHTML =
                            `<tr><td colspan="8" class="text-center text-danger">Gagal memuat data</td></tr>`;
                    });
            }

            function formatTanggal(tanggal) {
                const d = new Date(tanggal);
                return d.toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function getBadgeColor(method) {
                switch (method) {
                    case 'POST':
                        return 'success';
                    case 'PUT':
                        return 'warning';
                    case 'DELETE':
                        return 'danger';
                    default:
                        return 'secondary';
                }
            }

            window.loadErrors = loadErrors;
            loadErrors(currentPage);
        });
    </script>
@endsection
