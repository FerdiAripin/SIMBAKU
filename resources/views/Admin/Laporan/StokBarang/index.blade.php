@extends('Master.Layouts.app', ['title' => $title])

@section('content')
    <div class="page-header">
        <h1 class="page-title">Laporan Stok Produk</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-gray">Laporan</li>
                <li class="breadcrumb-item active" aria-current="page">Stok Produk</li>
            </ol>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="form-group mb-3">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" onclick="setJenisProdukIndex('baru')"
                        id="btn-baru-index">Produk
                        Baru</button>
                    <button type="button" class="btn btn-outline-primary" onclick="setJenisProdukIndex('lama')"
                        id="btn-lama-index">Produk Lama</button>
                </div>
                <input type="hidden" name="jenis_produk_index" id="jenis_produk_index" value="baru">
            </div>

            <div class="card">
                <div class="card-header justify-content-between">
                    <h3 class="card-title">Data</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="" class="fw-bold">Filter</label>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="text" name="tglawal" class="form-control datepicker-date"
                                    placeholder="Tanggal Awal">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="text" name="tglakhir" class="form-control datepicker-date"
                                    placeholder="Tanggal Akhir">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" id="filter_kategori">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($kategori->where('kategori_nama', '!=', 'CEK') as $kat)
                                        <option value="{{ $kat->kategori_id }}">{{ $kat->kategori_nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-success-light" onclick="filter()"><i class="fe fe-filter"></i>
                                        Filter</button>
                                    <button class="btn btn-secondary-light" onclick="reset()"><i
                                            class="fe fe-refresh-ccw"></i>
                                        Reset</button>
                                    <button class="btn btn-danger-light" onclick="pdf()"><i class="fa fa-file-pdf-o"></i>
                                        PDF</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="table-1"
                            class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                            <thead>
                                <th class="border-bottom-0" width="1%">No</th>
                                <th class="border-bottom-0">Kode Produk</th>
                                <th class="border-bottom-0">Produk</th>
                                <th class="border-bottom-0">Kategori</th>
                                <th class="border-bottom-0">Stok Awal</th>
                                <th class="border-bottom-0">Jumlah Masuk</th>
                                <th class="border-bottom-0">Jumlah Keluar</th>
                                <th class="border-bottom-0">Total Stok</th>
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

        $(document).ready(function() {
            getData();
        });

        function getData() {
            table = $('#table-1').DataTable({

                "processing": true,
                "serverSide": true,
                "info": true,
                "order": [],
                "scrollX": true,
                "stateSave": true,
                "lengthMenu": [
                    [5, 10, 25, 50, 100, -1],
                    [5, 10, 25, 50, 100, 'Semua']
                ],
                "pageLength": 10,

                lengthChange: true,

                "ajax": {
                    "url": "{{ route('lap-sb.getlap-sb') }}",
                    "data": function(d) {
                        d.tglawal = $('input[name="tglawal"]').val();
                        d.tglakhir = $('input[name="tglakhir"]').val();
                        d.jenis = $('#jenis_produk_index').val();
                        d.kategori = $('#filter_kategori').val();
                    }
                },

                "columns": [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false
                    },
                    {
                        data: 'barang_kode',
                        name: 'barang_kode',
                    },
                    {
                        data: 'barang_nama',
                        name: 'barang_nama',
                    },
                    {
                        data: 'kategori',
                        name: 'kategori_nama',
                    },
                    {
                        data: 'stokawal',
                        name: 'barang_stok',
                    },
                    {
                        data: 'jmlmasuk',
                        name: 'barang_kode',
                        orderable: false,
                    },
                    {
                        data: 'jmlkeluar',
                        name: 'barang_kode',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'totalstok',
                        name: 'barang_kode',
                        searchable: false,
                        orderable: false,
                    },
                ],

            });
        }

        function setJenisProdukIndex(jenis) {
            if (jenis === 'baru') {
                $('#btn-baru-index').removeClass('btn-outline-primary').addClass('btn-primary');
                $('#btn-lama-index').removeClass('btn-primary').addClass('btn-outline-primary');
            } else {
                $('#btn-lama-index').removeClass('btn-outline-primary').addClass('btn-primary');
                $('#btn-baru-index').removeClass('btn-primary').addClass('btn-outline-primary');
            }

            $('#jenis_produk_index').val(jenis);

            jenisProduk = jenis;
            table.ajax.reload();
        }

        function filter() {
            var tglawal = $('input[name="tglawal"]').val();
            var tglakhir = $('input[name="tglakhir"]').val();

            if ((tglawal != '' && tglakhir != '') || $('#filter_kategori').val() != '') {
                table.ajax.reload(null, false);
            } else {
                validasi("Isi minimal satu filter (Tanggal atau Kategori)!", 'warning');
            }
        }

        function reset() {
            $('input[name="tglawal"]').val('');
            $('input[name="tglakhir"]').val('');
            $('#filter_kategori').val('');
            table.ajax.reload(null, false);
        }

        function print() {
            var tglawal = $('input[name="tglawal"]').val();
            var tglakhir = $('input[name="tglakhir"]').val();
            var jenis = $('#jenis_produk_index').val();
            var kategori = $('#filter_kategori').val();

            var url = "{{ route('lap-sb.print') }}?jenis=" + jenis;

            if (tglawal != '' && tglakhir != '') {
                url += "&tglawal=" + tglawal + "&tglakhir=" + tglakhir;
            }

            if (kategori != '') {
                url += "&kategori=" + kategori;
            }

            window.open(url, '_blank');
        }

        function pdf() {
            var tglawal = $('input[name="tglawal"]').val();
            var tglakhir = $('input[name="tglakhir"]').val();
            var jenis = $('#jenis_produk_index').val();
            var kategori = $('#filter_kategori').val();

            var url = "{{ route('lap-sb.pdf') }}?jenis=" + jenis;

            if (tglawal != '' && tglakhir != '') {
                url += "&tglawal=" + tglawal + "&tglakhir=" + tglakhir;
            }

            if (kategori != '') {
                url += "&kategori=" + kategori;
            }

            window.open(url, '_blank');
        }

        function validasi(judul, status) {
            swal({
                title: judul,
                type: status,
                confirmButtonText: "Iya."
            });
        }
    </script>
@endsection
