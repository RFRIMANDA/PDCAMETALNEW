@extends('layouts.main')

@section('content')
<!-- Pie Chart Library CSS & JS -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
    body {
        background: linear-gradient(135deg, #f0f4f8, #e2e2e2);
        font-family: 'Arial', sans-serif;
        color: #333;
    }

    .animate-card {
        transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }

    .animate-card:hover {
        transform: scale(1.05);
        background-color: #ffffff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .animate-card:active {
        transform: scale(0.95);
    }

    .card-icon {
        background-color: #007bff;
        padding: 12px;
        border-radius: 50%;
        font-size: 32px;
        color: white;
    }

    h6 {
        margin: 0;
        font-weight: bold;
        color: #333;
        line-height: 1.5;
    }

    .alert {
        margin-bottom: 20px;
        font-weight: bold;
        color: #4CAF50;
        border: 1px solid #4CAF50;
        background-color: #d4edda;
        border-radius: 8px;
        padding: 10px;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    .container-fluid {
        margin-top: 20px;
    }

    .section-dashboard {
        padding: 30px 20px;
        background-color: #f8f9fa;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .card-body h5 {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 20px;
        color: #007bff;
    }

    .card {
        margin-bottom: 20px;
        border-radius: 12px;
        overflow: hidden;
    }

    .col-lg-4 {
        display: flex;
        justify-content: center;
    }

    .modal-content {
        border-radius: 12px;
        overflow: hidden;
    }

    .modal-header {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }

    .modal-body {
        padding: 20px;
        background-color: #f9f9f9;
    }

    table th {
        background-color: #007bff;
        color: white;
    }

    table tr:hover {
        background-color: #f1f1f1;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    .modal-footer {
        padding: 15px;
    }

    .card-body {
        padding: 20px;
        background-color: #ffffff;
    }
</style>

<section class="section-dashboard">
    <br>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <form method="GET" action="{{ route('dashboard.index') }}">
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manajemen')
                <div class="form-group">
                    <label for="departemen">Filter by Departemen:</label>
                    <select name="departemen" id="departemen" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Select Departemen --</option>
                        @foreach($departemenList as $departemen)
                            <option value="{{ $departemen }}" {{ $selectedDepartemen == $departemen ? 'selected' : '' }}>
                                {{ $departemen }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </form>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }} {{ Auth::user()->nama_user }} 👋
                </div>
            @endif

            <!-- Status Pie Chart -->
            <div class="col-lg-4">
                <div class="card animate-card">
                    <div class="card-body">
                        <h5>Status Risiko</h5>
                        <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tingkatan Pie Chart -->
            <div class="col-lg-4">
                <div class="card animate-card">
                    <div class="card-body">
                        <h5>Tingkatan Risiko / Peluang</h5>
                        <canvas id="tingkatanPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Table with status details -->
                    <table class="table table-striped">
                        <tbody>
                            @foreach($statusDetails as $status => $details)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $status }}</td>
                                    <td>
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Issue</th>
                                                    <th>Resiko</th>
                                                    <th>Peluang</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($details as $index => $resiko)
                                                    <tr data-id="{{ $resiko->id_divisi }}" onclick="navigateToRiskRegister(this)">
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $resiko->nama_issue }}</td>
                                                        <td>{{ $resiko->nama_resiko }}</td>
                                                        <td>{{ $resiko->peluang }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tingkatan Modal -->
    <div class="modal fade" id="tingkatanModal" tabindex="-1" aria-labelledby="tingkatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Table with tingkatan details -->
                    <table class="table table-striped">
                        <tbody>
                            @foreach($tingkatanDetails as $tingkatan => $details)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $tingkatan }}</td>
                                    <td>
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Issue</th>
                                                    <th>Resiko</th>
                                                    <th>Peluang</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($details as $index => $resiko)
                                                    <tr data-id="{{ $resiko->id_divisi }}" onclick="navigateToRiskRegister(this)">
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <a href="{{ route('riskregister.tablerisk', ['id' => $resiko->id_divisi]) }}">{{ $resiko->nama_issue }}</a>
                                                        </td>
                                                        <td>{{ $resiko->nama_resiko }}</td>
                                                        <td>{{ $resiko->peluang }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</section>
<script>

    document.addEventListener('DOMContentLoaded', function () {
        const statusDetails = @json($statusDetails);
        const tingkatanDetails = @json($tingkatanDetails);

        // Status Pie Chart
        const statusPieChart = new Chart(document.getElementById('statusPieChart'), {
            type: 'pie',
            data: {
                labels: @json($statusCounts->keys()),
                datasets: [{
                    data: @json($statusCounts->values()),
                    backgroundColor: ['#FFD700', '#32CD32','#FF6347']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            // Show percentage and count in tooltip
                            label: function(tooltipItem) {
                                let total = tooltipItem.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                let value = tooltipItem.raw;
                                let percentage = ((value / total) * 100).toFixed(2) + '%';
                                let count = value;
                                return tooltipItem.label + ': ' + count + ' (' + percentage + ')';
                            }
                        }
                    }
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const segmentIndex = elements[0].index;
                        const selectedStatus = statusPieChart.data.labels[segmentIndex];
                        fetchFilteredData('status', selectedStatus);
                    }
                }
            }
        });

        // Tingkatan Pie Chart
        const tingkatanPieChart = new Chart(document.getElementById('tingkatanPieChart'), {
            type: 'pie',
            data: {
                labels: @json($tingkatanCounts->keys()),
                datasets: [{
                    data: @json($tingkatanCounts->values()),
                    backgroundColor: ['#FF6347','#FFD700','#32CD32']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            // Show percentage and count in tooltip
                            label: function(tooltipItem) {
                                let total = tooltipItem.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                let value = tooltipItem.raw;
                                let percentage = ((value / total) * 100).toFixed(2) + '%';
                                let count = value;
                                return tooltipItem.label + ': ' + count + ' (' + percentage + ')';
                            }
                        }
                    }
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const segmentIndex = elements[0].index;
                        const selectedTingkatan = tingkatanPieChart.data.labels[segmentIndex];
                        fetchFilteredData('tingkatan', selectedTingkatan);
                    }
                }
            }
        });

        // Fetch data based on filters
        function fetchFilteredData(filterType, filterValue) {
            let filteredData = [];
            if (filterType === 'status') {
                filteredData = statusDetails[filterValue] || [];
            } else if (filterType === 'tingkatan') {
                filteredData = tingkatanDetails[filterValue] || [];
            }
            displayDataInModal(filteredData, filterType, filterValue);
        }

        // Navigate to RiskRegister
        function navigateToRiskRegister(row) {
            const idDivisi = row.getAttribute('data-id');
            if (idDivisi) {
                window.location.href = `/riskregister/${id}`;
            }
        }

        function displayDataInModal(data, filterType, filterValue) {
            const modalId = filterType === 'status' ? '#statusModal' : '#tingkatanModal';
            const modalBody = document.querySelector(`${modalId} .modal-body`);
            modalBody.innerHTML = `
                <h5>${filterType === 'status' ? 'Status Data' : 'Tingkatan Data'} - ${filterValue}</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Issue</th>
                            <th>Risiko</th>
                            <th>Peluang</th>
                            <th>Departement</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.map((resiko, index) => `
                            <tr data-id="${resiko.idDivisi}">
                                <td>${index + 1}</td>
                                <td>
                                    <a href="/riskregister/${resiko.id_divisi}">
                                        ${resiko.nama_issue}
                                    </a>
                                </td>
                                <td>${resiko.nama_resiko}</td>
                                <td>${resiko.peluang}</td>
                                <td>${resiko.nama_divisi}</td>
                            </tr>`).join('')}
                    </tbody>
                </table>
            `;
            $(modalId).modal('show'); // Show modal
        }
    });
</script>


@endsection

