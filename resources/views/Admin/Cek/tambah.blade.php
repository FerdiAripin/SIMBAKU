<!-- MODAL TAMBAH (Admin/Cek/tambah.blade.php) -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Buku Cek</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-tambah" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Buku Cek <span class="text-danger">*</span></label>
                                <select class="form-control" name="jenis_buku" required>
                                    <option value="">Pilih Jenis Buku</option>
                                    <option value="5">Cek 5 Lembar</option>
                                    <option value="10">Cek 10 Lembar</option>
                                    <option value="25">Cek 25 Lembar</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jumlah Buku <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="jumlah_buku" min="1"
                                    max="100" required>
                                <small class="text-muted">Maksimal 100 buku per sekali input</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kode Huruf (3 Huruf) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase" name="kode_huruf"
                                    maxlength="3" pattern="[A-Z]{3}" required style="text-transform: uppercase;"
                                    placeholder="DAA">
                                <small class="text-muted">Contoh: DAA, BCA, MAN</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kode Angka (2 Angka) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="kode_angka" maxlength="2"
                                    pattern="[0-9]{2}" required placeholder="10">
                                <small class="text-muted">Contoh: 10, 25, 01</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nomor Awal <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="nomor_awal" min="1" required
                                    placeholder="123456">
                                <small class="text-muted">Contoh: 123456</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Preview Nomor Seri <span class="text-danger">*</span></label>
                                <div class="form-control bg-light" id="preview-nomor-seri"
                                    style="min-height: 38px; display: flex; align-items: center;">
                                    <span class="text-muted">Contoh akan muncul di sini</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Masuk <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_terbit"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <strong>Informasi Format Nomor Seri:</strong>
                        <ul class="mb-0">
                            <li>Format: <strong>[3 HURUF][2 ANGKA][NOMOR URUT]</strong></li>
                            <li>Contoh: DAA10123456, BCA25000001, MAN01999999</li>
                            <li>Sistem akan otomatis generate nomor seri berurutan untuk setiap lembar</li>
                            <li>Contoh: Jika input 2 buku cek 5 lembar dengan DAA10123456, akan dibuat:</li>
                            <li class="ms-3">Buku 1: DAA10123456 - DAA10123460</li>
                            <li class="ms-3">Buku 2: DAA10123461 - DAA10123465</li>
                        </ul>
                    </div>
                    <div id="range-check-result"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-warning me-2" id="btn-check-range">Cek Ketersediaan</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit" disabled>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
