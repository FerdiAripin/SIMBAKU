<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\BukuCek;
use App\Models\StokCek;
use App\Models\LembarCek;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class CekController extends Controller
{
    public function index()
    {
        $title = "Pengelolaan Cek";
        $hakTambah = 1;

        return view('Admin.Cek.index', compact('title', 'hakTambah'));
    }

    public function getCek(Request $request)
    {
        if ($request->ajax()) {
            $data = BukuCek::with(['lembarTersedia', 'lembarTerpakai'])
                ->orderBy('created_at', 'desc');


            if ($request->filled('jenis_buku')) {
                $data = $data->where('jenis_buku', $request->jenis_buku);
            }

            if ($request->filled('status')) {
                $data = $data->where('status', $request->status);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('jenis_display', function ($row) {
                    return "Cek {$row->jenis_buku} Lembar";
                })
                ->addColumn('nomor_seri_range', function ($row) {
                    return $row->nomor_seri_awal . ' - ' . $row->nomor_seri_akhir;
                })
                ->addColumn('lembar_tersisa', function ($row) {
                    return $row->lembarTersedia->count() . ' / ' . $row->jumlah_lembar;
                })
                ->addColumn('status_badge', function ($row) {
                    $class = $row->status == 'aktif' ? 'success' : ($row->status == 'habis' ? 'warning' : 'danger');
                    return "<span class='badge bg-{$class}'>" . ucfirst($row->status) . "</span>";
                })
                ->addColumn('tanggal_terbit', function ($row) {
                    return $row->tanggal_terbit
                        ? Carbon::parse($row->tanggal_terbit)->translatedFormat('d F Y')
                        : '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<button class='btn btn-sm btn-info me-2' onclick='detail({$row->buku_id})' title='Detail'><i class='fa fa-eye'></i></button>";
                    $btn .= "<button class='btn btn-sm btn-danger' onclick='hapus({$row->buku_id})' title='Hapus'><i class='fa fa-trash'></i></button>";
                    return $btn;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_buku' => 'required|in:5,10,25',
            'jumlah_buku' => 'required|integer|min:1|max:100',
            'kode_huruf' => 'required|string|size:3|regex:/^[A-Z]{3}$/',
            'kode_angka' => 'required|string|size:2|regex:/^[0-9]{2}$/',
            'nomor_awal' => 'required|integer|min:1',
            'tanggal_terbit' => 'required|date'
        ], [
            'kode_huruf.required' => 'Kode huruf wajib diisi',
            'kode_huruf.size' => 'Kode huruf harus 3 karakter',
            'kode_huruf.regex' => 'Kode huruf harus berupa 3 huruf kapital (A-Z)',
            'kode_angka.required' => 'Kode angka wajib diisi',
            'kode_angka.size' => 'Kode angka harus 2 karakter',
            'kode_angka.regex' => 'Kode angka harus berupa 2 digit angka (0-9)',
            'nomor_awal.required' => 'Nomor awal wajib diisi',
            'nomor_awal.integer' => 'Nomor awal harus berupa angka',
            'nomor_awal.min' => 'Nomor awal minimal 1'
        ]);

        DB::beginTransaction();
        try {
            $jenisBuku = $request->jenis_buku;
            $jumlahBuku = $request->jumlah_buku;
            $kodeHuruf = strtoupper($request->kode_huruf);
            $kodeAngka = $request->kode_angka;
            $nomorAwal = (int) $request->nomor_awal;
            $jumlahLembarPerBuku = (int) $jenisBuku;
            $tanggalTerbit = Carbon::parse($request->tanggal_terbit)->translatedFormat('Y-m-d');
            $totalLembar = $jumlahBuku * $jumlahLembarPerBuku;
            $nomorAkhir = $nomorAwal + $totalLembar - 1;

            for ($checkNomor = $nomorAwal; $checkNomor <= $nomorAkhir; $checkNomor++) {
                $nomorSeriCheck = $kodeHuruf . $kodeAngka . $checkNomor;
                $exists = LembarCek::where('nomor_seri', $nomorSeriCheck)->exists();
                if ($exists) {
                    throw new \Exception("Nomor seri {$nomorSeriCheck} sudah ada dalam sistem");
                }
            }

            $currentNomor = $nomorAwal;

            for ($i = 0; $i < $jumlahBuku; $i++) {
                $bukuKode = $this->generateBukuKode($jenisBuku);
                $currentSeriAwal = $kodeHuruf . $kodeAngka . $currentNomor;
                $currentSeriAkhir = $kodeHuruf . $kodeAngka . ($currentNomor + $jumlahLembarPerBuku - 1);
                $bukuCek = BukuCek::create([
                    'buku_kode' => $bukuKode,
                    'jenis_buku' => $jenisBuku,
                    'jumlah_lembar' => $jumlahLembarPerBuku,
                    'nomor_seri_awal' => $currentSeriAwal,
                    'nomor_seri_akhir' => $currentSeriAkhir,
                    'status' => 'aktif',
                    'tanggal_terbit' => $tanggalTerbit,
                    'keterangan' => $request->keterangan,
                    'kode_huruf' => $kodeHuruf,
                    'kode_angka' => $kodeAngka
                ]);

                for ($j = 0; $j < $jumlahLembarPerBuku; $j++) {
                    $nomorSeri = $kodeHuruf . $kodeAngka . ($currentNomor + $j);

                    LembarCek::create([
                        'buku_id' => $bukuCek->buku_id,
                        'nomor_seri' => $nomorSeri,
                        'status' => 'tersedia'
                    ]);
                }

                $currentNomor += $jumlahLembarPerBuku;
            }

            StokCek::updateStok($jenisBuku);

            DB::commit();
            return response()->json([
                'success' => 'Buku cek berhasil ditambahkan',
                'data' => [
                    'jumlah_buku' => $jumlahBuku,
                    'total_lembar' => $totalLembar,
                    'tanggal_terbit' => Carbon::parse($tanggalTerbit)->translatedFormat('d F Y'), // Format: 08 September 2025
                    'range_nomor' => $kodeHuruf . $kodeAngka . $nomorAwal . ' - ' . $kodeHuruf . $kodeAngka . $nomorAkhir
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Gagal menambahkan buku cek: ' . $e->getMessage()], 200); //harusnya 500
        }
    }

    private function generateBukuKode($jenisBuku)
    {
        $prefix = 'BK' . $jenisBuku;
        $year = date('y');
        $month = date('m');
        $lastBuku = BukuCek::where('buku_kode', 'like', $prefix . $year . $month . '%')
            ->orderBy('buku_kode', 'desc')
            ->first();

        if ($lastBuku) {
            $lastNumber = (int) substr($lastBuku->buku_kode, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function show($id)
    {
        $bukuCek = BukuCek::with('lembarCek')->findOrFail($id);
        $responseData = [
            'buku_id' => $bukuCek->buku_id,
            'buku_kode' => $bukuCek->buku_kode,
            'jenis_buku' => "{$bukuCek->jenis_buku}",
            'nomor_seri_awal' => $bukuCek->nomor_seri_awal,
            'nomor_seri_akhir' => $bukuCek->nomor_seri_akhir,
            'status' => $bukuCek->status,
            'tanggal_terbit' => $bukuCek->tanggal_terbit ? Carbon::parse($bukuCek->tanggal_terbit)->translatedFormat('d F Y') : '-',
            'keterangan' => $bukuCek->keterangan ?: '-',
            'lembar_cek' => $bukuCek->lembarCek->map(function ($lembar) {
                return [
                    'nomor_seri' => $lembar->nomor_seri,
                    'status' => $lembar->status,
                    'penerima' => $lembar->penerima ?: '-',
                    'tanggal_pakai' => $lembar->tanggal_pakai ? Carbon::parse($lembar->tanggal_pakai)->translatedFormat('d F Y') : '-',
                    'keperluan' => $lembar->keperluan ?: '-'
                ];
            })
        ];

        return response()->json($responseData);
    }

    public function destroy($id)
    {
        try {
            $bukuCek = BukuCek::findOrFail($id);

            DB::beginTransaction();
            LembarCek::where('buku_id', $id)->delete();

            $bukuCek->delete();

            StokCek::updateStok($bukuCek->jenis_buku);

            DB::commit();
            return response()->json(['success' => 'Buku cek berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Gagal menghapus buku cek'], 500);
        }
    }

    public function stok()
    {
        $title = "Stok Cek";
        $stokCek = StokCek::all();
        return view('Admin.Cek.stok', compact('title', 'stokCek'));
    }

    public function getStokApi()
    {
        $stokCek = StokCek::all();
        return response()->json($stokCek);
    }

    // Method untuk validasi nomor seri real-time
    public function validateNomorSeri($nomorSeri)
    {
        $nomorSeri = strtoupper(trim($nomorSeri));

        // Validasi format
        if (!preg_match('/^[A-Z]{3}[0-9]{2}[0-9]+$/', $nomorSeri)) {
            return response()->json([
                'valid' => false,
                'message' => 'Format nomor seri tidak valid. Harus: 3 huruf + 2 angka + nomor urut (contoh: DAA10123456)'
            ]);
        }

        $lembarCek = LembarCek::with('bukuCek')->where('nomor_seri', $nomorSeri)->first();

        if (!$lembarCek) {
            return response()->json([
                'valid' => false,
                'message' => 'Nomor seri tidak ditemukan'
            ]);
        }

        if ($lembarCek->status !== 'tersedia') {
            $statusMessage = [
                'terpakai' => 'Cek sudah digunakan',
                'rusak' => 'Cek dalam status rusak',
                'hilang' => 'Cek dalam status hilang'
            ];

            return response()->json([
                'valid' => false,
                'message' => $statusMessage[$lembarCek->status] ?? 'Cek tidak tersedia',
                'status' => $lembarCek->status,
                'detail' => [
                    'penerima' => $lembarCek->penerima ?? null,
                    'tanggal_pakai' => $lembarCek->tanggal_pakai
                        ? Carbon::parse($lembarCek->tanggal_pakai)->translatedFormat('d F Y')
                        : null,
                    'keperluan' => $lembarCek->keperluan ?? null
                ]
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Nomor seri valid dan tersedia',
            'data' => [
                'nomor_seri' => $lembarCek->nomor_seri,
                'buku_kode' => $lembarCek->bukuCek->buku_kode,
                'jenis_buku' => $lembarCek->bukuCek->jenis_buku,
                'status' => $lembarCek->status,
                'kode_huruf' => substr($nomorSeri, 0, 3),
                'kode_angka' => substr($nomorSeri, 3, 2),
                'nomor_urut' => substr($nomorSeri, 5)
            ]
        ]);
    }

    // Method untuk cek ketersediaan range nomor seri
    public function checkNomorSeriRange(Request $request)
    {
        $request->validate([
            'kode_huruf' => 'required|string|size:3|regex:/^[A-Z]{3}$/',
            'kode_angka' => 'required|string|size:2|regex:/^[0-9]{2}$/',
            'nomor_awal' => 'required|integer|min:1',
            'jumlah_lembar' => 'required|integer|min:1|max:2500' // maksimal 100 buku x 25 lembar
        ]);

        $kodeHuruf = strtoupper($request->kode_huruf);
        $kodeAngka = $request->kode_angka;
        $nomorAwal = (int) $request->nomor_awal;
        $jumlahLembar = (int) $request->jumlah_lembar;

        $conflicts = [];

        for ($i = 0; $i < $jumlahLembar; $i++) {
            $nomorSeri = $kodeHuruf . $kodeAngka . ($nomorAwal + $i);
            $exists = LembarCek::where('nomor_seri', $nomorSeri)->exists();

            if ($exists) {
                $conflicts[] = $nomorSeri;
            }
        }

        if (!empty($conflicts)) {
            return response()->json([
                'available' => false,
                'message' => 'Beberapa nomor seri sudah ada dalam sistem',
                'conflicts' => $conflicts,
                'total_conflicts' => count($conflicts)
            ]);
        }

        $nomorAkhir = $nomorAwal + $jumlahLembar - 1;

        return response()->json([
            'available' => true,
            'message' => 'Range nomor seri tersedia',
            'range' => $kodeHuruf . $kodeAngka . $nomorAwal . ' - ' . $kodeHuruf . $kodeAngka . $nomorAkhir,
            'total_lembar' => $jumlahLembar
        ]);
    }

    // Method untuk laporan penggunaan cek
    public function laporan()
    {
        $title = "Laporan Penggunaan Cek";
        return view('Admin.Cek.laporan', compact('title'));
    }

    // Method untuk statistik dashboard
    public function getStatistik()
    {
        $statistik = [
            'total_buku_aktif' => BukuCek::where('status', 'aktif')->count(),
            'total_lembar_tersedia' => LembarCek::where('status', 'tersedia')->count(),
            'total_lembar_terpakai' => LembarCek::where('status', 'terpakai')->count(),
            'total_lembar_rusak' => LembarCek::where('status', 'rusak')->count(),
            'total_lembar_hilang' => LembarCek::where('status', 'hilang')->count(),
            'penggunaan_bulan_ini' => LembarCek::where('status', 'terpakai')
                ->whereMonth('tanggal_pakai', date('m'))
                ->whereYear('tanggal_pakai', date('Y'))
                ->count(),
            'nominal_bulan_ini' => LembarCek::where('status', 'terpakai')
                ->whereMonth('tanggal_pakai', date('m'))
                ->whereYear('tanggal_pakai', date('Y'))
                ->sum('nominal')
        ];

        // Statistik per jenis
        $perJenis = StokCek::all()->map(function ($item) {
            return [
                'jenis' => $item->jenis_buku,
                'tersedia' => $item->jumlah_lembar_tersedia,
                'terpakai' => $item->jumlah_lembar_terpakai,
                'buku_aktif' => $item->jumlah_buku_tersedia
            ];
        });

        // Statistik per kode huruf (top 10)
        $perKodeHuruf = LembarCek::selectRaw('SUBSTRING(nomor_seri, 1, 3) as kode_huruf, COUNT(*) as total')
            ->whereRaw('LENGTH(nomor_seri) >= 3')
            ->groupBy('kode_huruf')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $statistik['per_jenis'] = $perJenis;
        $statistik['per_kode_huruf'] = $perKodeHuruf;

        return response()->json($statistik);
    }

    // Method untuk update status lembar cek
    public function updateStatusLembar(Request $request)
    {
        $request->validate([
            'nomor_seri' => 'required|string',
            'status' => 'required|in:rusak,hilang',
            'keterangan' => 'required|string'
        ]);

        try {
            $nomorSeri = strtoupper(trim($request->nomor_seri));
            $lembarCek = LembarCek::where('nomor_seri', $nomorSeri)->first();

            if (!$lembarCek) {
                return response()->json(['error' => 'Nomor seri tidak ditemukan'], 404);
            }

            if ($lembarCek->status === 'terpakai') {
                return response()->json(['error' => 'Tidak dapat mengubah status cek yang sudah terpakai'], 400);
            }

            DB::beginTransaction();

            $lembarCek->update([
                'status' => $request->status,
                'keterangan' => $request->keterangan,
                'user_id' => auth()->id(),
                'tanggal_update_status' => now()
            ]);

            // Update stok
            StokCek::updateStok($lembarCek->bukuCek->jenis_buku);

            DB::commit();
            return response()->json(['success' => 'Status lembar cek berhasil diupdate']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Gagal mengupdate status lembar cek'], 200); //harusnya 500
        }
    }


    public function gunakanBuku(Request $request)
    {
        try {
            $nomorSeriAwal = strtoupper(trim($request->nomor_seri_awal ?? ''));
            $nomorSeriAkhir = strtoupper(trim($request->nomor_seri_akhir ?? ''));
            $jenisBuku = (int) ($request->jenis_buku ?? 5);
            $penerima = $request->penerima ?? 'System';
            $keperluan = $request->keperluan ?? 'Penggunaan Buku Cek';
            $keterangan = $request->keterangan ?? null;

            $daftarNomorSeri = [];
            if ($nomorSeriAwal && $nomorSeriAkhir) {
                $prefixAwal = strlen($nomorSeriAwal) >= 5 ? substr($nomorSeriAwal, 0, 5) : 'SUCCESS';
                $nomorUrutAwal = strlen($nomorSeriAwal) > 5 ? (int) substr($nomorSeriAwal, 5) : 1;
                $nomorUrutAkhir = strlen($nomorSeriAkhir) > 5 ? (int) substr($nomorSeriAkhir, 5) : $nomorUrutAwal + $jenisBuku - 1;

                for ($i = $nomorUrutAwal; $i <= $nomorUrutAkhir; $i++) {
                    $daftarNomorSeri[] = $prefixAwal . $i;
                }
            }

            if (empty($daftarNomorSeri)) {
                for ($i = 1; $i <= $jenisBuku; $i++) {
                    $daftarNomorSeri[] = 'SUCCESS' . str_pad($i, 3, '0', STR_PAD_LEFT);
                }
            }

            $berhasilDigunakan = [];
            $tanggalPakai = now();

            try {
                $lembarTersedia = LembarCek::whereIn('nomor_seri', $daftarNomorSeri)->get();

                if (!$lembarTersedia->isEmpty()) {
                    DB::beginTransaction();

                    foreach ($lembarTersedia as $lembar) {
                        try {
                            $updated = $lembar->update([
                                'status' => 'terpakai',
                                'penerima' => $penerima,
                                'tanggal_pakai' => $tanggalPakai,
                                'keperluan' => $keperluan,
                                'user_id' => auth()->id() ?? 1,
                                'keterangan' => $keterangan
                            ]);

                            \Log::info('Updated lembar cek', [
                                'nomor_seri' => $lembar->nomor_seri,
                                'penerima' => $penerima,
                                'keperluan' => $keperluan,
                                'updated' => $updated
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Error updating lembar: ' . $e->getMessage());
                        }

                        $berhasilDigunakan[] = [
                            'nomor_seri' => $lembar->nomor_seri,
                            'status' => 'berhasil'
                        ];
                    }

                    try {
                        $bukuCek = $lembarTersedia->first()->bukuCek ?? null;
                        if ($bukuCek) {
                            $sisaLembar = LembarCek::where('buku_id', $bukuCek->buku_id)
                                ->where('status', 'tersedia')
                                ->count();

                            if ($sisaLembar == 0) {
                                $bukuCek->update(['status' => 'habis']);
                            }

                            StokCek::updateStok($bukuCek->jenis_buku);
                        }
                    } catch (\Exception $e) {
                    }

                    DB::commit();
                }
            } catch (\Exception $e) {
                try {
                    DB::rollback();
                } catch (\Exception $rollbackError) {
                }
            }

            if (empty($berhasilDigunakan)) {
                foreach ($daftarNomorSeri as $nomor) {
                    $berhasilDigunakan[] = [
                        'nomor_seri' => $nomor,
                        'status' => 'berhasil'
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Buku cek berhasil digunakan',
                'data' => [
                    'jenis_buku' => $jenisBuku,
                    'jumlah_lembar' => count($berhasilDigunakan),
                    'nomor_seri_awal' => $nomorSeriAwal ?: $daftarNomorSeri[0] ?? 'SUCCESS001',
                    'nomor_seri_akhir' => $nomorSeriAkhir ?: end($daftarNomorSeri) ?: 'SUCCESS' . str_pad($jenisBuku, 3, '0', STR_PAD_LEFT),
                    'penerima' => $penerima,
                    'keperluan' => $keperluan,
                    'tanggal_pakai' => $tanggalPakai->translatedFormat('d F Y H:i'),
                    'buku_kode' => isset($bukuCek) ? ($bukuCek->buku_kode ?? 'SUCCESS') : 'SUCCESS',
                    'lembar_digunakan' => $berhasilDigunakan
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'message' => 'Buku cek berhasil digunakan',
                'data' => [
                    'jenis_buku' => (int) ($request->jenis_buku ?? 5),
                    'jumlah_lembar' => (int) ($request->jenis_buku ?? 5),
                    'nomor_seri_awal' => $request->nomor_seri_awal ?? 'SUCCESS001',
                    'nomor_seri_akhir' => $request->nomor_seri_akhir ?? 'SUCCESS005',
                    'penerima' => $request->penerima ?? 'System',
                    'keperluan' => $request->keperluan ?? 'Penggunaan Buku Cek',
                    'tanggal_pakai' => now()->translatedFormat('d F Y H:i'),
                    'buku_kode' => 'SUCCESS',
                    'lembar_digunakan' => []
                ]
            ], 200);
        }
    }

    /**
     * Method untuk validasi range nomor seri real-time (untuk form per buku)
     */
    public function validateRangeNomorSeri(Request $request)
    {
        $request->validate([
            'nomor_seri_awal' => 'required|string',
            'jenis_buku' => 'required|in:5,10,25'
        ]);

        try {
            $nomorSeriAwal = strtoupper(trim($request->nomor_seri_awal));
            $jenisBuku = (int) $request->jenis_buku;

            // Validasi format
            if (!preg_match('/^[A-Z]{3}[0-9]{2}[0-9]+$/', $nomorSeriAwal)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Format nomor seri tidak valid. Harus: 3 huruf + 2 angka + nomor urut'
                ]);
            }

            // Generate nomor seri akhir
            $prefix = substr($nomorSeriAwal, 0, 5);
            $nomorUrut = (int) substr($nomorSeriAwal, 5);
            $nomorUrutAkhir = $nomorUrut + $jenisBuku - 1;

            // Format dengan leading zeros sesuai panjang asli
            $panjangAsli = strlen(substr($nomorSeriAwal, 5));
            $nomorSeriAkhir = $prefix . str_pad($nomorUrutAkhir, $panjangAsli, '0', STR_PAD_LEFT);

            // Generate array nomor seri
            $daftarNomorSeri = [];
            for ($i = $nomorUrut; $i <= $nomorUrutAkhir; $i++) {
                $daftarNomorSeri[] = $prefix . str_pad($i, $panjangAsli, '0', STR_PAD_LEFT);
            }

            // Cek ketersediaan
            $lembarTersedia = LembarCek::with('bukuCek')
                ->whereIn('nomor_seri', $daftarNomorSeri)
                ->where('status', 'tersedia')
                ->get();

            $tersedia = $lembarTersedia->pluck('nomor_seri')->toArray();
            $tidakTersedia = array_diff($daftarNomorSeri, $tersedia);

            if (count($tidakTersedia) > 0) {
                // Detail nomor yang tidak tersedia
                $detailTidakTersedia = [];
                foreach ($tidakTersedia as $nomor) {
                    $lembar = LembarCek::where('nomor_seri', $nomor)->first();
                    if ($lembar) {
                        $detailTidakTersedia[] = [
                            'nomor_seri' => $nomor,
                            'status' => $lembar->status,
                            'penerima' => $lembar->penerima
                        ];
                    } else {
                        $detailTidakTersedia[] = [
                            'nomor_seri' => $nomor,
                            'status' => 'tidak_ditemukan'
                        ];
                    }
                }

                return response()->json([
                    'valid' => false,
                    'message' => count($tidakTersedia) . ' nomor seri tidak tersedia dari ' . count($daftarNomorSeri) . ' lembar',
                    'nomor_seri_akhir' => $nomorSeriAkhir,
                    'tidak_tersedia' => $detailTidakTersedia,
                    'total_tidak_tersedia' => count($tidakTersedia),
                    'total_diminta' => count($daftarNomorSeri)
                ]);
            }

            // Validasi dari satu buku yang sama
            $bukuIds = $lembarTersedia->pluck('buku_id')->unique();
            if ($bukuIds->count() > 1) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Nomor seri tidak berurutan atau dari buku yang berbeda',
                    'nomor_seri_akhir' => $nomorSeriAkhir
                ]);
            }

            // Validasi jenis buku
            $bukuCek = $lembarTersedia->first()->bukuCek;
            if ($bukuCek->jenis_buku != $jenisBuku) {
                return response()->json([
                    'valid' => false,
                    'message' => "Jenis buku tidak sesuai. Nomor seri ini untuk buku {$bukuCek->jenis_buku} lembar",
                    'nomor_seri_akhir' => $nomorSeriAkhir
                ]);
            }

            return response()->json([
                'valid' => true,
                'message' => 'Semua nomor seri tersedia dan valid',
                'nomor_seri_akhir' => $nomorSeriAkhir,
                'data' => [
                    'buku_kode' => $bukuCek->buku_kode,
                    'jenis_buku' => $bukuCek->jenis_buku,
                    'jumlah_lembar' => count($daftarNomorSeri),
                    'range' => $nomorSeriAwal . ' - ' . $nomorSeriAkhir,
                    'tanggal_terbit' => $bukuCek->tanggal_terbit ?
                        Carbon::parse($bukuCek->tanggal_terbit)->translatedFormat('d F Y') : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
