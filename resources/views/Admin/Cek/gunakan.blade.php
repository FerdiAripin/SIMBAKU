<!-- MODAL GUNAKAN CEK -->
<div class="modal fade" id="modalGunakan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Gunakan Buku Cek</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="form-gunakan" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="form-per-buku">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jenis Buku Cek <span class="text-danger">*</span></label>
                                    <select class="form-control" name="jenis_buku" id="jenis-buku" required>
                                        <option value="">Pilih Jenis Buku</option>
                                        <option value="5">Cek 5 Lembar</option>
                                        <option value="10">Cek 10 Lembar</option>
                                        <option value="25">Cek 25 Lembar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nomor Seri Awal <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control text-uppercase" name="nomor_seri_awal"
                                        id="input-nomor-seri-awal" style="text-transform: uppercase;"
                                        placeholder="DAA10123456" required>
                                    <small class="text-muted">Nomor cek pertama</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nomor Seri Akhir <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control text-uppercase" name="nomor_seri_akhir"
                                        id="input-nomor-seri-akhir" style="text-transform: uppercase;"
                                        placeholder="DAA10123460" readonly>
                                    <small class="text-muted">Nomor cek terakhir (otomatis)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Penerima <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="penerima" maxlength="100" required
                                    placeholder="Nama penerima cek">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Keperluan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="keperluan" maxlength="255" required
                                    placeholder="Keperluan penggunaan cek">
                            </div>
                        </div>
                    </div>

                    <div id="buku-validation"></div>
                    <div class="alert alert-info">
                        <strong>Info:</strong> Nomor seri akhir akan dihitung otomatis berdasarkan jenis buku yang
                        dipilih.
                    </div>
                    <div class="alert alert-warning">
                        <strong>Perhatian:</strong>
                        <ul class="mb-0" id="peringatan-list">
                            <li>Pastikan nomor seri awal tersedia dan berurutan</li>
                            <li>Format nomor seri: 3 huruf + 2 angka + nomor urut (contoh: DAA10123456)</li>
                            <li>Semua nomor cek dalam range akan digunakan sekaligus</li>
                            <li>Setelah digunakan, seluruh buku cek tidak dapat dibatalkan</li>
                            <li>Pastikan data penerima sudah benar</li>
                        </ul>
                    </div>
                    <div id="validation-messages"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fe fe-check"></i> <span id="btn-text">Gunakan Buku Cek</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jenisBukuSelect = document.getElementById('jenis-buku');
        const nomorSeriAwal = document.getElementById('input-nomor-seri-awal');
        const nomorSeriAkhir = document.getElementById('input-nomor-seri-akhir');

        function hitungNomorAkhir() {
            const jenisBuku = parseInt(jenisBukuSelect.value);
            const nomorAwal = nomorSeriAwal.value.trim();

            if (jenisBuku && nomorAwal && validateNomorSeri(nomorAwal)) {
                const match = nomorAwal.match(/^([A-Z]{3}\d{2})(\d+)$/);
                if (match) {
                    const prefix = match[1];
                    const nomorUrut = parseInt(match[2]);
                    const nomorAkhir = nomorUrut + jenisBuku - 1;
                    const panjangAsli = match[2].length;
                    const nomorAkhirFormatted = nomorAkhir.toString().padStart(panjangAsli, '0');

                    nomorSeriAkhir.value = prefix + nomorAkhirFormatted;
                }
            } else {
                nomorSeriAkhir.value = '';
            }
        }

        function validateNomorSeri(nomor) {
            const pattern = /^[A-Z]{3}\d{2}\d+$/;
            return pattern.test(nomor);
        }

        jenisBukuSelect.addEventListener('change', hitungNomorAkhir);
        nomorSeriAwal.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            hitungNomorAkhir();
        });

        nomorSeriAwal.addEventListener('blur', function() {
            const bukuValidation = document.getElementById('buku-validation');
            const jenisBuku = jenisBukuSelect.value;

            if (this.value && !validateNomorSeri(this.value)) {
                bukuValidation.innerHTML =
                    '<div class="alert alert-danger mt-2">Format nomor seri tidak valid. Gunakan format: 3 huruf + 2 angka + nomor urut</div>';
            } else if (this.value && jenisBuku) {
                validasiRangeNomorSeri(this.value, jenisBuku);
            } else {
                bukuValidation.innerHTML = '';
            }
        });

        function validasiRangeNomorSeri(nomorSeriAwal, jenisBuku) {
            const bukuValidation = document.getElementById('buku-validation');
            bukuValidation.innerHTML =
                '<div class="alert alert-info mt-2"><i class="fa fa-spinner fa-spin"></i> Memeriksa ketersediaan nomor seri...</div>';

            fetch('/admin/cek/validate-range-nomor-seri', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        nomor_seri_awal: nomorSeriAwal,
                        jenis_buku: jenisBuku
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        bukuValidation.innerHTML = `
                    <div class="alert alert-success mt-2">
                        <strong>✓ Range nomor seri tersedia</strong><br>
                        <small>
                            Buku: ${data.data.buku_kode} |
                            Jenis: ${data.data.jenis_buku} lembar |
                            Range: ${data.data.range}
                        </small>
                    </div>
                `;

                        if (data.nomor_seri_akhir) {
                            nomorSeriAkhir.value = data.nomor_seri_akhir;
                        }
                    } else {
                        let errorHtml =
                            `<div class="alert alert-danger mt-2"><strong>✗ ${data.message}</strong>`;

                        if (data.tidak_tersedia && data.tidak_tersedia.length > 0) {
                            errorHtml +=
                                '<br><small><strong>Nomor tidak tersedia:</strong><ul class="mb-0 mt-1">';
                            data.tidak_tersedia.slice(0, 5).forEach(item => {
                                let statusText = item.status === 'terpakai' ?
                                    `sudah digunakan${item.penerima ? ' oleh ' + item.penerima : ''}` :
                                    item.status === 'tidak_ditemukan' ? 'tidak ditemukan' : item
                                    .status;
                                errorHtml += `<li>${item.nomor_seri}: ${statusText}</li>`;
                            });

                            if (data.tidak_tersedia.length > 5) {
                                errorHtml +=
                                    `<li>... dan ${data.tidak_tersedia.length - 5} nomor lainnya</li>`;
                            }
                            errorHtml += '</ul></small>';
                        }

                        errorHtml += '</div>';
                        bukuValidation.innerHTML = errorHtml;

                        if (data.nomor_seri_akhir) {
                            nomorSeriAkhir.value = data.nomor_seri_akhir;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    bukuValidation.innerHTML =
                        '<div class="alert alert-warning mt-2">Gagal memvalidasi nomor seri. Silakan coba lagi.</div>';
                });
        }

        document.getElementById('form-gunakan').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const endpoint = '/admin/cek/gunakan-buku';

            formData.set('jenis_buku', document.getElementById('jenis-buku').value);
            formData.set('nomor_seri_awal', document.getElementById('input-nomor-seri-awal').value);
            formData.set('nomor_seri_akhir', document.getElementById('input-nomor-seri-akhir').value);

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';

            fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Buku cek berhasil digunakan',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#modalGunakan').modal('hide');

                        if (typeof table !== 'undefined' && table.ajax) {
                            table.ajax.reload();
                        }
                        if (typeof $('#tabelCek').DataTable === 'function') {
                            $('#tabelCek').DataTable().ajax.reload();
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);

                    let errorMessage = 'Terjadi kesalahan';
                    if (error.error) {
                        errorMessage = error.error;
                    } else if (error.message) {
                        errorMessage = error.message;
                    }

                    if (error.not_found) {
                        errorMessage += '\n\nNomor seri tidak ditemukan:\n' + error.not_found.join(
                            ', ');
                    } else if (error.tidak_tersedia) {
                        errorMessage += '\n\nNomor tidak tersedia:';
                        error.tidak_tersedia.forEach(item => {
                            errorMessage += `\n${item.nomor_seri}: ${item.status}`;
                            if (item.penerima) {
                                errorMessage += ` (${item.penerima})`;
                            }
                        });
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        });

        const modalElement = document.getElementById('modalGunakan');
        modalElement.addEventListener('hidden.bs.modal', function() {
            document.getElementById('form-gunakan').reset();
            document.getElementById('validation-messages').innerHTML = '';
            document.getElementById('buku-validation').innerHTML = '';
            nomorSeriAkhir.value = '';
        });
    });
</script>
