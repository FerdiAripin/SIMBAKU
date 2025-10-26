<!-- MODAL EDIT -->
<div class="modal fade" data-bs-backdrop="static" id="Umodaldemo8">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Ubah Kategori</h6><button aria-label="Close" class="btn-close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="idkategoriU">
                <div class="form-group">
                    <label for="kategoriU" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="kategoriU" class="form-control" placeholder="">
                </div>
                <div class="form-group">
                    <label for="ketU" class="form-label">Keterangan</label>
                    <textarea name="ketU" class="form-control" rows="4"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success d-none" id="btnLoaderU" type="button" disabled="">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Loading...
                </button>
                <a href="javascript:void(0)" onclick="checkFormU()" id="btnSimpanU" class="btn btn-success">Simpan Perubahan <i class="fe fe-check"></i></a>
                <a href="javascript:void(0)" class="btn btn-light" onclick="resetU()" data-bs-dismiss="modal">Batal <i class="fe fe-x"></i></a>
            </div>
        </div>
    </div>
</div>

@section('formEditJS')
<script>
    function checkFormU() {
        const kategori = $("input[name='kategoriU']").val();
        setLoadingU(true);
        resetValidU();

        if (kategori == "") {
            validasi('Kategori wajib di isi!', 'warning');
            $("input[name='kategoriU']").addClass('is-invalid');
            setLoadingU(false);
            return false;
        } else {
            submitFormU();
        }
    }

    function submitFormU() {
        const id = $("input[name='idkategoriU']").val();
        const kategori = $("input[name='kategoriU']").val();
        const ket = $("textarea[name='ketU']").val();

        $.ajax({
            type: 'POST',
            url: "{{url('admin/kategori/proses_ubah')}}/" + id,
            enctype: 'multipart/form-data',
            data: {
                kategori: kategori,
                ket: ket
            },
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
        $("input[name='kategoriU']").removeClass('is-invalid');
        $("textarea[name='ketU']").removeClass('is-invalid');
    };

    function resetU() {
        resetValidU();
        $("input[name='idkategoriU']").val('');
        $("input[name='kategoriU']").val('');
        $("textarea[name='ketU']").val('');
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
</script>
@endsection