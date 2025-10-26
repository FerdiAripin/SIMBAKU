@extends('Master.Layouts.app', ['title' => $title])

@section('content')
    <div class="page-header">
        <h1 class="page-title">Kelola Buku Cek</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-gray">Pengelolaan</li>
                <li class="breadcrumb-item active" aria-current="page">Kelola Buku Cek</li>
            </ol>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h3 class="card-title">Data Buku Cek</h3>
                    @if ($hakTambah > 0)
                        <div>
                            <a class="btn btn-success me-2" href="#" onclick="showModal('gunakan')">
                                <i class="fe fe-check"></i> Gunakan Cek
                            </a>
                            <a class="modal-effect btn btn-primary-light" data-bs-effect="effect-super-scaled"
                                data-bs-toggle="modal" href="#modalTambah">
                                Tambah Buku Cek <i class="fe fe-plus"></i>
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Filter Jenis Buku</label>
                            <select id="filter-jenis-buku" class="form-select">
                                <option value="">Semua Jenis</option>
                                <option value="5">Cek 5 Lembar</option>
                                <option value="10">Cek 10 Lembar</option>
                                <option value="25">Cek 25 Lembar</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Filter Status</label>
                            <select id="filter-status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="habis">Habis</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" id="reset-filter" class="btn btn-secondary">
                                <i class="fe fe-refresh-cw me-1"></i> Reset
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="table-1"
                            class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                            <thead>
                                <th class="border-bottom-0" width="1%" style="text-align: center">No</th>
                                <th class="border-bottom-0" style="text-align: center">Kode Buku</th>
                                <th class="border-bottom-0" style="text-align: center">Jenis</th>
                                <th class="border-bottom-0" style="text-align: center">Range Nomor Serial</th>
                                <th class="border-bottom-0" style="text-align: center">Status</th>
                                <th class="border-bottom-0" style="text-align: center">Tanggal Masuk</th>
                                <th class="border-bottom-0" width="1%" style="text-align: center">Action</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('Admin.Cek.tambah')
    @include('Admin.Cek.detail')
    @include('Admin.Cek.gunakan')
    @include('Admin.Cek.hapus')

    <script>
        function showModal(type) {
            if (type === 'gunakan') {
                $('#modalGunakan').modal('show');
            }
        }

        function detail(id) {
            $.get('{{ route('cek.show', '') }}/' + id, function(data) {
                $('#detail-kode').text(data.buku_kode);
                $('#detail-jenis').text('Cek ' + data.jenis_buku + ' Lembar');
                $('#detail-seri').text(data.nomor_seri_awal + ' - ' + data.nomor_seri_akhir);
                $('#detail-status').html('<span class="badge bg-' + (data.status == 'aktif' ? 'success' :
                    'warning') + '">' + data.status + '</span>');
                $('#detail-tanggal').text(data.tanggal_terbit);
                $('#detail-keterangan').text(data.keterangan || '-');
                if (data.kode_huruf && data.kode_angka) {
                    $('#detail-format').text(data.kode_huruf + data.kode_angka + 'XXXXXX');
                } else {
                    $('#detail-format').text('Format lama');
                }

                let tableBody = '';
                data.lembar_cek.forEach(function(lembar, index) {
                    let statusClass = lembar.status == 'tersedia' ? 'success' :
                        lembar.status == 'terpakai' ? 'primary' :
                        lembar.status == 'rusak' ? 'warning' : 'danger';

                    let formatSerial = '-';
                    if (lembar.nomor_seri && lembar.nomor_seri.length >= 5) {
                        let huruf = lembar.nomor_seri.substring(0, 3);
                        let angka = lembar.nomor_seri.substring(3, 5);
                        let urut = lembar.nomor_seri.substring(5);
                        formatSerial = '<small class="text-muted">' + huruf + '-' + angka + '-' + urut +
                            '</small>';
                    }

                    tableBody += '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td><strong>' + lembar.nomor_seri + '</strong></td>' +
                        '<td>' + formatSerial + '</td>' +
                        '<td><span class="badge bg-' + statusClass + '">' + lembar.status + '</span></td>' +
                        '<td>' + (lembar.penerima || '-') + '</td>' +
                        '<td>' + (lembar.tanggal_pakai || '-') + '</td>' +
                        '<td>' + (lembar.keperluan || '-') + '</td>' +
                        '</tr>';
                });
                $('#detail-lembar-body').html(tableBody);

                $('#modalDetail').modal('show');
            });
        }

        function hapus(id) {
            $('#hapus-id').val(id);
            $('#modalHapus').modal('show');
        }

        function loadStatistik() {
            $.get('{{ route('cek.stok.api') }}', function(data) {
                data.forEach(function(item) {
                    $('#stok-' + item.jenis_buku).text(item.jumlah_lembar_tersedia);
                    $('#buku-' + item.jenis_buku).text(item.jumlah_buku_tersedia);
                });

                let totalTerpakai = data.reduce((sum, item) => sum + item.jumlah_lembar_terpakai, 0);
                $('#total-terpakai').text(totalTerpakai);
            });
        }

        function validasi(judul, status) {
            swal({
                title: judul,
                type: status,
                confirmButtonText: "Iya"
            });
        }

        function validateNomorSeri(nomorSeri) {
            if (!nomorSeri || nomorSeri.length < 6) {
                return false;
            }

            return $.get('{{ route('cek.validate', '') }}/' + encodeURIComponent(nomorSeri))
                .done(function(response) {
                    if (response.valid) {
                        showValidationMessage('Nomor seri valid dan tersedia', 'success');
                        return true;
                    } else {
                        showValidationMessage(response.message, 'error');
                        return false;
                    }
                })
                .fail(function() {
                    showValidationMessage('Gagal memvalidasi nomor seri', 'error');
                    return false;
                });
        }

        function showValidationMessage(message, type) {
            let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            let alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                '<i class="fe fe-' + (type === 'success' ? 'check-circle' : 'alert-circle') + ' me-2"></i>' +
                message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>';

            $('#validation-messages').html(alertHtml);

            setTimeout(function() {
                $('.alert').fadeOut();
            }, 3000);
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
            loadStatistik();

            table = $('#table-1').DataTable({
                "processing": true,
                "serverSide": true,
                "info": true,
                "order": [],
                "stateSave": true,
                "scrollX": true,
                "lengthMenu": [
                    [5, 10, 25, 50, 100],
                    [5, 10, 25, 50, 100]
                ],
                "pageLength": 10,
                lengthChange: true,
                "ajax": {
                    "url": "{{ route('cek.getcek') }}",
                    "data": function(d) {
                        d.jenis_buku = $('#filter-jenis-buku').val();
                        d.status = $('#filter-status').val();
                        d.kode_huruf = $('#filter-kode-huruf').val();
                    }
                },
                "columns": [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false
                    },
                    {
                        data: 'buku_kode',
                        name: 'buku_kode',
                    },
                    {
                        data: 'jenis_display',
                        name: 'jenis_buku',
                    },
                    {
                        data: 'nomor_seri_range',
                        name: 'nomor_seri_awal',
                        orderable: false
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                    },
                    {
                        data: 'tanggal_terbit',
                        name: 'tanggal_terbit',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            $('#filter-jenis-buku, #filter-status').on('change', function() {
                table.ajax.reload();
            });

            $('#filter-kode-huruf').on('keyup', function() {
                this.value = this.value.toUpperCase();
                clearTimeout(window.filterTimeout);
                window.filterTimeout = setTimeout(function() {
                    table.ajax.reload();
                }, 500);
            });

            $('#reset-filter').on('click', function() {
                $('#filter-jenis-buku').val('');
                $('#filter-status').val('');
                $('#filter-kode-huruf').val('');
                table.ajax.reload();
            });

            $('#form-tambah').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                let kodeHuruf = formData.get('kode_huruf');
                let kodeAngka = formData.get('kode_angka');
                let nomorAwal = formData.get('nomor_awal');

                if (!/^[A-Z]{3}$/.test(kodeHuruf)) {
                    validasi('Kode huruf harus 3 huruf kapital (A-Z)', 'error');
                    return;
                }

                if (!/^[0-9]{2}$/.test(kodeAngka)) {
                    validasi('Kode angka harus 2 digit angka (0-9)', 'error');
                    return;
                }

                if (!nomorAwal || nomorAwal < 1) {
                    validasi('Nomor awal harus diisi dan minimal 1', 'error');
                    return;
                }

                $.ajax({
                    url: '{{ route('cek.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#modalTambah').modal('hide');
                        if (response.data) {
                            validasi('Buku cek berhasil ditambahkan. Range: ' + response.data
                                .range_nomor, 'success');
                        } else {
                            validasi('Buku cek berhasil ditambahkan', 'success');
                        }
                        table.ajax.reload();
                        loadStatistik();
                        $('#form-tambah')[0].reset();
                        updatePreview();
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        if (errors) {
                            for (let key in errors) {
                                errorMessage += errors[key][0] + '\n';
                            }
                        } else {
                            errorMessage = xhr.responseJSON.error ||
                                'Gagal menambahkan buku cek';
                        }
                        validasi(errorMessage, 'error');
                    }
                });
            });

            $('#form-gunakan').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: '{{ route('admin.cek.gunakan-buku') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#modalGunakan').modal('hide');
                        let message = 'Cek berhasil digunakan';
                        if (response.data) {
                            message += ' - ' + response.data.nomor_seri + ' untuk ' + response
                                .data.penerima;
                        }
                        validasi(message, 'success');
                        table.ajax.reload();
                        loadStatistik();
                        $('#form-gunakan')[0].reset();
                        $('#gunakan-validation').empty();
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON.error || 'Gagal menggunakan cek';
                        validasi(message, 'error');
                    }
                });
            });

            $('#form-hapus').submit(function(e) {
                e.preventDefault();
                let id = $('#hapus-id').val();

                $.ajax({
                    url: '{{ route('cek.destroy', '') }}/' + id,
                    type: 'DELETE',
                    success: function(response) {
                        $('#modalHapus').modal('hide');
                        validasi('Buku cek berhasil dihapus', 'success');
                        table.ajax.reload();
                        loadStatistik();
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON.error || 'Gagal menghapus buku cek';
                        validasi(message, 'error');
                    }
                });
            });

            $('#input-nomor-seri').on('input', function() {
                let nomorSeri = $(this).val().toUpperCase();
                $(this).val(nomorSeri); // Auto uppercase

                if (nomorSeri.length >= 6) {
                    validateNomorSeriGunakan(nomorSeri);
                } else {
                    $('#gunakan-validation').empty();
                }
            });
        });

        function validateNomorSeriGunakan(nomorSeri) {
            $.get('{{ route('cek.validate', '') }}/' + encodeURIComponent(nomorSeri))
                .done(function(response) {
                    let alertClass = response.valid ? 'alert-success' : 'alert-danger';
                    let icon = response.valid ? 'check-circle' : 'x-circle';
                    let message = response.message;

                    if (!response.valid && response.detail) {
                        if (response.detail.penerima) {
                            message += '<br><small>Penerima: ' + response.detail.penerima + '</small>';
                        }
                        if (response.detail.tanggal_pakai) {
                            message += '<br><small>Tanggal: ' + response.detail.tanggal_pakai + '</small>';
                        }
                        if (response.detail.nominal) {
                            message += '<br><small>Nominal: ' + response.detail.nominal + '</small>';
                        }
                    }

                    $('#gunakan-validation').html(
                        '<div class="alert ' + alertClass + ' py-2">' +
                        '<i class="fe fe-' + icon + ' me-2"></i>' +
                        message +
                        '</div>'
                    );
                })
                .fail(function() {
                    $('#gunakan-validation').html(
                        '<div class="alert alert-warning py-2">' +
                        '<i class="fe fe-alert-triangle me-2"></i>' +
                        'Gagal memvalidasi nomor seri' +
                        '</div>'
                    );
                });
        }
    </script>
    <style>
        #table-1 thead th,
        #table-1 tbody td {
            text-align: center !important;
            vertical-align: middle !important;
        }

        #table-1 tbody td:nth-child(4) {
            text-align: center !important;
        }

        #table-1 .badge {
            display: inline-block;
        }

        @media (max-width: 768px) {
            #table-1 thead th,
            #table-1 tbody td {
                text-align: center !important;
                vertical-align: middle !important;
            }
        }
    </style>
@endsection
