<?php

namespace App\Filament\Resources\PemakaianResource\Pages;

use App\Filament\Resources\PemakaianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPemakaians extends ListRecords
{
    protected static string $resource = PemakaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
