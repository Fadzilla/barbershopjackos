<?php

namespace App\Filament\Resources\JurnalResource\Pages;

use App\Filament\Resources\JurnalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\JurnalResource\Widgets\JurnalUmum;
class Listjurnal extends ListRecords
{
    protected static string $resource = JurnalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }

    // tambahan
    protected function getHeaderWidgets(): array
    {
        return [
            JurnalUmum::class,
        ];
    }
}