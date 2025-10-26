@extends('Master.Layouts.app', ['title' => $title])

@section('content')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <h1 class="page-title">Produk Keluar</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-gray">Pengelolaan</li>
                <li class="breadcrumb-item active" aria-current="page">Produk Keluar</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->


    <!-- ROW -->
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
                                <th class="border-bottom-0">Tanggal Keluar</th>
                                <th class="border-bottom-0">Kode Produk Keluar</th>
                                <th class="border-bottom-0">Kode Produk</th>
                                <th class="border-bottom-0">Produk</th>
                                <th class="border-bottom-0">Kategori</th>
                                <th class="border-bottom-0">Jumlah Keluar</th>
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

    @include('Admin.BarangKeluar.tambah')
    @include('Admin.BarangKeluar.edit')
    @include('Admin.BarangKeluar.hapus')
    @include('Admin.BarangKeluar.barang')

    <script>
        function generateID() {
            id = new Date().getTime();
            $("input[name='bkkode']").val("PK-" + id);
        }

        function update(data) {
            $("input[name='idbkU']").val(data.bk_id);
            $("input[name='bkkodeU']").val(data.bk_kode);
            $("input[name='kdbarangU']").val(data.barang_kode);
            $("input[name='jmlU']").val(data.bk_jumlah);
            $("textarea[name='keteranganU']").val(data.bk_keterangan.replace(/_/g, ' '));

            getbarangbyidU(data.barang_kode);

            $("input[name='tglkeluarU").bootstrapdatepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            }).bootstrapdatepicker("update", data.bk_tanggal);
        }

        function hapus(data) {
            $("input[name='idbk']").val(data.bk_id);
            $("#vbk").html("Kode BK " + "<b>" + data.bk_kode + "</b>");
        }

        function validasi(judul, status) {
            swal({
                title: judul,
                type: status,
                confirmButtonText: "Iya."
            });
        }

        function setJenisProdukIndex(jenis) {
            // Update button states
            if (jenis === 'baru') {
                $('#btn-baru-index').removeClass('btn-outline-primary').addClass('btn-primary');
                $('#btn-lama-index').removeClass('btn-primary').addClass('btn-outline-primary');
            } else {
                $('#btn-lama-index').removeClass('btn-outline-primary').addClass('btn-primary');
                $('#btn-baru-index').removeClass('btn-primary').addClass('btn-outline-primary');
            }

            // Set the hidden input value
            $('#jenis_produk_index').val(jenis);

            // Refresh the datatable with new jenis parameter
            jenisProduk = jenis;
            table.ajax.reload();
        }

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
                    "url": "{{ route('barang-keluar.getbarang-keluar') }}",
                    "data": function(d) {
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
                        data: 'tgl',
                        name: 'bk_tanggal',
                    },
                    {
                        data: 'bk_kode',
                        name: 'bk_kode',
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
                        data: 'bk_jumlah',
                        name: 'bk_jumlah',
                    },
                    {
                        data: 'keterangan',
                        name: 'bk_keterangan',
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
