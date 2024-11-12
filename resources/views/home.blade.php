@extends('layouts.main')

@section('content')
<!-- Pie Chart Library CSS & JS -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
    body {
        background: linear-gradient(135deg, #f0f4f8, #e2e2e2);
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

    .clicked {
        animation: clickEffect 0.3s forwards;
    }

    @keyframes clickEffect {
        0% { transform: scale(1); }
        50% { transform: scale(0.95); }
        100% { transform: scale(1); }
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
    }
</style>

<section class="section dashboard">
    <br>
    <div class="container-fluid">
        <div class="row justify-content-center">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }} {{ Auth::user()->nama_user }} 👋
                </div>
            @endif

            <!-- Card Container -->
            <div class="d-flex flex-wrap justify-content-center align-items-start">

                <!-- Risk & Opportunity Card -->
                <div class="col-xxl-4 col-md-6 mb-4">
                    <div class="card info-card sales-card">
                        <button class="card-body btn btn-light animate-card" style="border: none; padding: 0; text-align: left;" onclick="window.location.href='{{ route('riskregister.biglist') }}'">
                            <div class="d-flex align-items-center">
                                <div class="card-icon">
                                    <i class="bi bi-file-text-fill"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>Risk & Opportunity <br>Register</h6>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
                <!-- End Risk & Opportunity Card -->

                <!-- PPK Card -->
                <div class="col-xxl-4 col-md-6 mb-4">
                    <div class="card info-card sales-card">
                        <button class="card-body btn btn-light animate-card" style="border: none; padding: 0; text-align: left;" onclick="window.location.href='{{ route('ppk.index') }}'">
                            <div class="d-flex align-items-center">
                                <div class="card-icon">
                                    <i class="bi bi-bar-chart-fill"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>Proses Peningkatan <br>Kinerja (PPK)</h6>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
                <!-- End PPK Card -->
            </div><!-- End Card Container -->


            <section class="section dashboard">
    <div class="container">
        <div class="row">
            <!-- Status Pie Chart -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Status Departemen</h5>
                        <canvas id="statusPieChart"></canvas>
                        <!-- Button to open status modal -->
                    </div>
                </div>
            </div>

            <!-- Tingkatan Pie Chart -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Tingkatan Departemen</h5>
                        <canvas id="tingkatanPieChart"></canvas>
                        <!-- Button to open tingkatan modal -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Modal -->
    <!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Status Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Table with status details -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                    </thead>
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
                                                <th>Nama Resiko</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($details as $index => $resiko)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $resiko->nama_resiko }}</td>
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
                    <h5 class="modal-title" id="tingkatanModalLabel">Tingkatan Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Table with tingkatan details -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tingkatan</th>
                                <th>Details</th>
                            </tr>
                        </thead>
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
                                                    <th>Nama Resiko</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($details as $index => $resiko)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $resiko->nama_resiko }}</td>
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

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Pass the PHP variables to JavaScript as JSON
        const statusDetails = @json($statusDetails);
        const tingkatanDetails = @json($tingkatanDetails);

        // Status Pie Chart
        const statusPieChart = new Chart(document.getElementById('statusPieChart'), {
            type: 'pie',
            data: {
                labels: @json($statusCounts->keys()),
                datasets: [{
                    data: @json($statusCounts->values()),
                    backgroundColor: ['#FFD700', '#32CD32', '#FF6347']
                }]
            },
            options: {
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const segmentIndex = elements[0].index;
                        const selectedStatus = statusPieChart.data.labels[segmentIndex];

                        // Filter data by selected status
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
                    backgroundColor: ['#FF6347', '#FFD700', '#32CD32']
                }]
            },
            options: {
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const segmentIndex = elements[0].index;
                        const selectedTingkatan = tingkatanPieChart.data.labels[segmentIndex];

                        // Filter data by selected tingkatan
                        fetchFilteredData('tingkatan', selectedTingkatan);
                    }
                }
            }
        });

        // Function to fetch filtered data
        function fetchFilteredData(filterType, filterValue) {
            let filteredData = [];

            // Check which chart was clicked and filter the data accordingly
            if (filterType === 'status') {
                filteredData = statusDetails[filterValue] || [];
            } else if (filterType === 'tingkatan') {
                filteredData = tingkatanDetails[filterValue] || [];
            }

            displayDataInModal(filteredData, filterType, filterValue);
        }

        // Function to display data in modal
        function displayDataInModal(data, filterType, filterValue) {
            const modalId = filterType === 'status' ? '#statusModal' : '#tingkatanModal';
            const modalTitle = filterType === 'status' ? 'Status Data' : 'Tingkatan Data';

            const modalBody = document.querySelector(`${modalId} .modal-body`);
            modalBody.innerHTML = `
                <h5>${modalTitle} - ${filterValue}</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Resiko</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.map((resiko, index) => `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${resiko.nama_resiko}</td>
                                <td>${resiko.detail || '-'}</td>
                            </tr>`).join('')}
                    </tbody>
                </table>
            `;

            // Show the modal
            $(modalId).modal('show');
        }
    });
</script>

@endsection

