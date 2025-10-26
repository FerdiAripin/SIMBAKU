@extends('Master.Layouts.app', ['title' => $title])

@section('content')
    <div class="page-header">
        <h1 class="page-title">Riwayat Pengelolaan Cek</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-gray">Laporan</li>
                <li class="breadcrumb-item active" aria-current="page">Riwayat</li>
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
                    <form id="form-filter">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tanggal Dari</label>
                                    <input type="date" class="form-control" id="tanggal_dari" name="tanggal_dari">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tanggal Sampai</label>
                                    <input type="date" class="form-control" id="tanggal_sampai" name="tanggal_sampai">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Jenis Cek</label>
                                    <select class="form-control" id="jenis_buku" name="jenis_buku">
                                        <option value="">Semua Jenis</option>
                                        <option value="5">Cek 5 Lembar</option>
                                        <option value="10">Cek 10 Lembar</option>
                                        <option value="25">Cek 25 Lembar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tipe Laporan</label>
                                    <select class="form-control" id="tipe_laporan" name="tipe_laporan">
                                        <option value="penggunaan">Cek Keluar</option>
                                        <option value="masuk">Cek Masuk</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="button" class="btn btn-success-light" id="btn-filter">
                                        <i class="fe fe-filter"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-secondary-light" id="btn-refresh">
                                        <i class="fe fe-refresh-ccw"></i> Reset
                                    </button>
                                    <button type="button" class="btn btn-danger-light" id="btn-export-pdf">
                                        <i class="fa fa-file-pdf-o"></i> PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h3 class="card-title" id="table-title">Data Laporan Cek</h3>
                    <div class="card-options">
                        <span class="badge bg-primary" id="total-records">Total: 0</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-laporan"
                            class="table table-bordered text-nowrap border-bottom dataTable no-footer">
                            <thead>
                                <tr id="table-header">
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var tableLaporan;
        var currentTipe = 'penggunaan';

        $(document).ready(function() {
            setDefaultDates();
            getData();
            $('#btn-filter').click(function() {
                filter();
            });

            $('#tipe_laporan').change(function() {
                currentTipe = $(this).val();
                updateTableTitle();
                tableLaporan.destroy();
                getData();
            });

            $('#btn-export-pdf').click(function() {
                exportPDF();
            });

            $('#btn-refresh').click(function() {
                $('#tanggal_dari').val('');
                $('#tanggal_sampai').val('');
                $('#jenis_buku').val('');
                $('#tipe_laporan').val('penggunaan');

                currentTipe = 'penggunaan';

                updateTableTitle();

                if (tableLaporan) {
                    tableLaporan.destroy();
                }
                getData();
            });
        });

        function getData() {
            updateTableHeader();

            tableLaporan = $('#table-laporan').DataTable({
                "processing": true,
                "serverSide": true,
                "info": true,
                "order": [
                    [1, 'desc']
                ],
                "scrollX": false,
                "responsive": true,
                "stateSave": false,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'Semua']
                ],
                "pageLength": 25,
                lengthChange: true,

                "ajax": {
                    "url": getAjaxUrl(),
                    "data": function(d) {
                        d.tanggal_dari = $('#tanggal_dari').val();
                        d.tanggal_sampai = $('#tanggal_sampai').val();
                        d.jenis_buku = $('#jenis_buku').val();
                        d.tipe = currentTipe;
                    },
                    "error": function(xhr, error, code) {
                        console.log('Ajax Error Details:');
                        console.log('Status:', xhr.status);
                        console.log('Response Text:', xhr.responseText);
                        console.log('Error:', error);
                        console.log('Code:', code);

                        let colCount = getColumnCount();
                        $('#table-laporan tbody').html(
                            `<tr><td colspan="${colCount}" class="text-center text-danger">` +
                            'Error memuat data: ' + xhr.status + ' - ' + error +
                            '</td></tr>'
                        );
                    }
                },

                "columns": getTableColumns(),

                "drawCallback": function(settings) {
                    if (settings.json && settings.json.recordsFiltered !== undefined) {
                        updateTotalRecords(settings.json.recordsFiltered);
                    } else {
                        updateTotalRecords(0);
                    }
                }
            });

            loadSummary();
        }

        function getAjaxUrl() {
            if (currentTipe === 'penggunaan') {
                return "/admin/laporan-cek/penggunaan";
            } else if (currentTipe === 'masuk') {
                return "/admin/laporan-cek/masuk";
            } else {
                return "/admin/laporan-cek/semua";
            }
        }

        function getColumnCount() {
            return currentTipe === 'penggunaan' ? 6 : 6;
        }

        function updateTableHeader() {
            let headerHtml = '';

            if (currentTipe === 'penggunaan') {
                headerHtml = `
            <th class="border-bottom-0" width="5%">No</th>
            <th class="border-bottom-0" width="15%">Kode Buku</th>
            <th class="border-bottom-0" width="12%">Jenis</th>
            <th class="border-bottom-0" width="25%">Range Nomor Serial</th>
            <th class="border-bottom-0" width="25%">Keperluan</th>
            <th class="border-bottom-0" width="18%">Tanggal Keluar</th>
        `;
            } else {
                headerHtml = `
            <th class="border-bottom-0" width="5%">No</th>
            <th class="border-bottom-0" width="15%">Kode Buku</th>
            <th class="border-bottom-0" width="12%">Jenis</th>
            <th class="border-bottom-0" width="25%">Range Nomor Serial</th>
            <th class="border-bottom-0" width="25%">Keterangan</th>
            <th class="border-bottom-0" width="18%">Tanggal Masuk</th>
        `;
            }

            $('#table-header').html(headerHtml);
        }

        function getTableColumns() {
            if (currentTipe === 'penggunaan') {
                return [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false
                    },
                    {
                        data: 'buku_kode',
                        name: 'buku_kode',
                        defaultContent: '-'
                    },
                    {
                        data: function(row) {
                            return row.jenis_buku || '-';
                        },
                        name: 'jenis_buku',
                    },
                    {
                        data: function(row) {
                            return row.nomor_seri || '-';
                        },
                        name: 'nomor_seri',
                    },
                    {
                        data: function(row) {
                            return row.keperluan_list || '-';
                        },
                        name: 'keperluan',
                    },
                    {
                        data: function(row) {
                            return row.tanggal_range || '-';
                        },
                        name: 'created_at',
                    }
                ];
            } else {
                return [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false
                    },
                    {
                        data: 'buku_kode',
                        name: 'buku_kode',
                        defaultContent: '-'
                    },
                    {
                        data: function(row) {
                            return row.jenis_display || '-';
                        },
                        name: 'jenis_buku',
                    },
                    {
                        data: function(row) {
                            return row.nomor_seri_range || '-';
                        },
                        name: 'nomor_seri',
                    },
                    {
                        data: function(row) {
                            return row.keterangan || '-';
                        },
                        name: 'keterangan',
                    },
                    {
                        data: function(row) {
                            return row.tanggal_terbit || '-';
                        },
                        name: 'tanggal_terbit',
                    }
                ];
            }
        }

        function updateTableTitle() {
            if (currentTipe === 'penggunaan') {
                $('#table-title').text('Data Penggunaan Cek (Keluar)');
            } else {
                $('#table-title').text('Data Penambahan Cek (Masuk)');
            }
        }

        function filter() {
            var tanggal_dari = $('#tanggal_dari').val();
            var tanggal_sampai = $('#tanggal_sampai').val();

            if (tanggal_dari != '' && tanggal_sampai != '') {
                tableLaporan.ajax.reload(null, false);
                loadSummary();
            } else {
                validasi("Isi dulu Form Filter Tanggal!", 'warning');
            }
        }

        function loadSummary() {
            $.ajax({
                url: "/admin/laporan-cek/summary",
                type: 'GET',
                data: {
                    tanggal_dari: $('#tanggal_dari').val(),
                    tanggal_sampai: $('#tanggal_sampai').val(),
                    jenis_buku: $('#jenis_buku').val()
                },
                success: function(data) {
                    if ($('#summary-keluar').length) {
                        $('#summary-keluar').text(data.total_terpakai.toLocaleString());
                    }
                    if ($('#summary-masuk').length) {
                        $('#summary-masuk').text(data.total_lembar_masuk.toLocaleString());
                    }
                    if ($('#summary-saldo').length) {
                        $('#summary-saldo').text((data.total_lembar_masuk - data.total_terpakai)
                            .toLocaleString());
                    }
                    if ($('#summary-nominal').length) {
                        $('#summary-nominal').text('Rp ' + data.total_nominal.toLocaleString('id-ID'));
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log('Summary Error:', xhr.status, textStatus, errorThrown);
                }
            });
        }

        function updateTotalRecords(total) {
            $('#total-records').text('Total: ' + total.toLocaleString());
        }

        function exportPDF() {
            var tanggal_dari = $('#tanggal_dari').val();
            var tanggal_sampai = $('#tanggal_sampai').val();
            var jenis_buku = $('#jenis_buku').val();

            let params = new URLSearchParams();
            if (tanggal_dari) params.append('tanggal_dari', tanggal_dari);
            if (tanggal_sampai) params.append('tanggal_sampai', tanggal_sampai);
            if (jenis_buku) params.append('jenis_buku', jenis_buku);
            params.append('tipe', currentTipe);

            if (tanggal_dari != '' && tanggal_sampai != '') {
                let url = '/admin/laporan-cek/export-pdf';
                if (params.toString()) {
                    url += '?' + params.toString();
                }
                window.open(url, '_blank');
            } else {
                swal({
                    title: "Yakin export PDF Semua Data?",
                    type: "warning",
                    buttons: true,
                    dangerMode: true,
                    confirmButtonText: "Yakin",
                    cancelButtonText: 'Batal',
                    showCancelButton: true,
                    showConfirmButton: true,
                    closeOnConfirm: false,
                    confirmButtonColor: '#09ad95',
                }, function(value) {
                    if (value == true) {
                        let url = '/admin/laporan-cek/export-pdf?' + params.toString();
                        window.open(url, '_blank');
                        swal.close();
                    }
                });
            }
        }

        function setDefaultDates() {
            let today = new Date();
            let lastMonth = new Date();
            lastMonth.setDate(today.getDate() - 30);

            $('#tanggal_dari').val(lastMonth.toISOString().split('T')[0]);
            $('#tanggal_sampai').val(today.toISOString().split('T')[0]);
        }

        function validasi(judul, status) {
            swal({
                title: judul,
                type: status,
                confirmButtonText: "Iya."
            });
        }
    </script>

    <style>
        .table-responsive {
            overflow-x: auto;
            padding: 0;
        }

        #table-laporan {
            width: 100% !important;
            table-layout: fixed !important;
        }

        #table-laporan thead th {
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

        #table-laporan tbody td {
            vertical-align: middle;
            padding: 8px;
            border: 1px solid #dee2e6;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #table-laporan th:nth-child(1),
        #table-laporan td:nth-child(1) {
            width: 5% !important;
            text-align: center;
        }

        #table-laporan th:nth-child(2),
        #table-laporan td:nth-child(2) {
            width: 15% !important;
            text-align: center;
        }

        #table-laporan th:nth-child(3),
        #table-laporan td:nth-child(3) {
            width: 12% !important;
            text-align: center;
        }

        #table-laporan th:nth-child(4),
        #table-laporan td:nth-child(4) {
            width: 25% !important;
            text-align: center;
        }

        #table-laporan th:nth-child(5),
        #table-laporan td:nth-child(5) {
            width: 25% !important;
            text-align: center;
            white-space: normal !important;
            word-wrap: break-word;
        }

        #table-laporan th:nth-child(6),
        #table-laporan td:nth-child(6) {
            width: 18% !important;
            text-align: center;
            white-space: nowrap;
        }

        #table-laporan tbody tr:hover {
            background-color: #f5f5f5;
        }

        .dataTables_wrapper {
            width: 100%;
        }

        .dataTables_processing {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 200px;
            height: 30px;
            margin-left: -100px;
            margin-top: -15px;
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
            color: #999;
            font-size: 11px;
            background-color: white;
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.85rem;
            }

            #table-laporan th,
            #table-laporan td {
                padding: 6px 4px;
            }

            #table-laporan th:nth-child(5),
            #table-laporan td:nth-child(5) {
                display: none;
            }

            #table-laporan th:nth-child(4),
            #table-laporan td:nth-child(4) {
                width: 35% !important;
            }

            #table-laporan th:nth-child(6),
            #table-laporan td:nth-child(6) {
                width: 25% !important;
            }
        }

        @media (max-width: 576px) {
            #table-laporan th:nth-child(4),
            #table-laporan td:nth-child(4) {
                display: none;
            }

            #table-laporan th:nth-child(2),
            #table-laporan td:nth-child(2) {
                width: 25% !important;
            }

            #table-laporan th:nth-child(3),
            #table-laporan td:nth-child(3) {
                width: 30% !important;
            }

            #table-laporan th:nth-child(6),
            #table-laporan td:nth-child(6) {
                width: 35% !important;
            }
        }

        @media print {

            .page-header,
            .breadcrumb,
            .btn,
            .card-options {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .table-responsive {
                overflow: visible !important;
            }

            #table-laporan {
                font-size: 10px;
            }

            #table-laporan th,
            #table-laporan td {
                padding: 2px !important;
                border: 1px solid #000 !important;
            }
        }
    </style>
@endsection
