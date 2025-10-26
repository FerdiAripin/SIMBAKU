@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Admin</li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->

<!-- ROW 1 - INVENTORY WIDGETS -->
<div class="row">
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-primary img-card box-primary-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$kategori}}</h2>
                        <p class="text-white mb-0">Kategori</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-edit text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-info img-card box-info-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$barang}}</h2>
                        <p class="text-white mb-0">Produk ATM & Tabungan</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-package text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-success img-card box-success-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$bm}}</h2>
                        <p class="text-white mb-0">ATM & Tabungan Masuk</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-repeat text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-danger img-card box-danger-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$bk}}</h2>
                        <p class="text-white mb-0">ATM & Tabungan Keluar</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-repeat text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->
</div>
<!-- ROW 1 CLOSED -->

<!-- ROW 2 - CHECK BOOK WIDGETS -->
<div class="row">
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-purple img-card box-purple-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$total_buku_cek}}</h2>
                        <p class="text-white mb-0">Total Buku Cek</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-file-text text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-teal img-card box-teal-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$buku_cek_aktif}}</h2>
                        <p class="text-white mb-0">Buku Cek Tersedia</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-check-circle text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-orange img-card box-orange-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$total_lembar_tersedia}}</h2>
                        <p class="text-white mb-0">Total Lembar Cek Tersedia</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-layers text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-secondary img-card box-secondary-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$penggunaan_bulan_ini}}</h2>
                        <p class="text-white mb-0">Lembar Cek Terpakai Bulan ini</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-trending-up text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->
</div>
<!-- ROW 2 CLOSED -->



<!-- ROW 4 - USER WIDGET -->
<div class="row">
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-warning img-card box-warning-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$user}}</h2>
                        <p class="text-white mb-0">User</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-user text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-indigo img-card box-indigo-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$total_lembar_terpakai}}</h2>
                        <p class="text-white mb-0">Total Lembar Cek Terpakai</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-activity text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-pink img-card box-pink-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">
                            @if($total_lembar_tersedia > 0)
                                {{number_format(($total_lembar_terpakai / ($total_lembar_tersedia + $total_lembar_terpakai)) * 100, 1)}}%
                            @else
                                0%
                            @endif
                        </h2>
                        <p class="text-white mb-0">Tingkat Penggunaan Cek</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-pie-chart text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-cyan img-card box-cyan-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$total_lembar_tersedia + $total_lembar_terpakai}}</h2>
                        <p class="text-white mb-0">Total Semua Lembar Cek</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-database text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->
</div>
<!-- ROW 4 CLOSED -->

@endsection
