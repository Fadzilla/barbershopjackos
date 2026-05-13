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
use Filament\Forms\Components\Wizard; //untuk menggunakan wizard
use Filament\Forms\Components\TextInput; //untuk penggunaan text input
use Filament\Forms\Components\DateTimePicker; //untuk penggunaan date time picker
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select; //untuk penggunaan select
use Filament\Forms\Components\Repeater; //untuk penggunaan repeater
use Filament\Tables\Columns\TextColumn; //untuk tampilan tabel
use Filament\Forms\Components\Placeholder; //untuk menggunakan text holder
use Filament\Forms\Get; //menggunakan get 
use Filament\Forms\Set; //menggunakan set 
use Filament\Forms\Components\Hidden; //menggunakan hidden field
use Filament\Tables\Filters\SelectFilter; //untuk menambahkan filter

// model
use App\Models\Pelanggan;
use App\Models\Pegawai;
use App\Models\Paket;
use App\Models\Pembayaran;
use App\Models\PendapatanJasa;

// DB
use Illuminate\Support\Facades\DB;
// untuk dapat menggunakan action
//use Filament\Forms\Components\Actions\Action;

// tambahan untuk tombol unduh pdf
use Filament\Tables\Actions\Action; //untuk dapat menggunakan action
use Barryvdh\DomPDF\Facade\Pdf; // Kalau kamu pakai DomPDF
use Illuminate\Support\Facades\Storage;

class PendapatanResource extends Resource
{
    protected static ?string $model = Pendapatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    // merubah nama label menjadi Pendapatan
    protected static ?string $navigationLabel = 'Pendapatan';

    // tambahan buat grup masterdata
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Wizard
                Wizard::make([
                    Wizard\Step::make('Pesanan')
                        ->schema([
                            // section 1
                            Forms\Components\Section::make('Faktur') // Bagian pertama
                                // ->description('Detail Barang')
                                ->icon('heroicon-m-document-duplicate')
                                ->schema([ 
                                    TextInput::make('no_faktur')
                                        ->default(fn () => Pendapatan::getKodeFaktur()) // Ambil default dari method getKodePakets
                                        ->label('Nomor Faktur')
                                        ->required()
                                        ->readonly() // Membuat field menjadi read-only
                                    ,
                                    DateTimePicker::make('tgl')->default(now()) // Nilai default: waktu sekarang
                                    ,
                                    Select::make('pelanggan_id')
                                        ->label('Pelanggan')
                                        ->options(Pelanggan::pluck('nama_pelanggan', 'id')->toArray()) // Mengambil data dari tabel
                                        ->required()
                                        ->placeholder('Pilih Pelanggan') // Placeholder default

                                    ,
                                    Select::make('pegawai_id')
                                        ->label('Pegawai')
                                        ->options(Pegawai::pluck('nama_pegawai', 'id')->toArray()) // Mengambil data dari tabel
                                        ->required()
                                        ->placeholder('Pilih Pegawai') // Placeholder default
                                    ,
                                    TextInput::make('total')
                                        ->default(0) // Nilai default
                                        ->hidden()
                                    ,
                                    TextInput::make('status')
                                        ->default('pesan') // Nilai default status pemesanan adalah pesan/bayar/kirim
                                        ->hidden()
                                    ,
                                ])
                                ->collapsible() // Membuat section dapat di-collapse
                                ->columns(3)
                            ,
                        ]),
                    Wizard\Step::make('Pilih Paket')
                    ->schema([
                        // 
                            // untuk menambahkan repeater
                            Repeater::make('items')
                            ->relationship('pendapatanJasa')
                            // ->live()
                            ->schema([
                                Select::make('pakets_id')
                                        ->label('Paket')
                                        ->options(Paket::pluck('deskripsi', 'id')->toArray())
                                        // Mengambil data dari tabel
                                        ->required()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems() //agar komponen item tidak berulang
                                        ->reactive() // Membuat field reactive
                                        ->placeholder('Pilih Paket') // Placeholder default
                                        ->afterStateUpdated(function ($state, $set) {
                                            $paket = Paket::find($state);
                                            $set('harga', $paket ? $paket->harga : 0);
                                        })
                                        ->searchable()
                                ,
                                TextInput::make('harga')
                                    ->label('Harga Paket')
                                    ->numeric()
                                    // ->reactive()
                                    ->readonly() // Agar pengguna tidak bisa mengedit
                                    // ->required()
                                    ->dehydrated()
                                ,
                                TextInput::make('jml')
                                    ->label('Jumlah')
                                    ->default(1)
                                    ->reactive()
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // $harga = $get('harga_paket'); // Ambil harga barang
                                        // $total = $harga * $state; // Hitung total
                                        // $set('total', $total); // Set total secara otomatis
                                        $total = collect($get('pendapatan_jasa'))
                                        ->sum(fn ($item) => ($item['harga_paket'] ?? 0) * ($item['jml'] ?? 0));
                                        $set('total', $total);
                                    })
                                ,
                                DatePicker::make('tgl')
                                ->default(today()) // Nilai default: hari ini
                                ->required(),
                            ])
                            ->columns([
                                'md' => 4, //mengatur kolom menjadi 4
                            ])
                            ->addable()
                            ->deletable()
                            ->reorderable()
                            ->createItemButtonLabel('Tambah Item') // Tombol untuk menambah item baru
                            ->minItems(1) // Minimum item yang harus diisi
                            ->required() // Field repeater wajib diisi
                            ,

                            //tambahan form simpan sementara
                            // **Tombol Simpan Sementara**
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

                                        // Simpan data paket
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

                                        // Hitung total total
                                        $total = PendapatanJasa::where('pendapatan_id', $pendapatan->id)
                                            ->sum(DB::raw('harga_paket * jml'));

                                        // Update total di tabel pendapatan
                                        $pendapatan->update(['total' => $total]);
                                                                    })
                                        
                                        ->label('Proses')
                                        ->color('primary'),
                                                            
                                    ])    
       
                        // 
                    ])
                    ,
                    Wizard\Step::make('Pembayaran')
                        ->schema([
                            Placeholder::make('Tabel Pembayaran')
                                    ->content(fn (Get $get) => view('filament.components.pendapatan-table', [
                                        'pembayarans' => Pendapatan::where('no_faktur', $get('no_faktur'))->get()
                                ])), 
                        ]),
                ])->columnSpan(3)
                // Akhir Wizard
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_faktur')->label('No Faktur')->searchable(),
                TextColumn::make('pelanggan.nama_pelanggan') // Relasi ke nama pelanggan
                    ->label('Nama Pelanggan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('pegawai.nama_pegawai') // Relasi ke nama pegawai
                    ->label('Nama Pegawai')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bayar' => 'success',
                        'pesan' => 'warning',
                    }),
                TextColumn::make('total')
                    ->formatStateUsing(fn (string|int|null $state): string => rupiah($state))
                    // ->extraAttributes(['class' => 'text-right']) // Tambahkan kelas CSS untuk rata kanan
                    ->sortable()
                    ->alignment('end') // Rata kanan
                ,
                TextColumn::make('created_at')->label('Tanggal')->dateTime(),
            ])
            ->filters([
                //tambahan untuk memilah data berdasarkan status
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'pesan' => 'Pemesanan',
                        'bayar' => 'Pembayaran',
                    ])
                    ->searchable()
                    ->preload(), // Menampilkan semua opsi saat filter diklik
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            // tombol tambahan
            ->headerActions([
                // tombol tambahan export pdf
                // ✅ Tombol Unduh PDF
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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