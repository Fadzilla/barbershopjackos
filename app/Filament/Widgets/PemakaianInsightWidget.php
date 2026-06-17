<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\PemakaianInsight;
use Illuminate\Support\Str;

class PemakaianInsightWidget extends BaseWidget
{

    protected static bool $isDiscovered = false;
    // Mengatur agar widget membentang penuh 100% dari kiri ke kanan layar [cite: 347]
    protected int | string | array $columnSpan = 'full';

    // Set 1 kolom supaya kotak card otomatis melebar secara horizontal ke samping [cite: 347]
    protected int | string | array $columns = 1;

    protected function getStats(): array
    {
        // Mengambil data analisis tren produk teranyar dari database [cite: 230]
        $insight = PemakaianInsight::latest()->first();

        $subJudul = $insight ? "Berdasarkan Penggunaan Produk: {$insight->nama_produk}" : 'Status Sistem';
        $judulCard = "💈 Barbershop Product Trend & Best Seller Recommendations";
        
        // Ambil data teks mentah hasil analisis AI dari database [cite: 157]
        $teksMentah = $insight?->analisis_ai ?? 'Belum ada analisis tren produk. Silakan buat transaksi pemakaian produk baru di barbershop, lalu klik tombol "Refresh AI Insights" di atas.';

        // Proses membersihkan karakter markdown pengganggu agar UI rapi polos
        $teksBersih = str_replace(['**', '#', '-', '---'], '', $teksMentah);
        
        // Batasi teks agar pas horizontal ke samping dan mudah dimengerti
        $teksAnalisis = Str::limit($teksBersih, 260, '...');

        return [
            // Memanggil komponen Stat bawaan Filament yang aman tanpa file Blade
            Stat::make($judulCard, $subJudul)
                ->description($teksAnalisis)
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning'), // Warna oranye/amber khas AI panel
        ];
    }
}