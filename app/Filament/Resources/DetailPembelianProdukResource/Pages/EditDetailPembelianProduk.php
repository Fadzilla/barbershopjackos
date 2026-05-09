<?php

namespace App\Filament\Resources\DetailPembelianProdukResource\Pages;

use App\Filament\Resources\DetailPembelianProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDetailPembelianProduk extends EditRecord
{
    protected static string $resource = DetailPembelianProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
