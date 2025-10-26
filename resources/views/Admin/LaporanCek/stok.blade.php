@extends('Master.Layouts.app', ['title' => $title])

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ $title }}</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-gray">Laporan</li>
                <li class="breadcrumb-item active" aria-current="page">Laporan Stok</li>
            </ol>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Laporan</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai" value="{{ date('Y-m-01') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="tanggal_akhir" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success-light" id="btn-filter">
                                        <i class="fe fe-filter"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-secondary-light" id="btn-reset">
                                        <i class="fe fe-refresh-ccw"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <div>
                        <h3 class="card-title">Data Stok Per Jenis Cek</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div id="loading-indicator" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data...</p>
                    </div>

                    <div id="error-message" class="alert alert-danger" style="display: none;">
                        <strong>Error!</strong> <span id="error-text"></span>
                        <button class="btn btn-sm btn-outline-danger ms-2" id="retry-btn">Coba Lagi</button>
                    </div>
                    <div class="table-responsive" id="data-container">
                        <table class="table table-bordered text-nowrap border-bottom dataTable no-footer" id="table-stok">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" width="25%">Jenis Cek</th>
                                    <th class="border-bottom-0" width="20%">Jumlah Masuk</th>
                                    <th class="border-bottom-0" width="20%">Jumlah Keluar</th>
                                    <th class="border-bottom-0" width="20%">Total Stok</th>
                                    <th class="border-bottom-0" width="15%">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-per-jenis">
                                <tr>
                                    <td colspan="5" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-center">TOTAL AKHIR</th>
                                    <th class="text-center" id="grand-masuk">0</th>
                                    <th class="text-center" id="grand-keluar">0</th>
                                    <th class="text-center" id="grand-saldo">0</th>
                                    <th class="text-center">-</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            loadData();

            $('#btn-filter').click(function() {
                loadData();
            });

            $('#btn-reset').click(function() {
                resetFilter();
            });

            $('#btn-export-pdf').click(function() {
                exportPDF();
            });

            $('#btn-print').click(function() {
                window.print();
            });

            $('#btn-refresh').click(function() {
                loadData();
            });

            $('#retry-btn').click(function() {
                loadData();
            });
        });

        function resetFilter() {
            $('#tanggal_mulai').val('{{ date("Y-m-01") }}');
            $('#tanggal_akhir').val('{{ date("Y-m-d") }}');
            updatePeriodeInfo();
            loadData();
        }

        function formatDate(date) {
            return date.getFullYear() + '-' +
                   String(date.getMonth() + 1).padStart(2, '0') + '-' +
                   String(date.getDate()).padStart(2, '0');
        }

        function formatDateDisplay(dateString) {
            var date = new Date(dateString);
            var options = { day: '2-digit', month: 'short', year: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }

        function updatePeriodeInfo() {
            var startDate = $('#tanggal_mulai').val();
            var endDate = $('#tanggal_akhir').val();

            if (startDate && endDate) {
                var startFormatted = formatDateDisplay(startDate);
                var endFormatted = formatDateDisplay(endDate);

                if (startDate === endDate) {
                    $('#periode-text').text(startFormatted);
                } else {
                    $('#periode-text').text(startFormatted + ' - ' + endFormatted);
                }
            }
        }

        function showLoading() {
            $('#loading-indicator').show();
            $('#error-message').hide();
            $('#data-container').hide();
        }

        function hideLoading() {
            $('#loading-indicator').hide();
        }

        function showError(message) {
            $('#error-text').text(message);
            $('#error-message').show();
            $('#data-container').hide();
            hideLoading();
        }

        function showData() {
            $('#error-message').hide();
            $('#data-container').show();
            hideLoading();
        }

        function loadData() {
            console.log('Loading data...');
            showLoading();
            updatePeriodeInfo();

            var tanggalMulai = $('#tanggal_mulai').val();
            var tanggalAkhir = $('#tanggal_akhir').val();

            if (!tanggalMulai || !tanggalAkhir) {
                showError('Tanggal mulai dan akhir harus diisi');
                return;
            }

            if (tanggalMulai > tanggalAkhir) {
                showError('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
                return;
            }

            var url = "{{ url('/laporan-stok-cek/data') }}";
            console.log('Request URL:', url);

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                data: {
                    tanggal_mulai: tanggalMulai,
                    tanggal_akhir: tanggalAkhir
                },
                timeout: 10000,
                success: function(response) {
                    console.log('Response received:', response);

                    if (response.success === false) {
                        showError('Server error: ' + response.message);
                        if (response.data) {
                            updateTable(response.data);
                            showData();
                        }
                        return;
                    }

                    var data = response.data || response;

                    if (!Array.isArray(data)) {
                        console.error('Data is not array:', data);
                        showError('Format data tidak valid');
                        return;
                    }

                    updateTable(data);
                    showData();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });

                    var errorMsg = 'Error memuat data';

                    if (status === 'timeout') {
                        errorMsg = 'Request timeout - coba lagi';
                    } else if (xhr.status === 404) {
                        errorMsg = 'Route tidak ditemukan (404)';
                    } else if (xhr.status === 500) {
                        errorMsg = 'Server error (500)';
                    } else if (xhr.responseText) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            errorMsg = response.message || errorMsg;
                        } catch (e) {
                            errorMsg += ': ' + error;
                        }
                    }

                    showError(errorMsg);
                    var defaultData = [{
                            jenis_buku: '5',
                            total_masuk: 0,
                            total_keluar: 0,
                            saldo_akhir: 0
                        },
                        {
                            jenis_buku: '10',
                            total_masuk: 0,
                            total_keluar: 0,
                            saldo_akhir: 0
                        },
                        {
                            jenis_buku: '25',
                            total_masuk: 0,
                            total_keluar: 0,
                            saldo_akhir: 0
                        }
                    ];
                    updateTable(defaultData);
                }
            });
        }

        function updateTable(data) {
            console.log('Updating table with data:', data);

            let html = '';
            let grandMasuk = 0;
            let grandKeluar = 0;
            let grandSaldo = 0;

            if (!Array.isArray(data) || data.length === 0) {
                html = '<tr><td colspan="5" class="text-center text-muted">Tidak ada data tersedia untuk periode yang dipilih</td></tr>';
            } else {
                data.forEach(function(item) {
                    let masuk = parseInt(item.total_masuk) || 0;
                    let keluar = parseInt(item.total_keluar) || 0;
                    let saldo = parseInt(item.saldo_akhir) || 0;

                    grandMasuk += masuk;
                    grandKeluar += keluar;
                    grandSaldo += saldo;

                    let totalStok = saldo + keluar;
                    let persenTersisa = totalStok > 0 ? Math.round((saldo / totalStok) * 100) : 0;
                    let statusText = '';
                    let statusClass = '';

                    if (persenTersisa == 0 || saldo == 0) {
                        statusText = 'Stok Habis';
                        statusClass = 'text-danger';
                    } else if (persenTersisa < 20) {
                        statusText = 'Stok Menipis';
                        statusClass = 'text-warning';
                    } else if (persenTersisa < 50) {
                        statusText = 'Stok Sedang';
                        statusClass = 'text-info';
                    } else {
                        statusText = 'Stok Aman';
                        statusClass = 'text-success';
                    }

                    html += `
                        <tr>
                            <td><strong>Cek ${item.jenis_buku} Lembar</strong></td>
                            <td class="text-center">${masuk.toLocaleString()}</td>
                            <td class="text-center">${keluar.toLocaleString()}</td>
                            <td class="text-center">${saldo.toLocaleString()}</td>
                            <td class="text-center">
                                <span class="${statusClass}">${statusText}</span>
                                <br><small class="text-muted">${persenTersisa}% dari total stok</small>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#tabel-per-jenis').html(html);
            $('#grand-masuk').text(grandMasuk.toLocaleString());
            $('#grand-keluar').text(grandKeluar.toLocaleString());
            $('#grand-saldo').text(grandSaldo.toLocaleString());
        }

        function exportPDF() {
            var tanggalMulai = $('#tanggal_mulai').val();
            var tanggalAkhir = $('#tanggal_akhir').val();

            var url = "{{ route('laporan-cek.stok-pdf') }}" +
                      "?tanggal_mulai=" + encodeURIComponent(tanggalMulai) +
                      "&tanggal_akhir=" + encodeURIComponent(tanggalAkhir);

            $('#btn-export-pdf').prop('disabled', true).html('<i class="fe fe-loader"></i> Processing...');

            var link = document.createElement('a');
            link.href = url;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            setTimeout(function() {
                $('#btn-export-pdf').prop('disabled', false).html('<i class="fe fe-download"></i> Export PDF');
            }, 2000);
        }

        $(document).ready(function() {
            updatePeriodeInfo();
        });
    </script>

    <style>
        .table-responsive {
            overflow-x: auto;
            padding: 0;
        }

        #table-stok {
            width: 100% !important;
            table-layout: fixed !important;
        }

        #table-stok thead th {
            text-align: center;
            vertical-align: middle;
            font-weight: 600;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #table-stok tbody td {
            vertical-align: middle;
            padding: 8px;
            border: 1px solid #dee2e6;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #table-stok tfoot th {
            text-align: center;
            vertical-align: middle;
            font-weight: 600;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
        }

        #table-stok th:nth-child(1),
        #table-stok td:nth-child(1) {
            width: 25% !important;
            text-align: center;
        }

        #table-stok th:nth-child(2),
        #table-stok td:nth-child(2) {
            width: 20% !important;
            text-align: center;
        }

        #table-stok th:nth-child(3),
        #table-stok td:nth-child(3) {
            width: 20% !important;
            text-align: center;
        }

        #table-stok th:nth-child(4),
        #table-stok td:nth-child(4) {
            width: 20% !important;
            text-align: center;
        }

        #table-stok th:nth-child(5),
        #table-stok td:nth-child(5) {
            width: 15% !important;
            text-align: center;
        }

        #table-stok tbody tr:hover {
            background-color: #f5f5f5;
        }

        .spinner-border {
            width: 2rem;
            height: 2rem;
        }

        .card-subtitle {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .d-flex.gap-2 > * {
            margin-right: 0.5rem;
        }

        .d-flex.gap-2 > *:last-child {
            margin-right: 0;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .text-warning {
            color: #fd7e14 !important;
        }

        .text-info {
            color: #17a2b8 !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.85rem;
            }

            #table-stok th,
            #table-stok td {
                padding: 6px 4px;
            }

            #table-stok th:nth-child(5),
            #table-stok td:nth-child(5) {
                display: none;
            }

            #table-stok th:nth-child(1),
            #table-stok td:nth-child(1) {
                width: 30% !important;
            }

            #table-stok th:nth-child(2),
            #table-stok td:nth-child(2),
            #table-stok th:nth-child(3),
            #table-stok td:nth-child(3),
            #table-stok th:nth-child(4),
            #table-stok td:nth-child(4) {
                width: 23.33% !important;
            }

            .d-flex.gap-2 {
                flex-direction: column;
            }

            .d-flex.gap-2 > * {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
        }

        @media (max-width: 576px) {
            #table-stok th:nth-child(3),
            #table-stok td:nth-child(3) {
                display: none;
            }

            #table-stok th:nth-child(1),
            #table-stok td:nth-child(1) {
                width: 40% !important;
            }

            #table-stok th:nth-child(2),
            #table-stok td:nth-child(2),
            #table-stok th:nth-child(4),
            #table-stok td:nth-child(4) {
                width: 30% !important;
            }
        }

        @media print {
            .page-header,
            .breadcrumb,
            .btn,
            .card-options,
            #loading-indicator,
            #error-message,
            .card:first-child {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .table-responsive {
                overflow: visible !important;
            }

            #table-stok {
                font-size: 10px;
            }

            #table-stok th,
            #table-stok td {
                padding: 2px !important;
                border: 1px solid #000 !important;
            }

            .card-subtitle {
                font-size: 12px;
                margin-bottom: 10px !important;
            }
        }
    </style>
@endsection
