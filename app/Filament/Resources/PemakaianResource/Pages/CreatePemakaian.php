<?php

namespace App\Filament\Resources\PemakaianResource\Pages;

use App\Filament\Resources\PemakaianResource;
use App\Models\Pemakaian;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification; 
use App\Services\JurnalOtomatisService;  

class CreatePemakaian extends CreateRecord
{
    protected static string $resource = PemakaianResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // ambil record terakhir yang sudah disimpan manual
        return Pemakaian::latest()->first();
    }

     protected function afterCreate(): void
    {
        $pemakaian = $this->record;
        
        if ($pemakaian) {
            $this->buatJurnalOtomatis($pemakaian);
        }
    }

    protected function buatJurnalOtomatis($pemakaian)
    {
        try {
            $jurnalService = app(JurnalOtomatisService::class);
            $jurnal = $jurnalService->dariPemakaian($pemakaian);

            Notification::make()
                ->success()
                ->title('Jurnal otomatis dibuat')
                ->body("Jurnal {$jurnal->no_jurnal} untuk pemakaian {$pemakaian->nomer_pemakaian}")
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Gagal membuat jurnal')
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
