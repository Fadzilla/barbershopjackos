<?php

namespace App\Filament\Resources\PendapatanResource\Pages;

use App\Filament\Resources\PendapatanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

// tambahan untuk akses ke penjualanbarang
use App\Models\Pendapatan;
use App\Models\PendapatanJasa;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

// untuk notifikasi
use Filament\Notifications\Notification;
use App\Services\JurnalOtomatisService;

class CreatePendapatan extends CreateRecord
{
    protected static string $resource = PendapatanResource::class;

    //penanganan kalau status masih kosong 
    protected function beforeCreate(): void
    {
        $this->data['status'] = $this->data['status'] ?? 'pesan';
    }

     // tambahan untuk simpan
    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('bayar')
                ->label('selesai')
                ->color('success')
                ->action(fn () => $this->simpanPembayaran())
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Selesai')
                ->modalDescription('Apakah Anda yakin ingin menyelesaikan transaksi ini?')
                ->modalButton('iya'),
        ];
    }

    // penanganan
    protected function simpanPembayaran()
    {
        // $pendapatan = $this->record; // Ambil data pendapatan yang sedang dibuat
        $pendapatan = $this->record ?? Pendapatan::latest()->first(); // Ambil pendapatan terbaru jika null
        // Simpan ke tabel pembayaran2
        Pembayaran::create([
            'pendapatan_id' => $pendapatan->id,
            'tgl_bayar'    => now(),
            'jenis_pembayaran' => 'tunai',
            'transaction_time' => now(),
            'gross_amount'       => $pendapatan->total, // Sesuaikan dengan field di tabel pembayaran
            'order_id' => $pendapatan->no_faktur,
        ]);

        // Update status pendapatan jadi "dibayar"
        $pendapatan->update(['status' => 'bayar']);

        $this->buatJurnalOtomatis($pendapatan);

        // Notifikasi sukses
        Notification::make()
            ->title('Transaksi Selesai!')
            ->success()
            ->send();

        // 5.  untuk kembali ke halaman index (view) pendapatan
        $this->redirect($this->getResource()::getUrl('index'));
    }
    protected function afterCreate(): void
    {
        $this->buatJurnalOtomatis($this->record);
    }

    protected function buatJurnalOtomatis($record)
{
    if ($record->status === 'bayar') {
        try {
            $jurnalService = app(JurnalOtomatisService::class);  // ← Perbaiki ini
            $jurnal = $jurnalService->dariPendapatan($record);

            Notification::make()
                ->success()
                ->title('Jurnal otomatis dibuat')
                ->body("Jurnal {$jurnal->no_jurnal} telah dibuat")
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Gagal membuat jurnal')
                ->body($e->getMessage())
                ->send();
        }
    }
}
}
