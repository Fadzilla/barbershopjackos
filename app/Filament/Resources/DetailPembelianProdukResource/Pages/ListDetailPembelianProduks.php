<?php

namespace App\Filament\Resources\DetailPembelianProdukResource\Pages;

use App\Filament\Resources\DetailPembelianProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDetailPembelianProduks extends ListRecords
{
    protected static string $resource = DetailPembelianProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
