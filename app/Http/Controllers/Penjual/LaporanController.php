<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\ProdukViewLog;
use App\Models\Ulasan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Tampilkan Rekapitulasi Mingguan Laporan Promosi Penjual.
     */
    public function index(Request $request): View
    {
        $penjual = auth()->user()->penjual;
        if (!$penjual) {
            abort(403, 'Akses khusus penjual.');
        }

        $penjualId = $penjual->id;

        // ── 1. Penentuan Periode Minggu ────────────────────────────────────────
        $weekOffset = (int) $request->query('week_offset', 0);
        
        // Mencegah navigasi ke minggu depan jika offset > 0
        if ($weekOffset > 0) {
            $weekOffset = 0;
        }

        Carbon::setLocale('id');

        $selectedStart = Carbon::now()->addWeeks($weekOffset)->startOfWeek(Carbon::MONDAY)->startOfDay();
        $selectedEnd   = Carbon::now()->addWeeks($weekOffset)->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $prevStart = (clone $selectedStart)->subWeek()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $prevEnd   = (clone $selectedStart)->subWeek()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $canGoNext = (clone $selectedStart)->addWeek()->startOfWeek(Carbon::MONDAY)->lte(Carbon::now());

        // Dynamic date label
        $periodLabel = $selectedStart->translatedFormat('l, d F Y') . ' – ' . $selectedEnd->translatedFormat('l, d F Y');

        // IDs produk milik penjual
        $sellerProduk = Produk::where('penjual_id', $penjualId)->get();
        $sellerProdukIds = $sellerProduk->pluck('id')->toArray();

        // ── 2. Stat Cards & Comparisons ────────────────────────────────────────
        $totalProdukCount = $sellerProduk->count();

        // Views minggu ini & lalu
        $viewsThisWeek = ProdukViewLog::whereIn('produk_id', $sellerProdukIds)
            ->where('type', 'view')
            ->whereBetween('created_at', [$selectedStart, $selectedEnd])
            ->count();

        $viewsPrevWeek = ProdukViewLog::whereIn('produk_id', $sellerProdukIds)
            ->where('type', 'view')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();

        // Fallback jika log per-timestamp belum ada sama sekali tetapi total counter views > 0
        if ($viewsThisWeek === 0 && $viewsPrevWeek === 0 && $sellerProduk->sum('views') > 0 && $weekOffset === 0) {
            $viewsThisWeek = $sellerProduk->sum('views');
        }

        $viewsComp = $this->calculateComparison($viewsThisWeek, $viewsPrevWeek);

        // WA Clicks minggu ini & lalu
        $waThisWeek = ProdukViewLog::whereIn('produk_id', $sellerProdukIds)
            ->where('type', 'whatsapp')
            ->whereBetween('created_at', [$selectedStart, $selectedEnd])
            ->count();

        $waPrevWeek = ProdukViewLog::whereIn('produk_id', $sellerProdukIds)
            ->where('type', 'whatsapp')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();

        $waComp = $this->calculateComparison($waThisWeek, $waPrevWeek);

        // Ulasan Baru minggu ini & lalu
        $ulasanThisWeek = Ulasan::whereIn('produk_id', $sellerProdukIds)
            ->publicVisible()
            ->whereBetween('created_at', [$selectedStart, $selectedEnd])
            ->count();

        $ulasanPrevWeek = Ulasan::whereIn('produk_id', $sellerProdukIds)
            ->publicVisible()
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();

        $ulasanComp = $this->calculateComparison($ulasanThisWeek, $ulasanPrevWeek);

        // ── 3. Grafik Performa 7 Hari ──────────────────────────────────────────
        $chartLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $chartViewsData = [];
        $chartWaData = [];

        for ($i = 0; $i < 7; $i++) {
            $dayStart = (clone $selectedStart)->addDays($i)->startOfDay();
            $dayEnd   = (clone $selectedStart)->addDays($i)->endOfDay();

            $vCount = ProdukViewLog::whereIn('produk_id', $sellerProdukIds)
                ->where('type', 'view')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();

            $wCount = ProdukViewLog::whereIn('produk_id', $sellerProdukIds)
                ->where('type', 'whatsapp')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();

            $chartViewsData[] = $vCount;
            $chartWaData[]    = $wCount;
        }

        // ── 4. Produk Terpopuler (Top 5) ──────────────────────────────────────
        $produkTerpopuler = Produk::with(['kategori'])
            ->where('penjual_id', $penjualId)
            ->withCount(['viewsLogs as period_views' => function ($q) use ($selectedStart, $selectedEnd) {
                $q->where('type', 'view')->whereBetween('created_at', [$selectedStart, $selectedEnd]);
            }])
            ->withCount(['ulasan as period_reviews' => function ($q) use ($selectedStart, $selectedEnd) {
                $q->publicVisible()->whereBetween('created_at', [$selectedStart, $selectedEnd]);
            }])
            ->orderByDesc('period_views')
            ->orderByDesc('views')
            ->limit(5)
            ->get();

        // ── 5. Performa Setiap Produk ──────────────────────────────────────────
        $performaProduk = Produk::with(['kategori'])
            ->where('penjual_id', $penjualId)
            ->withCount(['viewsLogs as period_views' => function ($q) use ($selectedStart, $selectedEnd) {
                $q->where('type', 'view')->whereBetween('created_at', [$selectedStart, $selectedEnd]);
            }])
            ->withCount(['viewsLogs as prev_views' => function ($q) use ($prevStart, $prevEnd) {
                $q->where('type', 'view')->whereBetween('created_at', [$prevStart, $prevEnd]);
            }])
            ->withCount(['ulasan as new_reviews' => function ($q) use ($selectedStart, $selectedEnd) {
                $q->publicVisible()->whereBetween('created_at', [$selectedStart, $selectedEnd]);
            }])
            ->get()
            ->map(function ($p) use ($weekOffset) {
                $cur = (int) $p->period_views;
                // Fallback jika belum ada log tetapi total views > 0
                if ($cur === 0 && $p->views > 0 && $weekOffset === 0) {
                    $cur = $p->views;
                }
                $prev = (int) $p->prev_views;
                $diff = $cur - $prev;

                $trend = 'same';
                if ($diff > 0) {
                    $trend = 'up';
                } elseif ($diff < 0) {
                    $trend = 'down';
                }

                $p->cur_views = $cur;
                $p->views_diff = $diff;
                $p->trend = $trend;

                return $p;
            });

        // ── 6. Ulasan Mingguan ────────────────────────────────────────────────
        $ulasanMingguan = Ulasan::with('produk')
            ->whereIn('produk_id', $sellerProdukIds)
            ->publicVisible()
            ->whereBetween('created_at', [$selectedStart, $selectedEnd])
            ->latest()
            ->take(10)
            ->get();

        // ── 7. Insight Performa Faktual ───────────────────────────────────────
        $insights = [];
        $topOne = $produkTerpopuler->first();

        if ($topOne && ($topOne->period_views > 0 || $topOne->views > 0)) {
            $viewsCount = $topOne->period_views > 0 ? $topOne->period_views : $topOne->views;
            $insights[] = "Produk '{$topOne->nama}' menjadi produk paling banyak dilihat minggu ini dengan {$viewsCount} views.";
        }

        if ($viewsComp['type'] === 'up' && $viewsComp['percent'] > 0) {
            $insights[] = "Jumlah kunjungan produk meningkat {$viewsComp['percent']}% dibanding minggu sebelumnya.";
        } elseif ($viewsComp['type'] === 'down' && $viewsComp['percent'] < 0) {
            $insights[] = "Jumlah kunjungan produk turun " . abs($viewsComp['percent']) . "% dibanding minggu sebelumnya.";
        }

        if ($waThisWeek > 0) {
            $insights[] = "Toko Anda mendapatkan {$waThisWeek} klik potensi pembeli via WhatsApp minggu ini.";
        }

        if (empty($insights)) {
            $insights[] = "Belum cukup data untuk membuat insight performa minggu ini.";
        }

        $produk = $produkTerpopuler;

        return view('penjual.laporan.index', compact(
            'penjual',
            'weekOffset',
            'periodLabel',
            'canGoNext',
            'totalProdukCount',
            'viewsThisWeek',
            'viewsComp',
            'waThisWeek',
            'waComp',
            'ulasanThisWeek',
            'ulasanComp',
            'chartLabels',
            'chartViewsData',
            'chartWaData',
            'produkTerpopuler',
            'produk',
            'performaProduk',
            'ulasanMingguan',
            'insights'
        ));
    }

    /**
     * Hitung perbandingan persentase / selisih dari periode sebelumnya.
     *
     * @param int $current
     * @param int $previous
     * @return array{text: string, type: string, diff: int, percent: float}
     */
    protected function calculateComparison(int $current, int $previous): array
    {
        if ($previous == 0) {
            if ($current > 0) {
                return [
                    'text' => "+{$current} dibanding minggu lalu",
                    'type' => 'up',
                    'diff' => $current,
                    'percent' => 100.0,
                ];
            }
            return [
                'text' => 'Data sebelumnya belum tersedia',
                'type' => 'neutral',
                'diff' => 0,
                'percent' => 0.0,
            ];
        }

        $diff = $current - $previous;
        $percent = round(($diff / $previous) * 100, 1);

        if ($diff > 0) {
            return [
                'text' => "↑ {$percent}% (+{$diff}) dari minggu lalu",
                'type' => 'up',
                'diff' => $diff,
                'percent' => $percent,
            ];
        } elseif ($diff < 0) {
            return [
                'text' => "↓ " . abs($percent) . "% ({$diff}) dari minggu lalu",
                'type' => 'down',
                'diff' => $diff,
                'percent' => $percent,
            ];
        }

        return [
            'text' => "— Tetap dari minggu lalu",
            'type' => 'neutral',
            'diff' => 0,
            'percent' => 0.0,
        ];
    }
}
