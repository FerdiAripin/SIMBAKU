@extends('Master.Layouts.app', ['title' => $title])

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ $title }}</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-gray">Laporan</li>
                <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal Dari</label>
                                    <input type="date" class="form-control" id="tanggal_dari" name="tanggal_dari">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal Sampai</label>
                                    <input type="date" class="form-control" id="tanggal_sampai" name="tanggal_sampai">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jenis Buku</label>
                                    <select class="form-control" id="jenis_buku" name="jenis_buku">
                                        <option value="">Semua Jenis</option>
                                        <option value="5">Cek 5 Lembar</option>
                                        <option value="10">Cek 10 Lembar</option>
                                        <option value="25">Cek 25 Lembar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="button" class="btn btn-primary me-2" id="btn-filter">
                                        <i class="fe fe-search"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-success" id="btn-export">
                                        <i class="fe fe-download"></i> Export
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
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="">Total Cek Terpakai</h6>
                            <h3 class="mb-2 number-font" id="summary-total">0</h3>
                            <p class="text-muted mb-0">
                                <span class="text-primary">Lembar</span>
                            </p>
                        </div>
                        <div class="col col-auto">
                            <div class="counter-icon bg-primary-gradient box-shadow-primary brround ms-auto">
                                <i class="fe fe-file-text text-white mb-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="">Total Nominal</h6>
                            <h3 class="mb-2 number-font" id="summary-nominal">Rp 0</h3>
                            <p class="text-muted mb-0">
                                <span class="text-success">Rupiah</span>
                            </p>
                        </div>
                        <div class="col col-auto">
                            <div class="counter-icon bg-success-gradient box-shadow-success brround ms-auto">
                                <i class="fe fe-dollar-sign text-white mb-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="">Rata-rata per Hari</h6>
                            <h3 class="mb-2 number-font" id="summary-avg">0</h3>
                            <p class="text-muted mb-0">
                                <span class="text-warning">Lembar/hari</span>
                            </p>
                        </div>
                        <div class="col col-auto">
                            <div class="counter-icon bg-warning-gradient box-shadow-warning brround ms-auto">
                                <i class="fe fe-trending-up text-white mb-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="">Penerima Unik</h6>
                            <h3 class="mb-2 number-font" id="summary-penerima">0</h3>
                            <p class="text-muted mb-0">
                                <span class="text-info">Orang</span>
                            </p>
                        </div>
                        <div class="col col-auto">
                            <div class="counter-icon bg-info-gradient box-shadow-info brround ms-auto">
                                <i class="fe fe-users text-white mb-5"></i>
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
                    <h3 class="card-title">Data Penggunaan Cek</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-laporan"
                            class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                            <thead>
                                <th class="border-bottom-0" width="1%">No</th>
                                <th class="border-bottom-0">Nomor Seri</th>
                                <th class="border-bottom-0">Kode Buku</th>
                                <th class="border-bottom-0">Jenis</th>
                                <th class="border-bottom-0">Nominal</th>
                                <th class="border-bottom-0">Penerima</th>
                                <th class="border-bottom-0">Keperluan</th>
                                <th class="border-bottom-0">Tanggal Pakai</th>
                                <th class="border-bottom-0">User</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateSummary(data) {
            let totalCek = data.length;
            let totalNominal = data.reduce((sum, item) => sum + parseFloat(item.nominal || 0), 0);
            let uniquePenerima = [...new Set(data.map(item => item.penerima))].length;
            let tanggalUnik = [...new Set(data.map(item => item.tanggal_pakai))].length;
            let avgPerHari = tanggalUnik > 0 ? Math.round(totalCek / tanggalUnik) : 0;

            $('#summary-total').text(totalCek);
            $('#summary-nominal').text('Rp ' + totalNominal.toLocaleString('id-ID'));
            $('#summary-avg').text(avgPerHari);
            $('#summary-penerima').text(uniquePenerima);
        }
    </script>
@endsection

@section('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var tableLaporan;
        $(document).ready(function() {
            tableLaporan = $('#table-laporan').DataTable({
                "processing": true,
                "serverSide": true,
                "info": true,
                "order": [
                    [7, 'desc']
                ],
                "stateSave": false,
                "scrollX": true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],
                "pageLength": 25,
                lengthChange: true,
                "ajax": {
                    "url": "{{ route('cek.laporan.data') }}",
                    "data": function(d) {
                        d.tanggal_dari = $('#tanggal_dari').val();
                        d.tanggal_sampai = $('#tanggal_sampai').val();
                        d.jenis_buku = $('#jenis_buku').val();
                    }
                },
                "columns": [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false
                    },
                    {
                        data: 'nomor_seri',
                        name: 'nomor_seri',
                    },
                    {
                        data: 'buku_kode',
                        name: 'bukuCek.buku_kode',
                    },
                    {
                        data: 'jenis_buku',
                        name: 'bukuCek.jenis_buku',
                    },
                    {
                        data: 'nominal_formatted',
                        name: 'nominal',
                    },
                    {
                        data: 'penerima',
                        name: 'penerima',
                    },
                    {
                        data: 'keperluan',
                        name: 'keperluan',
                    },
                    {
                        data: 'tanggal_pakai',
                        name: 'tanggal_pakai',
                    },
                    {
                        data: 'user_name',
                        name: 'user.name',
                    }
                ],
                "drawCallback": function(settings) {
                    let api = this.api();
                    let data = api.rows({
                        page: 'current'
                    }).data().toArray();
                }
            });

            $('#btn-filter').click(function() {
                tableLaporan.ajax.reload();
            });

            $('#btn-export').click(function() {
                let params = new URLSearchParams();

                if ($('#tanggal_dari').val()) params.append('tanggal_dari', $('#tanggal_dari').val());
                if ($('#tanggal_sampai').val()) params.append('tanggal_sampai', $('#tanggal_sampai').val());
                if ($('#jenis_buku').val()) params.append('jenis_buku', $('#jenis_buku').val());

                let url = '{{ route('cek.laporan.export') }}';
                if (params.toString()) {
                    url += '?' + params.toString();
                }

                window.open(url, '_blank');
            });

            let today = new Date();
            let lastMonth = new Date();
            lastMonth.setDate(today.getDate() - 30);

            $('#tanggal_dari').val(lastMonth.toISOString().split('T')[0]);
            $('#tanggal_sampai').val(today.toISOString().split('T')[0]);
            tableLaporan.ajax.reload();
        });
    </script>
@endsection
