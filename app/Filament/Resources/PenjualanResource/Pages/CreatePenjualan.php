<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

// tambahan untuk akses ke model
use App\Models\Penjualan;
use App\Models\PenjualanProduk;
use App\Models\PembayaranPenjualan;
use Illuminate\Support\Facades\DB;

// untuk notifikasi
use Filament\Notifications\Notification;

class CreatePenjualan extends CreateRecord
{
    protected static string $resource = PenjualanResource::class;

    /**
     * Memastikan data status masuk ke database 
     * sebelum proses INSERT dilakukan.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan status diisi sebelum insert ke database
        $data['status'] = $data['status'] ?? 'pesan';
        
        return $data;
    }

    /**
     * Menambahkan tombol kustom "Bayar" di bagian bawah form.
     */
    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('bayar')
                ->label('Bayar')
                ->color('success')
                ->action(fn () => $this->simpanPembayaran())
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Pembayaran')
                ->modalDescription('Apakah Anda yakin ingin menyimpan pembayaran ini?')
                ->modalButton('Ya, Bayar'),
        ];
    }

    /**
     * Logika simpan: Membuat data penjualan lalu membuat data pembayaran.
     */
    protected function simpanPembayaran()
    {
        // Ambil data dari form sebagai cadangan jika properti model masih null
        $dataForm = $this->form->getState();
        $totalDariForm = $dataForm['total_dibayar'] ?? 0;

        // 1. Simpan data penjualan ke database
        $this->create();

        $penjualan = $this->record;

        if ($penjualan) {
            // 2. Simpan ke tabel pembayaran menggunakan data penjualan yang baru lahir
            \App\Models\PembayaranPenjualan::create([
                'penjualan_id'     => $penjualan->id,
                'tgl_bayar'        => now(),
                'jenis_pembayaran' => 'tunai',
                'waktu_transaksi'  => now(),
                'gross_amount'     => $penjualan->total_dibayar ?? $totalDariForm,
                'order_id'         => $penjualan->no_faktur,
            ]);

            // 3. Update status penjualan menjadi "bayar"
            $penjualan->update(['status' => 'bayar']);

            Notification::make()
                ->title('Pembayaran Berhasil!')
                ->success()
                ->send();
        }
    }
}