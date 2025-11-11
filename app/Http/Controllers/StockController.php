<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class StockController extends Controller
{
    private $stocksData = [
        [
            "symbol" => "ANTM.JK",
            "name" => "Aneka Tambang Tbk.",
            "sector" => "Perbankan",
            "marketCap" => "Rp 670 T",
            "description" => "Perusahaan pertambangan yang bergerak di bidang eksplorasi, penambangan, pengolahan, dan pemasaran bijih nikel, feronikel, emas, perak, bauksit, dan batubara."
        ],
        [
            "symbol" => "BBNI.JK",
            "name" => "Bank Negara Indonesia Tbk.",
            "sector" => "Perbankan",
            "marketCap" => "Rp 670 T",
            "description" => "Bank BUMN terbesar kedua di Indonesia yang menyediakan layanan perbankan lengkap untuk segmen korporat, komersial, UKM, dan konsumer."
        ],
        [
            "symbol" => "BBRI.JK",
            "name" => "Bank Rakyat Indonesia Tbk.",
            "sector" => "Perbankan",
            "marketCap" => "Rp 670 T",
            "description" => "Bank terbesar di Indonesia berdasarkan aset dan kredit yang memiliki fokus kuat pada segmen mikro, kecil, dan menengah."
        ],
        [
            "symbol" => "BMRI.JK",
            "name" => "Bank Mandiri Tbk.",
            "sector" => "Perbankan",
            "marketCap" => "Rp 670 T",
            "description" => "Bank terbesar di Indonesia berdasarkan aset yang menyediakan layanan perbankan universal dengan jaringan cabang yang luas."
        ],
        [
            "symbol" => "TLKM.JK",
            "name" => "Telkom Indonesia Tbk.",
            "sector" => "Telekomunikasi",
            "marketCap" => "Rp 390 T",
            "description" => "Penyedia layanan telekomunikasi dan teknologi informasi terintegrasi terbesar di Indonesia dengan layanan fixed line, mobile, dan digital."
        ],
    ];

    public function index()
    {
        $cacheKey = 'stocks_quote';
        $symbols = "ANTM.JK,BBNI.JK,BBRI.JK,BMRI.JK,TLKM.JK";

        try {
            $response = Http::timeout(60)->get("http://127.0.0.1:5000/quote", [
                "symbols" => $symbols
            ]);

            if ($response->successful()) {
                $stocksData = $response->json();

                $lastUpdated = now()->toDateTimeString();

                $dataToCache = [
                    'stocks' => $stocksData,
                    'last_updated' => $lastUpdated,
                ];

                Cache::put($cacheKey, $dataToCache, now()->addMinutes(60));
            } else {
                $dataToCache = Cache::get($cacheKey, ['stocks' => [], 'last_updated' => null]);
            }
        } catch (\Exception $e) {
            $dataToCache = Cache::get($cacheKey, ['stocks' => [], 'last_updated' => null]);
        }

        $stocksData = $dataToCache['stocks'];
        $lastUpdated = $dataToCache['last_updated'];

        $stockNames = [
            "ANTM.JK" => "Aneka Tambang Tbk.",
            "BBNI.JK" => "Bank Negara Indonesia Tbk.",
            "BBRI.JK" => "Bank Rakyat Indonesia Tbk.",
            "BMRI.JK" => "Bank Mandiri Tbk.",
            "TLKM.JK" => "Telkom Indonesia Tbk."
        ];

        foreach ($stocksData as &$stock) {
            if (isset($stockNames[$stock['symbol']])) {
                $stock['name'] = $stockNames[$stock['symbol']];
            }
        }

        return view('index', compact('stocksData', 'lastUpdated'));
    }


    public function show($symbol)
    {
        $stock = collect($this->stocksData)->firstWhere('symbol', $symbol);

        if (!$stock) {
            abort(404, "Stock not found");
        }

        $cacheKey = 'stock_history_' . $symbol;

        try {
            $response = Http::timeout(60)->get("http://127.0.0.1:5000/history", [
                "symbol" => $symbol
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $data['last_updated'] = now()->toDateTimeString();

                Cache::put($cacheKey, $data, now()->addMinutes(60));
            } else {
                $data = Cache::get($cacheKey, []);
            }
        } catch (\Exception $e) {
            $data = Cache::get($cacheKey, []);
        }

        $stock['price'] = $data['price'] ?? null;
        $stock['change'] = $data['change'] ?? null;
        $stock['changePercent'] = $data['changePercent'] ?? null;
        $stock['recommendation'] = $data['recommendation'] ?? null;
        $stock['history'] = $data['history'] ?? [];
        $stock['last_updated'] = $data['last_updated'] ?? null;

        return view('detail', compact('stock'));
    }
}
