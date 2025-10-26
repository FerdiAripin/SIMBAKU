@extends('Master.Layouts.app', ['title' => $title])

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ 'Detail ' . $title }}</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-gray">Produk</li>
                <li class="breadcrumb-item text-gray"><a href="/admin/produk_baru">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h3 class="card-title">Stok {{ $barang->barang_nama }}</h3>
                    <div class="card-options">
                        <a href="/admin/masuk" class="btn btn-primary btn-sm">Tambah Stok</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="table-1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Type</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($barang_history) > 0)
                                @foreach ($barang_history as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->tanggal }}</td>
                                        <td class="{{ $item->type == 'masuk' ? 'text-success' : 'text-danger' }}">
                                            {{ $item->jumlah }}</td>
                                        <td>
                                            @if ($item->type == 'masuk')
                                                <span class="badge bg-success">{{ $item->type }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $item->type }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $item->type == 'masuk' ? ($item->bm_keterangan ?: '-') : ($item->bk_keterangan ?: '-') }}
                                        </td>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">No data available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection
