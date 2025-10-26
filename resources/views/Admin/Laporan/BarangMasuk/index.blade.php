@extends('Master.Layouts.app', ['title' => $title])

@section('content')
    <div class="page-header">
        <h1 class="page-title">Laporan Produk Masuk</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-gray">Laporan</li>
                <li class="breadcrumb-item active" aria-current="page">Produk Masuk</li>
            </ol>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <input type="hidden" name="jenis_produk_index" id="jenis_produk_index" value="baru">

            <div class="card">
                <div class="card-header justify-content-between">
                    <h3 class="card-title">Data Laporan Produk Baru</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
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
                                <th class="border-bottom-0">Tanggal Masuk</th>
                                <th class="border-bottom-0">Kode Produk Masuk</th>
                                <th class="border-bottom-0">Kode Produk</th>
                                <th class="border-bottom-0">Produk</th>
                                <th class="border-bottom-0">Kategori</th>
                                <th class="border-bottom-0">Jumlah Masuk</th>
                                <th class="border-bottom-0">Keterangan</th>
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
                    "url": "{{ route('lap-bm.getlap-bm') }}",
                    "data": function(d) {
                        d.tglawal = $('input[name="tglawal"]').val();
                        d.tglakhir = $('input[name="tglakhir"]').val();
                        d.jenis = 'baru';
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
                ],

            });
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
            var jenis = 'baru'; // Always use 'baru'
            var kategori = $('#filter_kategori').val();

            var url = "{{ route('lap-bm.print') }}?jenis=" + jenis;

            if (tglawal != '' && tglakhir != '') {
                url += "&tglawal=" + tglawal + "&tglakhir=" + tglakhir;
            }

            if (kategori != '') {
                url += "&kategori=" + kategori;
            }

            if (tglawal != '' && tglakhir != '' || kategori != '') {
                window.open(url, '_blank');
            } else {
                swal({
                    title: "Yakin Print Semua Data Produk Baru?",
                    text: "Ini akan mencetak semua data laporan produk baru",
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
                        window.open(url, '_blank');
                        swal.close();
                    }
                });
            }
        }

        function pdf() {
            var tglawal = $('input[name="tglawal"]').val();
            var tglakhir = $('input[name="tglakhir"]').val();
            var jenis = 'baru'; // Always use 'baru'
            var kategori = $('#filter_kategori').val();

            console.log('=== PDF Laporan Produk Baru Function Called ===');
            console.log('Parameters:', {
                tglawal: tglawal,
                tglakhir: tglakhir,
                jenis: jenis,
                kategori: kategori
            });

            var url = "{{ route('lap-bm.pdf') }}?jenis=" + encodeURIComponent(jenis);

            if (tglawal != '' && tglakhir != '') {
                url += "&tglawal=" + encodeURIComponent(tglawal) + "&tglakhir=" + encodeURIComponent(tglakhir);
            }

            if (kategori != '') {
                url += "&kategori=" + encodeURIComponent(kategori);
            }

            console.log('Final PDF URL:', url);

            if ((tglawal != '' && tglakhir != '') || kategori != '') {
                console.log('Processing PDF with filters...');
                downloadPdf(url);
            } else {
                swal({
                    title: "Yakin export PDF Semua Data Produk Baru?",
                    text: "Ini akan mengexport semua data laporan produk baru ke dalam file PDF",
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
                        console.log('User confirmed, processing PDF...');
                        swal.close();
                        downloadPdf(url);
                    }
                });
            }
        }

        function downloadPdf(url) {
            console.log('Attempting to download PDF from:', url);

            swal({
                title: "Generating PDF Laporan Produk Baru...",
                text: "Mohon tunggu selagi kami membuat laporan PDF Anda",
                icon: "info",
                buttons: false,
                closeOnClickOutside: false,
                closeOnEsc: false
            });

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    console.log('Content-Type:', response.headers.get('Content-Type'));

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    const contentType = response.headers.get('Content-Type');

                    if (contentType && contentType.includes('application/json')) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Unknown server error');
                        });
                    } else if (contentType && contentType.includes('application/pdf')) {
                        console.log('PDF response received, opening in new tab...');
                        swal.close();
                        window.open(url, '_blank');

                        setTimeout(() => {
                            swal({
                                title: "Success!",
                                text: "Laporan PDF produk baru telah berhasil dibuat",
                                type: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }, 500);
                        return;
                    } else {
                        console.log('Unexpected content type:', contentType);
                        throw new Error('Unexpected response type: ' + contentType);
                    }
                })
                .catch(error => {
                    console.error('PDF generation error:', error);
                    swal({
                        title: "Error!",
                        text: "Gagal generate PDF laporan: " + error.message,
                        type: "error",
                        confirmButtonText: "OK"
                    });
                });
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
