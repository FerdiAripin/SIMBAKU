<!-- MODAL DETAIL -->

<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detail Buku Cek</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Kode Buku</strong></td>
                                <td>: <span id="detail-kode"></span></td>
                            </tr>
                            <tr>
                                <td><strong>Jenis Buku</strong></td>
                                <td>: <span id="detail-jenis"></span></td>
                            </tr>
                            <tr>
                                <td><strong>Range Nomor Seri</strong></td>
                                <td>: <span id="detail-seri"></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Status</strong></td>
                                <td>: <span id="detail-status"></span></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Masuk</strong></td>
                                <td>: <span id="detail-tanggal"></span></td>
                            </tr>
                            <tr>
                                <td><strong>Keterangan</strong></td>
                                <td>: <span id="detail-keterangan"></span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <h5><strong>Daftar Lembar Cek</strong></h5>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="detail-lembar-table">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nomor Serial</th>
                                <th>Format Serial</th>
                                <th width="12%">Status</th>
                                <th>Penerima</th>
                                <th width="15%">Tanggal Pakai</th>
                                <th>Keperluan</th>
                            </tr>
                        </thead>
                        <tbody id="detail-lembar-body">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function filterLembar(status) {
        const rows = document.querySelectorAll('#detail-lembar-body tr');

        rows.forEach(row => {
            if (status === 'all') {
                row.style.display = '';
            } else {
                const statusCell = row.querySelector('td:nth-child(4)');
                const statusText = statusCell ? statusCell.textContent.toLowerCase().trim() : '';

                if (statusText === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

</script>
