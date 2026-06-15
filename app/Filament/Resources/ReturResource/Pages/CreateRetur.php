<?php

namespace App\Filament\Resources\ReturResource\Pages;

use App\Filament\Resources\ReturResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification; 
use App\Services\JurnalOtomatisService;  


class CreateRetur extends CreateRecord
{
    protected static string $resource = ReturResource::class;

    protected function afterCreate(): void
    {
        $pemakaian = $this->record;
        
        if ($pemakaian) {
            $this->buatJurnalOtomatis($pemakaian);
        }
    }

    protected function buatJurnalOtomatis($retur)
    {
        try {
            $jurnalService = app(JurnalOtomatisService::class);
            $jurnal = $jurnalService->dariRetur($retur);

            Notification::make()
                ->success()
                ->title('Jurnal otomatis dibuat')
                ->body("Jurnal {$jurnal->no_jurnal} untuk retur {$retur->kode_retur}")
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
