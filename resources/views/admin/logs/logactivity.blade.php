@extends('admin.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Log Activity</h4>

        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">Daftar Log Activity</h5>
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
                                <th>List</th>
                            </tr>
                        </thead>
                        <tbody id="logTableBody">
                            <tr>
                                <td colspan="6" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- CARD (mobile) -->
                <div id="logCards" class="d-md-none"></div>

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
            const token = localStorage.getItem('token');
            let currentPage = 1;

            function loadLogs(page = 1) {
                fetch(`/api/logs/activity?page=${page}`, {
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        const tbody = document.getElementById('logTableBody');
                        const cardContainer = document.getElementById('logCards');
                        const pagination = document.getElementById('pagination');

                        tbody.innerHTML = '';
                        cardContainer.innerHTML = '';
                        pagination.innerHTML = '';

                        const data = res.data;
                        if (!data || data.length === 0) {
                            tbody.innerHTML =
                                `<tr><td colspan="6" class="text-center">Tidak ada data log.</td></tr>`;
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
                            <td>${log.list}</td>
                        </tr>
                    `;

                            // Kartu mobile
                            cardContainer.innerHTML += `
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>#${res.from + index}</strong>
                                    <span class="badge bg-label-${getBadgeColor(log.method)}">${log.method}</span>
                                </div>
                                <div><strong>User Agent:</strong> <span title="${log.agent}">${log.agent.slice(0, 60)}...</span></div>
                                <div><strong>IP:</strong> ${log.ip}</div>
                                <div><strong>Tanggal:</strong> ${formatTanggal(log.tanggal)}</div>
                                <div><strong>List:</strong> <code>${log.list}</code></div>
                            </div>
                        </div>
                    `;
                        });

                        for (let i = 1; i <= res.last_page; i++) {
                            pagination.innerHTML += `
                        <button class="btn btn-sm ${i === res.current_page ? 'btn-primary' : 'btn-outline-primary'}"
                            onclick="loadLogs(${i})">${i}</button>
                    `;
                        }
                    })
                    .catch(error => {
                        console.error('Gagal memuat log:', error);
                        document.getElementById('logTableBody').innerHTML =
                            `<tr><td colspan="6" class="text-center text-danger">Gagal memuat data</td></tr>`;
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

            window.loadLogs = loadLogs;
            loadLogs(currentPage);
        });
    </script>
@endsection
