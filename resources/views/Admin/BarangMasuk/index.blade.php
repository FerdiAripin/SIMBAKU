@extends('Master.Layouts.app', ['title' => $title])

@section('content')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <h1 class="page-title"> Produk Masuk</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-gray">Pengelolaan</li>
                <li class="breadcrumb-item active" aria-current="page">Produk Masuk</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <!-- ROW -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <input type="hidden" name="jenis_produk_index" id="jenis_produk_index" value="baru">

            <div class="card">
                <div class="card-header justify-content-between">
                    <h3 class="card-title">Data Produk Baru</h3>
                    <div class="d-flex gap-2">
                        <!-- Filter Kategori -->
                        <select class="form-select" id="filter_kategori" style="width: 200px;"
                            onchange="filterByKategori()">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategori as $kat)
                                @if ($kat->kategori_nama !== 'CEK')
                                    <option value="{{ $kat->kategori_id }}">{{ $kat->kategori_nama }}</option>
                                @endif
                            @endforeach
                        </select>

                        @if ($hakTambah > 0)
                            <a class="modal-effect btn btn-primary-light" onclick="generateID()"
                                data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#modaldemo8">Tambah Data
                                <i class="fe fe-plus"></i></a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-1"
                            class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                            <thead>
                                <th class="border-bottom-0" width="1%">No</th>
                                <th class="border-bottom-0">Tanggal Masuk</th>
                                <th class="border-bottom-0">Kode Produk Masuk</th>
                                <th class="border-bottom-0">Kode Produk</th>
                                <th class="border-bottom-0">Produk</th>
                                <th class="border-bottom-0">Kategori</th>
                                <th class="border-bottom-0">Jumlah Masuk</th>
                                <th class="border-bottom-0">Keterangan</th>
                                <th class="border-bottom-0" width="1%">Action</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ROW -->

    @include('Admin.BarangMasuk.tambah')
    @include('Admin.BarangMasuk.edit')
    @include('Admin.BarangMasuk.hapus')
    @include('Admin.BarangMasuk.barang')

    <script>
        function generateID() {
            id = new Date().getTime();
            $("input[name='bmkode']").val("PM-" + id);
        }

        function update(data) {
            $("input[name='idbmU']").val(data.bm_id);
            $("input[name='bmkodeU']").val(data.bm_kode);
            $("input[name='kdbarangU']").val(data.barang_kode);
            $("textarea[name='keteranganU']").val(data.bm_keterangan.replace(/_/g, ' '));
            $("input[name='jmlU']").val(data.bm_jumlah);

            getbarangbyidU(data.barang_kode);

            $("input[name='tglmasukU").bootstrapdatepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            }).bootstrapdatepicker("update", data.bm_tanggal);
        }

        function hapus(data) {
            $("input[name='idbm']").val(data.bm_id);
            $("#vbm").html("Kode BM " + "<b>" + data.bm_kode + "</b>");
        }

        function validasi(judul, status) {
            swal({
                title: judul,
                type: status,
                confirmButtonText: "Iya."
            });
        }

        // Function removed since we only handle 'baru' now
        // The jenis is always set to 'baru' by default

        function filterByKategori() {
            table.ajax.reload();
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

        var table;
        $(document).ready(function() {
            //datatables
            table = $('#table-1').DataTable({

                "processing": true,
                "serverSide": true,
                "info": true,
                "order": [],
                "scrollX": true,
                "stateSave": true,
                "lengthMenu": [
                    [5, 10, 25, 50, 100],
                    [5, 10, 25, 50, 100]
                ],
                "pageLength": 10,

                lengthChange: true,

                "ajax": {
                    "url": "{{ route('barang-masuk.getbarang-masuk') }}",
                    "data": function(d) {
                        d.jenis = 'baru'; // Always send 'baru' since we only handle new products
                        d.kategori = $('#filter_kategori').val();
                    }
                },

                "columns": [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false
                    },
                    {
                        data: 'tgl',
                        name: 'bm_tanggal',
                    },
                    {
                        data: 'bm_kode',
                        name: 'bm_kode',
                    },
                    {
                        data: 'barang_kode',
                        name: 'barang_kode',
                    },
                    {
                        data: 'barang',
                        name: 'barang_nama',
                    },
                    {
                        data: 'kategori',
                        name: 'kategori_nama',
                    },
                    {
                        data: 'bm_jumlah',
                        name: 'bm_jumlah',
                    },
                    {
                        data: 'bm_keterangan',
                        name: 'bm_keterangan',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],

            });
        });
    </script>
@endsection
