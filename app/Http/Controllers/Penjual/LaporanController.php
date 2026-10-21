<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Penjual;
use App\Models\Produk;
use App\Models\ProdukViewLog;
use App\Models\Ulasan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
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

        $weekOffset = (int) $request->query('week_offset', 0);
        if ($weekOffset > 0) {
            $weekOffset = 0;
        }

        $data = $this->buildReportData($penjual, $weekOffset);

        return view('penjual.laporan.index', $data);
    }

    /**
     * Unduh PDF Rekapitulasi Mingguan Laporan Promosi Penjual.
     */
    public function pdf(Request $request): Response
    {
        $penjual = auth()->user()->penjual;
        if (!$penjual) {
            abort(403, 'Akses khusus penjual.');
        }

        $weekOffset = (int) $request->query('week_offset', 0);
        if ($weekOffset > 0) {
            $weekOffset = 0;
        }

        $data = $this->buildReportData($penjual, $weekOffset);

        $safeStoreName = Str::slug($penjual->nama_toko ?: 'toko');
        $filename = 'rekap-penjual-' . $safeStoreName . '-' . $data['selectedStart']->format('Ymd') . '.pdf';

        return Pdf::loadView('penjual.laporan.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->download($filename);
    }

    /**
     * Siapkan data agregat laporan performa mingguan penjual.
     */
    protected function buildReportData(Penjual $penjual, int $weekOffset): array
    {
        $penjualId = $penjual->id;

        // ── 1. Penentuan Periode Minggu ────────────────────────────────────────
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

        for ($i = 0; $i < 7; $i++) {
            $dayStart = (clone $selectedStart)->addDays($i)->startOfDay();
            $dayEnd   = (clone $selectedStart)->addDays($i)->endOfDay();

            $vCount = ProdukViewLog::whereIn('produk_id', $sellerProdukIds)
                ->where('type', 'view')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();

            $chartViewsData[] = $vCount;
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
        $produk = $produkTerpopuler;

        return compact(
            'penjual',
            'weekOffset',
            'selectedStart',
            'selectedEnd',
            'periodLabel',
            'canGoNext',
            'totalProdukCount',
            'viewsThisWeek',
            'viewsComp',
            'ulasanThisWeek',
            'ulasanComp',
            'chartLabels',
            'chartViewsData',
            'produkTerpopuler',
            'produk',
            'performaProduk',
            'ulasanMingguan'
        );
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
                'text' => "Naik {$percent}% (+{$diff}) dari minggu lalu",
                'type' => 'up',
                'diff' => $diff,
                'percent' => $percent,
            ];
        } elseif ($diff < 0) {
            return [
                'text' => "Turun " . abs($percent) . "% ({$diff}) dari minggu lalu",
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
