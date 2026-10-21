<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rekap Kinerja Penjual — SiswaMart</title>
    <style>
        @page {
            margin: 24px 28px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #241A14;
            font-size: 9px;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .brand-title {
            font-size: 18px;
            font-weight: bold;
            color: #FF7A00;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .brand-title span {
            color: #241A14;
        }
        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #241A14;
            margin-top: 2px;
            text-transform: uppercase;
        }
        .period-info {
            font-size: 9px;
            color: #64748B;
            margin-top: 2px;
        }
        .meta-box {
            text-align: right;
            font-size: 8.5px;
            color: #64748B;
        }
        .divider {
            border-bottom: 2px solid #FF7A00;
            margin: 6px 0 12px 0;
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px 0;
            margin-bottom: 12px;
        }
        .summary-card {
            background: #FFFDF9;
            border: 1px solid #C9BEB5;
            padding: 6px 8px;
            vertical-align: top;
        }
        .summary-label {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748B;
            margin-bottom: 2px;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #241A14;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #241A14;
            border-left: 3px solid #FF7A00;
            padding-left: 6px;
            margin: 12px 0 6px 0;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 12px;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #C9BEB5;
            padding: 4.5px 6px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
            font-size: 8px;
        }
        .data-table th {
            background: #FFEBCB;
            font-weight: bold;
            font-size: 7.5px;
            text-transform: uppercase;
            color: #241A14;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .font-bold { font-weight: bold; }
        .text-muted { color: #64748B; }
        .text-emerald { color: #15803D; font-weight: bold; }
        .text-rose { color: #B91C1C; font-weight: bold; }

        .two-col-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            table-layout: fixed;
            margin-bottom: 10px;
        }
        .two-col-cell {
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .footer {
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px solid #C9BEB5;
            font-size: 7.5px;
            color: #64748B;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 65%;">
                <div class="brand-title">Siswa<span>Mart</span></div>
                <div class="report-title">Rekap Kinerja Penjual &middot; {{ $periodLabel }}</div>
                <div class="period-info">
                    Periode {{ $start->format('d M Y') }} sampai {{ $end->format('d M Y') }}
                </div>
            </td>
            <td style="width: 35%;" class="meta-box">
                <div><strong>Laporan Agregat Admin</strong></div>
                <div style="margin-top: 2px;">Tipe Periode: {{ ucfirst($period) }}</div>
                <div style="margin-top: 2px;">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- Ringkasan Statistik --}}
    <table class="summary-table">
        <tr>
            <td class="summary-card" style="width: 16.6%;">
                <div class="summary-label">Total Penjual</div>
                <div class="summary-value">{{ number_format($sellerReport['total_sellers']) }}</div>
            </td>
            <td class="summary-card" style="width: 16.6%;">
                <div class="summary-label">Produk Ditambah</div>
                <div class="summary-value text-emerald">{{ number_format($sellerReport['products_added']) }}</div>
            </td>
            <td class="summary-card" style="width: 16.6%;">
                <div class="summary-label">Produk Dihapus</div>
                <div class="summary-value text-rose">{{ number_format($sellerReport['products_deleted']) }}</div>
            </td>
            <td class="summary-card" style="width: 16.6%;">
                <div class="summary-label">Permintaan Edit</div>
                <div class="summary-value">{{ number_format($sellerReport['edits_requested']) }}</div>
            </td>
            <td class="summary-card" style="width: 16.6%;">
                <div class="summary-label">Total Dilihat</div>
                <div class="summary-value" style="color: #1A73E8;">{{ number_format($sellerReport['views']) }}</div>
            </td>
            <td class="summary-card" style="width: 16.6%;">
                <div class="summary-label">Ulasan Baru</div>
                <div class="summary-value" style="color: #B45309;">{{ number_format($sellerReport['reviews']) }}</div>
            </td>
        </tr>
    </table>

    {{-- Tabel Performa Setiap Penjual --}}
    <div class="section-title">Performa Setiap Penjual (Diurutkan berdasarkan kunjungan)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 25%;">Penjual / Toko</th>
                <th style="width: 13%;" class="text-center">Produk Aktif / Total</th>
                <th style="width: 11%;" class="text-right">Dilihat</th>
                <th style="width: 9%;" class="text-center">Ulasan</th>
                <th style="width: 9%;" class="text-center">Rating</th>
                <th style="width: 9%;" class="text-center">Tambah</th>
                <th style="width: 9%;" class="text-center">Hapus</th>
                <th style="width: 11%;" class="text-center">Minta Edit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sellerReport['sellers'] as $index => $seller)
                <tr>
                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $seller->nama_toko }}</strong><br>
                        <span class="text-muted" style="font-size: 7.5px;">{{ $seller->user?->name ?? 'Akun Penjual' }}</span>
                    </td>
                    <td class="text-center">{{ $seller->produk_aktif_count ?? 0 }} / {{ $seller->produk_count }}</td>
                    <td class="text-right font-bold" style="color: #1A73E8;">{{ number_format($seller->period_views) }}</td>
                    <td class="text-center">{{ number_format($seller->period_reviews) }}</td>
                    <td class="text-center font-bold" style="color: #B45309;">
                        {{ $seller->period_rating !== null ? number_format($seller->period_rating, 1) . ' / 5' : '—' }}
                    </td>
                    <td class="text-center text-emerald">+{{ $seller->period_added }}</td>
                    <td class="text-center text-rose">-{{ $seller->period_deleted }}</td>
                    <td class="text-center font-bold">{{ number_format($seller->period_edits_requested) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted" style="padding: 10px;">
                        Belum ada data penjual terdaftar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Dua Kolom: Produk Populer & Aktivitas Terakhir --}}
    <table class="two-col-table">
        <tr>
            {{-- Kolom Kiri: Produk Terpopuler --}}
            <td class="two-col-cell" style="width: 50%;">
                <div class="section-title">Produk Paling Banyak Dilihat</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;" class="text-center">#</th>
                            <th style="width: 50%;">Nama Produk &middot; Penjual</th>
                            <th style="width: 22%;" class="text-right">Dilihat</th>
                            <th style="width: 20%;" class="text-center">Ulasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sellerReport['top_products'] as $idx => $prod)
                            <tr>
                                <td class="text-center font-bold">{{ $idx + 1 }}</td>
                                <td>
                                    <strong>{{ $prod->nama }}</strong><br>
                                    <span class="text-muted" style="font-size: 7.5px;">{{ $prod->penjual?->nama_toko ?? 'Penjual tidak tersedia' }}</span>
                                </td>
                                <td class="text-right font-bold" style="color: #1A73E8;">{{ number_format($prod->period_views) }}</td>
                                <td class="text-center">{{ $prod->period_reviews }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">Belum ada kunjungan produk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>

            {{-- Kolom Kanan: Aktivitas Produk --}}
            <td class="two-col-cell" style="width: 50%;">
                <div class="section-title">Aktivitas Produk Terakhir</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Waktu</th>
                            <th style="width: 32%;">Penjual</th>
                            <th style="width: 48%;">Deskripsi Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sellerReport['events'] as $event)
                            <tr>
                                <td>{{ $event['created_at']->format('d M, H:i') }}</td>
                                <td><strong>{{ $event['seller_name'] }}</strong></td>
                                <td>{{ $event['description'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Belum ada aktivitas pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        Rekapitulasi resmi performa agregat penjual platform SiswaMart. Dihasilkan secara otomatis oleh sistem untuk kebutuhan pengawasan dan evaluasi manajerial.
    </div>
</body>
</html>
