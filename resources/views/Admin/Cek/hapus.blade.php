<!-- MODAL HAPUS -->
<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Hapus Buku Cek</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="form-hapus">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="buku_id" id="hapus-id">
                    <div class="text-center">
                        <i class="fe fe-alert-triangle text-danger" style="font-size: 3rem;"></i>
                        <h4 class="text-danger mt-3">Konfirmasi Hapus</h4>
                        <p class="mb-3">Hapus buku cek ini secara permanen?</p>
                        <div class="alert alert-danger py-2">
                            <i class="fe fe-trash-2 me-2"></i>
                            <small><strong>Tindakan ini tidak dapat dibatalkan</strong></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fe fe-trash-2"></i> Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updatePreview() {
        const kodeHuruf = document.querySelector('input[name="kode_huruf"]').value.toUpperCase();
        const kodeAngka = document.querySelector('input[name="kode_angka"]').value;
        const nomorAwal = document.querySelector('input[name="nomor_awal"]').value;
        const jenisSelect = document.querySelector('select[name="jenis_buku"]');
        const jumlahBuku = document.querySelector('input[name="jumlah_buku"]').value;
        const previewDiv = document.getElementById('preview-nomor-seri');
        const btnSubmit = document.getElementById('btn-submit');

        if (kodeHuruf.length === 3 && kodeAngka.length === 2 && nomorAwal) {
            const jenisBuku = parseInt(jenisSelect.value) || 5;
            const jumlah = parseInt(jumlahBuku) || 1;

            const nomorAwalInt = parseInt(nomorAwal);
            const nomorAkhirTotal = nomorAwalInt + (jumlah * jenisBuku) - 1;

            let previewText =
                `<strong>${kodeHuruf}${kodeAngka}${nomorAwal}</strong> s/d <strong>${kodeHuruf}${kodeAngka}${nomorAkhirTotal}</strong>`;
            previewText +=
                `<br><small class="text-muted">${jumlah} buku × ${jenisBuku} lembar = ${jumlah * jenisBuku} lembar total</small>`;

            previewDiv.innerHTML = previewText;
            btnSubmit.disabled = true;
            document.getElementById('range-check-result').innerHTML = '';
        } else {
            previewDiv.innerHTML = '<span class="text-muted">Isi semua field untuk melihat preview</span>';
            btnSubmit.disabled = true;
        }
    }

    function checkRange() {
        const kodeHuruf = document.querySelector('input[name="kode_huruf"]').value.toUpperCase();
        const kodeAngka = document.querySelector('input[name="kode_angka"]').value;
        const nomorAwal = document.querySelector('input[name="nomor_awal"]').value;
        const jenisSelect = document.querySelector('select[name="jenis_buku"]');
        const jumlahBuku = document.querySelector('input[name="jumlah_buku"]').value;

        if (!kodeHuruf || !kodeAngka || !nomorAwal || !jenisSelect.value || !jumlahBuku) {
            alert('Harap isi semua field terlebih dahulu');
            return;
        }

        const jenisBuku = parseInt(jenisSelect.value);
        const jumlah = parseInt(jumlahBuku);
        const totalLembar = jumlah * jenisBuku;

        const btnCheck = document.getElementById('btn-check-range');
        const btnSubmit = document.getElementById('btn-submit');
        const resultDiv = document.getElementById('range-check-result');

        btnCheck.disabled = true;
        btnCheck.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengecek...';

        $.post('{{ route('cek.check.range') }}', {
                kode_huruf: kodeHuruf,
                kode_angka: kodeAngka,
                nomor_awal: parseInt(nomorAwal),
                jumlah_lembar: totalLembar,
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                if (response.available) {
                    resultDiv.innerHTML =
                        '<div class="alert alert-success"><i class="fe fe-check-circle me-2"></i>' + response
                        .message + '<br><small>Range: ' + response.range + '</small></div>';
                    btnSubmit.disabled = false;
                } else {
                    let conflictList = response.conflicts.slice(0, 10).join(', ');
                    if (response.conflicts.length > 10) {
                        conflictList += '... dan ' + (response.conflicts.length - 10) + ' lainnya';
                    }
                    resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fe fe-x-circle me-2"></i>' +
                        response.message + '<br><small>Konflik: ' + conflictList + '</small></div>';
                    btnSubmit.disabled = true;
                }
            })
            .fail(function() {
                resultDiv.innerHTML =
                    '<div class="alert alert-warning"><i class="fe fe-alert-triangle me-2"></i>Gagal mengecek ketersediaan range</div>';
                btnSubmit.disabled = true;
            })
            .always(function() {
                btnCheck.disabled = false;
                btnCheck.innerHTML = 'Cek Ketersediaan';
            });
    }

    let allLembarData = [];

    function filterLembar(status) {
        const tbody = document.getElementById('detail-lembar-body');

        if (status === 'all') {
            displayLembarData(allLembarData);
        } else {
            const filteredData = allLembarData.filter(lembar => lembar.status === status);
            displayLembarData(filteredData);
        }

        document.querySelectorAll('.modal button[onclick*="filterLembar"]').forEach(btn => {
            btn.classList.remove('btn-success', 'btn-primary', 'btn-warning', 'btn-danger', 'btn-secondary');
            btn.classList.add('btn-outline-' + (btn.textContent.toLowerCase().includes('tersedia') ? 'success' :
                btn.textContent.toLowerCase().includes('terpakai') ? 'primary' :
                btn.textContent.toLowerCase().includes('rusak') ? 'warning' :
                btn.textContent.toLowerCase().includes('hilang') ? 'danger' : 'secondary'));
        });

        event.target.classList.remove('btn-outline-success', 'btn-outline-primary', 'btn-outline-warning',
            'btn-outline-danger', 'btn-outline-secondary');
        event.target.classList.add('btn-' + (status === 'tersedia' ? 'success' :
            status === 'terpakai' ? 'primary' :
            status === 'rusak' ? 'warning' :
            status === 'hilang' ? 'danger' : 'secondary'));
    }

    function displayLembarData(data) {
        const tbody = document.getElementById('detail-lembar-body');
        let tableBody = '';

        data.forEach(function(lembar, index) {
            let statusClass = lembar.status == 'tersedia' ? 'success' :
                lembar.status == 'terpakai' ? 'primary' :
                lembar.status == 'rusak' ? 'warning' : 'danger';
            let nomorSeriDisplay = lembar.nomor_seri;
            let breakdownDisplay = '';
            if (lembar.nomor_seri && lembar.nomor_seri.length >= 5) {
                let huruf = lembar.nomor_seri.substring(0, 3);
                let angka = lembar.nomor_seri.substring(3, 5);
                let urut = lembar.nomor_seri.substring(5);
                breakdownDisplay = `<small class="text-muted">${huruf}-${angka}-${urut}</small>`;
            }

            tableBody += '<tr>' +
                '<td>' + (index + 1) + '</td>' +
                '<td><strong>' + nomorSeriDisplay + '</strong></td>' +
                '<td>' + breakdownDisplay + '</td>' +
                '<td><span class="badge bg-' + statusClass + '">' + lembar.status + '</span></td>' +
                '<td>' + (lembar.penerima || '-') + '</td>' +
                '<td>' + (lembar.tanggal_pakai || '-') + '</td>' +
                '<td>' + (lembar.nominal ? 'Rp ' + new Intl.NumberFormat('id-ID').format(lembar.nominal) :
                    '-') + '</td>' +
                '</tr>';
        });

        tbody.innerHTML = tableBody;
    }

    function exportDetailToCSV() {
        if (allLembarData.length === 0) {
            alert('Tidak ada data untuk diekspor');
            return;
        }

        let csvContent = "No,Nomor Seri,Kode Huruf,Kode Angka,Nomor Urut,Status,Penerima,Tanggal Pakai,Nominal\n";

        allLembarData.forEach(function(lembar, index) {
            let huruf = lembar.nomor_seri && lembar.nomor_seri.length >= 3 ? lembar.nomor_seri.substring(0, 3) :
                '';
            let angka = lembar.nomor_seri && lembar.nomor_seri.length >= 5 ? lembar.nomor_seri.substring(3, 5) :
                '';
            let urut = lembar.nomor_seri && lembar.nomor_seri.length > 5 ? lembar.nomor_seri.substring(5) : '';

            csvContent += [
                index + 1,
                lembar.nomor_seri || '',
                huruf,
                angka,
                urut,
                lembar.status || '',
                lembar.penerima || '',
                lembar.tanggal_pakai || '',
                lembar.nominal || ''
            ].join(',') + "\n";
        });

        const blob = new Blob([csvContent], {
            type: 'text/csv'
        });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'detail_lembar_cek_' + new Date().getTime() + '.csv';
        a.click();
        window.URL.revokeObjectURL(url);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputs = ['kode_huruf', 'kode_angka', 'nomor_awal', 'jenis_buku', 'jumlah_buku'];

        inputs.forEach(function(inputName) {
            const element = document.querySelector(
                `input[name="${inputName}"], select[name="${inputName}"]`);
            if (element) {
                element.addEventListener('input', updatePreview);
                element.addEventListener('change', updatePreview);
            }
        });

        const kodeHurufInput = document.querySelector('input[name="kode_huruf"]');
        if (kodeHurufInput) {
            kodeHurufInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^A-Za-z]/g, '').toUpperCase();
                if (this.value.length > 3) {
                    this.value = this.value.slice(0, 3);
                }
                updatePreview();
            });
        }

        const kodeAngkaInput = document.querySelector('input[name="kode_angka"]');
        if (kodeAngkaInput) {
            kodeAngkaInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 2) {
                    this.value = this.value.slice(0, 2);
                }
                updatePreview();
            });
        }

        const btnCheckRange = document.getElementById('btn-check-range');
        if (btnCheckRange) {
            btnCheckRange.addEventListener('click', checkRange);
        }

        const originalDetail = window.detail;
        window.detail = function(id) {
            $.get('{{ route('cek.show', '') }}/' + id, function(data) {
                allLembarData = data.lembar_cek || [];

                if (originalDetail) {
                    originalDetail(id);
                } else {
                    $('#detail-kode').text(data.buku_kode);
                    $('#detail-jenis').text('Cek ' + data.jenis_buku + ' Lembar');
                    $('#detail-seri').text(data.nomor_seri_awal + ' - ' + data.nomor_seri_akhir);
                    $('#detail-status').html('<span class="badge bg-' + (data.status == 'aktif' ?
                        'success' : 'warning') + '">' + data.status + '</span>');
                    $('#detail-tanggal').text(data.tanggal_terbit);
                    $('#detail-keterangan').text(data.keterangan || '-');

                    if (data.kode_huruf && data.kode_angka) {
                        $('#detail-format').text(data.kode_huruf + data.kode_angka + 'XXXXXX');
                    } else {
                        $('#detail-format').text('Format lama');
                    }

                    displayLembarData(allLembarData);
                    $('#modalDetail').modal('show');
                }
            });
        };
    });
</script>
