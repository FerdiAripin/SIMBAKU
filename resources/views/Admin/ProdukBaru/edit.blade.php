<!-- MODAL EDIT -->
<div class="modal fade" data-bs-backdrop="static" id="Umodaldemo8">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Ubah Produk</h6><button onclick="resetU()" aria-label="Close" class="btn-close"
                    data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="idbarangU">
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label for="kodeU" class="form-label">Kode Produk <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="kodeU" readonly class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="namaU" class="form-label">Nama Produk <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="namaU" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-control">
                                <option value="">-- Pilih --</option>
                                @foreach ($kategori as $k)
                                    @if ($k->kategori_nama !== 'CEK')
                                        <option value="{{ $k->kategori_id }}">{{ $k->kategori_nama }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="stokU" class="form-label">Stok Awal <span
                                    class="text-danger">*</span></label>
                            <input type="text"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');"
                                name="stokU" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="title" class="form-label">Foto</label>
                            <center>
                                <img src="{{ url('/assets/default/barang/image.png') }}" width="80%"
                                    alt="profile-user" id="outputImgU" class="">
                            </center>
                            <input class="form-control mt-5" id="GetFileU" name="photoU" type="file"
                                onchange="VerifyFileNameAndFileSizeU()" accept=".png,.jpeg,.jpg,.svg">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success d-none" id="btnLoaderU" type="button" disabled="">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Loading...
                </button>
                <a href="javascript:void(0)" onclick="checkFormU()" id="btnSimpanU" class="btn btn-success">Simpan
                    Perubahan <i class="fe fe-check"></i></a>
                <a href="javascript:void(0)" class="btn btn-light" onclick="resetU()" data-bs-dismiss="modal">Batal <i
                        class="fe fe-x"></i></a>
            </div>
        </div>
    </div>
</div>

@section('formEditJS')
    <script>
        function checkFormU() {
            const kode = $("input[name='kodeU']").val();
            const nama = $("input[name='namaU']").val();
            const stok = $("input[name='stokU']").val();
            setLoadingU(true);
            resetValidU();
            if (kode == "") {
                validasi('Kode Barang wajib di isi!', 'warning');
                $("input[name='kodeU']").addClass('is-invalid');
                setLoadingU(false);
                return false;
            } else if (nama == "") {
                validasi('Nama Barang wajib di isi!', 'warning');
                $("input[name='namaU']").addClass('is-invalid');
                setLoadingU(false);
                return false;
            } else if (stok == "") {
                validasi('Stok Awal wajib di isi!', 'warning');
                $("input[name='stok']").addClass('is-invalid');
                setLoadingU(false);
                return false;
            } else {
                submitFormU();
            }
        }

        function submitFormU() {
            const id = $("input[name='idbarangU']").val();
            const kode = $("input[name='kodeU']").val();
            const nama = $("input[name='namaU']").val();
            const kategori = $("select[name='kategoriU']").val();
            const stok = $("input[name='stokU']").val();
            const foto = $('#GetFileU')[0].files;

            var fd = new FormData();

            // Append data
            fd.append('foto', foto[0]);
            fd.append('kode', kode);
            fd.append('nama', nama);
            fd.append('kategori', kategori);
            fd.append('stok', stok);
            $.ajax({
                type: 'POST',
                url: "{{ url('admin/produk_baru/proses_ubah') }}/" + id,
                processData: false,
                contentType: false,
                dataType: 'json',
                data: fd,
                success: function(data) {
                    swal({
                        title: "Berhasil diubah!",
                        type: "success"
                    });
                    $('#Umodaldemo8').modal('toggle');
                    table.ajax.reload(null, false);
                    resetU();
                }
            });
        }

        function resetValidU() {
            $("input[name='kodeU']").removeClass('is-invalid');
            $("input[name='namaU']").removeClass('is-invalid');
            $("select[name='kategoriU']").removeClass('is-invalid');
            $("input[name='stokU']").removeClass('is-invalid');
        };

        function resetU() {
            resetValidU();
            $("input[name='idbarangU']").val('');
            $("input[name='noU']").val('');
            $("input[name='namaU']").val('');
            $("select[name='kategoriU']").val('');
            $("input[name='stokU']").val('');
            $("#outputImgU").attr("src", "{{ url('/assets/default/barang/image.png') }}");
            $("#GetFileU").val('');
            setLoadingU(false);
        }

        function setLoadingU(bool) {
            if (bool == true) {
                $('#btnLoaderU').removeClass('d-none');
                $('#btnSimpanU').addClass('d-none');
            } else {
                $('#btnSimpanU').removeClass('d-none');
                $('#btnLoaderU').addClass('d-none');
            }
        }

        function fileIsValidU(fileName) {
            var ext = fileName.match(/\.([^\.]+)$/)[1];
            ext = ext.toLowerCase();
            var isValid = true;
            switch (ext) {
                case 'png':
                case 'jpeg':
                case 'jpg':
                case 'svg':
                    break;
                default:
                    this.value = '';
                    isValid = false;
            }
            return isValid;
        }

        function VerifyFileNameAndFileSizeU() {
            var file = document.getElementById('GetFileU').files[0];
            if (file != null) {
                var fileName = file.name;
                if (fileIsValidU(fileName) == false) {
                    validasi('Format bukan gambar!', 'warning');
                    document.getElementById('GetFileU').value = null;
                    return false;
                }
                var content;
                var size = file.size;
                if ((size != null) && ((size / (1024 * 1024)) > 3)) {
                    validasi('Ukuran Maximum 1 MB', 'warning');
                    document.getElementById('GetFileU').value = null;
                    return false;
                }
                var ext = fileName.match(/\.([^\.]+)$/)[1];
                ext = ext.toLowerCase();
                document.getElementById('outputImgU').src = window.URL.createObjectURL(file);
                return true;
            } else
                return false;
        }
    </script>
@endsection
