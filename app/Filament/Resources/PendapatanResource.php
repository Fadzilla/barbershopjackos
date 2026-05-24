<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendapatanResource\Pages;
use App\Filament\Resources\PendapatanResource\RelationManagers;
use App\Models\Pendapatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// tambahan
use Filament\Forms\Components\Wizard; 
use Filament\Forms\Components\TextInput; 
use Filament\Forms\Components\DateTimePicker; 
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select; 
use Filament\Forms\Components\Repeater; 
use Filament\Tables\Columns\TextColumn; 
use Filament\Forms\Components\Placeholder; 
use Filament\Forms\Get; 
use Filament\Forms\Set; 
use Filament\Forms\Components\Hidden; 
use Filament\Tables\Filters\SelectFilter; 

// model
use App\Models\Pelanggan;
use App\Models\Pegawai;
use App\Models\Paket;
use App\Models\Pembayaran;
use App\Models\PendapatanJasa;

// DB
use Illuminate\Support\Facades\DB;

// tambahan untuk tombol unduh pdf
use Filament\Tables\Actions\Action; 
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Facades\Storage;

use Midtrans\Snap;
use Midtrans\Config;

class PendapatanResource extends Resource
{
    protected static ?string $model = Pendapatan::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Pendapatan';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Pesanan')
                        ->schema([
                            Forms\Components\Section::make('Faktur')
                                ->icon('heroicon-m-document-duplicate')
                                ->schema([ 
                                    TextInput::make('no_faktur')
                                        ->default(fn () => Pendapatan::getKodeFaktur())
                                        ->label('Nomor Faktur')
                                        ->required()
                                        ->readonly(),
                                    DateTimePicker::make('tgl')->default(now()),
                                    Select::make('pelanggan_id')
                                        ->label('Pelanggan')
                                        ->options(Pelanggan::pluck('nama_pelanggan', 'id')->toArray())
                                        ->required()
                                        ->placeholder('Pilih Pelanggan'),
                                    Select::make('pegawai_id')
                                        ->label('Pegawai')
                                        ->options(Pegawai::pluck('nama_pegawai', 'id')->toArray())
                                        ->required()
                                        ->placeholder('Pilih Pegawai'),
                                    TextInput::make('total')->default(0)->hidden(),
                                    TextInput::make('status')->default('pesan')->hidden(),
                                ])
                                ->collapsible()
                                ->columns(3),
                        ]),
                    Wizard\Step::make('Pilih Paket')
                    ->schema([
                            Repeater::make('items')
                            ->relationship('pendapatanJasa')
                            ->schema([
                                Select::make('pakets_id')
                                        ->label('Paket')
                                        ->options(Paket::pluck('deskripsi', 'id')->toArray())
                                        ->required()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->reactive()
                                        ->placeholder('Pilih Paket')
                                        ->afterStateUpdated(function ($state, $set) {
                                            $paket = Paket::find($state);
                                            $set('harga', $paket ? $paket->harga : 0);
                                        })
                                        ->searchable(),
                                TextInput::make('harga')
                                    ->label('Harga Paket')
                                    ->numeric()
                                    ->readonly()
                                    ->dehydrated(),
                                TextInput::make('jml')
                                    ->label('Jumlah')
                                    ->default(1)
                                    ->reactive()
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $total = collect($get('pendapatan_jasa'))
                                        ->sum(fn ($item) => ($item['harga_paket'] ?? 0) * ($item['jml'] ?? 0));
                                        $set('total', $total);
                                    }),
                                DatePicker::make('tgl')
                                ->default(today())
                                ->required(),
                            ])
                            ->columns(['md' => 4])
                            ->addable()
                            ->deletable()
                            ->reorderable()
                            ->createItemButtonLabel('Tambah Item')
                            ->minItems(1)
                            ->required(),

                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('Simpan Sementara')
                                    ->action(function ($get) {
                                        $pendapatan = Pendapatan::updateOrCreate(
                                            ['no_faktur' => $get('no_faktur')],
                                            [
                                                'tgl' => $get('tgl'),
                                                'pelanggan_id' => $get('pelanggan_id'),
                                                'pegawai_id' => $get('pegawai_id'),
                                                'status' => 'pesan',
                                                'total' => 0
                                            ]
                                        );

                                        foreach ($get('items') as $item) {
                                            PendapatanJasa::updateOrCreate(
                                                [
                                                    'pendapatan_id' => $pendapatan->id,
                                                    'paket_id' => $item['pakets_id']
                                                ],
                                                [
                                                    'harga_paket' => $item['harga'],
                                                    'jml' => $item['jml'],
                                                    'tgl' => $item['tgl'],
                                                ]
                                            );
                                        }

                                        $total = PendapatanJasa::where('pendapatan_id', $pendapatan->id)
                                            ->sum(DB::raw('harga_paket * jml'));

                                        $pendapatan->update(['total' => $total]);
                                    })
                                    ->label('Proses')
                                    ->color('primary'),
                            ])    
                    ]),
                    
                    Wizard\Step::make('Pembayaran')
                    ->schema([
                        Placeholder::make('Tabel Pembayaran')
                            ->content(fn (Get $get) => view('filament.components.pendapatan-table', [
                                'pembayarans' => Pendapatan::where('no_faktur', $get('no_faktur'))->get()
                            ])),

                        Forms\Components\Actions::make([
                            
                            // --- TOMBOL TUNAI ---
                            Forms\Components\Actions\Action::make('bayar_tunai')
                                ->label('Bayar Tunai')
                                ->icon('heroicon-m-banknotes')
                                ->color('success')
                                ->requiresConfirmation()
                                ->action(function (Get $get) {
                                    $pendapatan = Pendapatan::where('no_faktur', $get('no_faktur'))->first();
                                    
                                    if ($pendapatan) {
                                        $pendapatan->update(['status' => 'bayar']);
                                        
                                        \App\Models\Pembayaran::updateOrCreate(
                                            ['order_id' => $pendapatan->no_faktur],
                                            [
                                                'pendapatan_id'    => $pendapatan->id,
                                                'order_id'         => $pendapatan->no_faktur,
                                                'jenis_pembayaran' => 'tunai',
                                                'tgl_bayar'        => now(),
                                                'transaction_time' => now(),
                                                'gross_amount'     => $pendapatan->total,
                                            ]
                                        );

                                        \Filament\Notifications\Notification::make()
                                            ->title('Pembayaran Tunai Berhasil!')
                                            ->success()
                                            ->send();
                                    }
                                }),

                            // --- TOMBOL MIDTRANS ---
                            Forms\Components\Actions\Action::make('bayar_midtrans')
                                ->label('Bayar Non-Tunai (Midtrans)')
                                ->icon('heroicon-m-credit-card')
                                ->color('primary')
                                ->action(function (Get $get) {
                                    $pendapatan = Pendapatan::where('no_faktur', $get('no_faktur'))->first();
                                    
                                    if ($pendapatan) {
                                        // Update status di tabel pendapatan
                                        $pendapatan->update(['status' => 'bayar']);
                                        
                                        // Catat ke tabel pembayaran
                                        \App\Models\Pembayaran::updateOrCreate(
                                            ['order_id' => $pendapatan->no_faktur],
                                            [
                                                'pendapatan_id'    => $pendapatan->id, 
                                                'order_id'         => $pendapatan->no_faktur,
                                                'tgl_bayar'        => now(),
                                                'jenis_pembayaran' => 'non tunai',
                                                'transaction_time' => now(),
                                                'gross_amount'     => $pendapatan->total,
                                            ]
                                        );

                                        // Konfigurasi Midtrans
                                        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                                        \Midtrans\Config::$isProduction = false;
                                        \Midtrans\Config::$isSanitized = true;
                                        \Midtrans\Config::$is3ds = true;

                                        $params = [
                                            'transaction_details' => [
                                                'order_id' => $pendapatan->no_faktur,
                                                'gross_amount' => (int) $pendapatan->total,
                                            ],
                                            'customer_details' => [
                                                'first_name' => $pendapatan->pelanggan->nama_pelanggan ?? 'Customer',
                                            ],
                                        ];

                                        try {
                                            $snapToken = \Midtrans\Snap::getSnapToken($params);
                                            return redirect()->route('midtrans.pembayaran', ['token' => $snapToken]);
                                        } catch (\Exception $e) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Gagal: ' . $e->getMessage())
                                                ->danger()
                                                ->send();
                                        }
                                    }
                                }),
                        ])->columnSpanFull()->alignCenter(),
                    ]),
                ])->columnSpan(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_faktur')->label('No Faktur')->searchable(),
                TextColumn::make('pelanggan.nama_pelanggan')->label('Nama Pelanggan')->sortable()->searchable(),
                TextColumn::make('pegawai.nama_pegawai')->label('Nama Pegawai')->sortable()->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bayar' => 'success',
                        'pesan' => 'warning',
                    }),
                TextColumn::make('total')
                    ->formatStateUsing(fn ($state) => "Rp " . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->alignment('end'),
                TextColumn::make('created_at')->label('Tanggal')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'pesan' => 'Pemesanan',
                        'bayar' => 'Pembayaran',
                    ])
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Action::make('downloadPdf')
                ->label('Unduh PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $pendapatan = Pendapatan::all();
                    $pdf = Pdf::loadView('pdf.pendapatan', ['pendapatan' => $pendapatan]);
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'pendapatan-list.pdf'
                    );
                })
            ]);

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendapatans::route('/'),
            'create' => Pages\CreatePendapatan::route('/create'),
            'edit' => Pages\EditPendapatan::route('/{record}/edit'),
        ];
    }
}