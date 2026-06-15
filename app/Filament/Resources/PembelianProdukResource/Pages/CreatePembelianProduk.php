<?php

namespace App\Filament\Resources\PembelianProdukResource\Pages;

use App\Filament\Resources\PembelianProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Services\JurnalOtomatisService;

class CreatePembelianProduk extends CreateRecord
{
    protected static string $resource = PembelianProdukResource::class;


    /**
     * Langkah 2: Menambah stok produk setelah transaksi berhasil dibuat.
     * Karena menggunakan Repeater, kita harus melooping semua barang yang dibeli.
     */
    protected function afterCreate(): void
    {
        $pembelian = $this->record;

        // Ambil semua detail barang dari relasi detailPembelian
        // Pastikan relasi 'detailPembelian' sudah didefinisikan di Model PembelianProduk
        $details = $pembelian->detailPembelian;

        if ($details) {
            foreach ($details as $detail) {
                // Cari produk berdasarkan produk_id yang ada di detail
                $produk = \App\Models\Produk::find($detail->produk_id);

                if ($produk) {
                    // Tambah stok produk sesuai dengan quantity yang dibeli
                    $produk->increment('stok', (int) $detail->qty);
                }
            }
        }

        $this->buatJurnalOtomatis($pembelian);
    }

    protected function buatJurnalOtomatis($pembelian)
    {
        try {
            $jurnalService = app(JurnalOtomatisService::class);
            $jurnal = $jurnalService->dariPembelian($pembelian);

            Notification::make()
                ->success()
                ->title('Jurnal otomatis dibuat')
                ->body("Jurnal {$jurnal->no_jurnal} untuk pembelian faktur {$pembelian->no_faktur}")
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Gagal membuat jurnal')
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Opsional: Mengarahkan halaman kembali ke daftar index setelah sukses
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}