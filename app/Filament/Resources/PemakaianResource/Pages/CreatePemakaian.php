<?php

namespace App\Filament\Resources\PemakaianResource\Pages;

use App\Filament\Resources\PemakaianResource;
use App\Models\Pemakaian;
use Filament\Resources\Pages\CreateRecord;

class CreatePemakaian extends CreateRecord
{
    protected static string $resource = PemakaianResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // ambil record terakhir yang sudah disimpan manual
        return Pemakaian::latest()->first();
    }
}