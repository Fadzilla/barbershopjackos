<?php

namespace App\Filament\Resources\PembelianProdukResource\Pages;

use App\Filament\Resources\PembelianProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePembelianProduk extends CreateRecord
{
    protected static string $resource = PembelianProdukResource::class;

    protected function afterCreate(): void
{
    $pembelian = $this->record;
    $produk = \App\Models\Produk::find($pembelian->produk_id);

    if ($produk) {
        $produk->increment('stok', $pembelian->qty);
    }
}
}
