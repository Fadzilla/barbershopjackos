<?php

namespace App\Filament\Resources\JurnalResource\Pages;

use App\Filament\Resources\JurnalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\JurnalResource\Widgets\JurnalStats;

class Listjurnal extends ListRecords
{
    protected static string $resource = JurnalResource::class;

    // 1. Tombol action jika ada (opsional)
    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(), // hapus / matikan karena canCreate bernilai false
        ];
    }    

    // ✅ INI PERBAIKAN UTAMANYA: Menggunakan getHeaderWidgets bawaan Page Filament
    protected function getHeaderWidgets(): array
    {
        return [
            JurnalStats::class,
        ];
    }

    // 2. Mengatur kolom layout agar rapi menjadi 3 kotak menyamping
    public function getHeaderWidgetsColumns(): int | array
    {
        return 3;
    }
}