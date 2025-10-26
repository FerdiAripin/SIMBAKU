@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<div class="page-header">
    <h1 class="page-title">{{$title}}</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Master Data</li>
            <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
        </ol>
    </div>
</div>

<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between">
                <h3 class="card-title">Data</h3>
                @if($hakTambah > 0)
                <div>
                    <a class="modal-effect btn btn-primary-light" onclick="generateID()" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#modaldemo8">Tambah Data <i class="fe fe-plus"></i></a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-1" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <th class="border-bottom-0" width="1%">No</th>
                            <th class="border-bottom-0">Gambar</th>
                            <th class="border-bottom-0">Kode Produk</th>
                            <th class="border-bottom-0">Nama Produk</th>
                            <th class="border-bottom-0">Kategori</th>
                            <th class="border-bottom-0">Stok</th>
                            <th class="border-bottom-0" width="1%">Action</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Admin.ProdukBaru.tambah', ['kategori' => $kategori])
@include('Admin.ProdukBaru.edit', ['kategori' => $kategori])
@include('Admin.ProdukBaru.hapus')
@include('Admin.ProdukBaru.gambar')

<script>
    function generateID(){
        id = new Date().getTime();
        $("input[name='kode']").val("PRD-"+id);
    }
    function update(data){
        $("input[name='idbarangU']").val(data.barang_id);
        $("input[name='kodeU']").val(data.barang_kode);
        $("input[name='namaU']").val(data.barang_nama.replace(/_/g, ' '));
        $("select[name='kategoriU']").val(data.kategori_id);
        $("input[name='stokU']").val(data.barang_stok);
        if(data.barang_gambar != 'image.png'){
            $("#outputImgU").attr("src", "{{asset('storage/barang/')}}"+"/"+data.barang_gambar);
        }
    }
    function hapus(data) {
        $("input[name='idbarang']").val(data.barang_id);
        $("#vbarang").html("barang " + "<b>" + data.barang_nama.replace(/_/g, ' ') + "</b>");
    }
    function gambar(data) {
        if(data.barang_gambar != 'image.png'){
            $("#outputImgG").attr("src", "{{asset('storage/barang/')}}"+"/"+data.barang_gambar);
        }else{
            $("#outputImgG").attr("src", "{{url('/assets/default/barang/image.png')}}");
        }
    }
    function validasi(judul, status) {
        swal({
            title: judul,
            type: status,
            confirmButtonText: "Iya"
        });
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
        table = $('#table-1').DataTable({
            "processing": true,
            "serverSide": true,
            "info": true,
            "order": [],
            "stateSave":true,
            "scrollX": true,
            "lengthMenu": [
                [5, 10, 25, 50, 100],
                [5, 10, 25, 50, 100]
            ],
            "pageLength": 10,
            lengthChange: true,
            "ajax": {
                "url": "{{route('produkbaru.getprodukbaru')}}",
            },
            "columns": [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false
                },
                {
                    data: 'img',
                    name: 'barang_gambar',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'barang_kode',
                    name: 'barang_kode',
                },
                {
                    data: 'barang_nama',
                    name: 'barang_nama',
                },
                {
                    data: 'kategori',
                    name: 'kategori_nama',
                },
                {
                    data: 'totalstok',
                    name: 'barang_stok',
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
