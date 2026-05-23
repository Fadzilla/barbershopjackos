<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Filament\Resources\PenjualanResource\RelationManagers;
use App\Models\Penjualan;
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
use App\Models\Produk;
use App\Models\PembayaranPenjualan;
use App\Models\PenjualanProduk;

// DB
use Illuminate\Support\Facades\DB;

// tambahan untuk tombol unduh pdf
use Filament\Tables\Actions\Action; //untuk dapat menggunakan action
use Barryvdh\DomPDF\Facade\Pdf; // Kalau kamu pakai DomPDF
use Illuminate\Support\Facades\Storage;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    // merubah nama label menjadi Penjualan
    protected static ?string $navigationLabel = 'Penjualan';

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
                                        ->default(fn () => Penjualan::getKodeFaktur()) // Ambil default dari method getKodeBarang
                                        ->label('Nomor Faktur')
                                        ->required()
                                        ->readonly() // Membuat field menjadi read-only
                                    ,
                                    DatePicker::make('tgl')->default(now()) // Nilai default: waktu sekarang
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
                                    TextInput::make('total_dibayar')
                                        ->default(0) // Nilai default
                                        ->hidden()
                                        ->dehydrated(true)
                                    ,
                                    TextInput::make('status')
                                        ->default('pesan') // Nilai default status pemesanan adalah pesan/bayar/kirim
                                        ->hidden()
                                        ->dehydrated()
                                    ,
                                ])
                                ->collapsible() // Membuat section dapat di-collapse
                                ->columns(3)
                            ,
                        ]),
                    Wizard\Step::make('Pilih Produk')
                    ->schema([
                        // 
                            // untuk menambahkan repeater
                            Repeater::make('items')
                            ->relationship('penjualanProduk')
                            ->live()
                            ->schema([
                                Select::make('produk_id')
                                        ->label('Produk')
                                        ->options(Produk::pluck('nama_produk', 'id')->toArray())
                                        // Mengambil data dari tabel
                                        ->required()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems() //agar komponen item tidak berulang
                                        ->reactive() // Membuat field reactive
                                        ->placeholder('Pilih Produk') // Placeholder default
                                        ->afterStateUpdated(function ($state, $set) {
                                            $produk = Produk::find($state);
                                            $set('harga_produk', $produk ? $produk->harga_produk : 0);
                                            $set('harga_jual', $produk ? $produk->harga_produk*1.2 : 0);
                                        })
                                        ->searchable()
                                ,
                                TextInput::make('harga_produk')
                                    ->label('Harga Produk')
                                    ->numeric()
                                    ->default(fn ($get) => $get('produk_id') ? Produk::find($get('produk_id'))?->harga_produk ?? 0 : 0)
                                    ->readonly() // Agar pengguna tidak bisa mengedit
                                    ->hidden()
                                    ->dehydrated()
                                ,
                                TextInput::make('harga_jual')
                                    ->label('Harga Produk')
                                    ->numeric()
                                    // ->reactive()
                                    ->readonly() // Agar pengguna tidak bisa mengedit
                                    // ->required()
                                    ->dehydrated()
                                ,
                                TextInput::make('jml')
                                ->label('Jumlah')
                                ->default(1)
                                ->numeric()
                                ->live()
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    // Ambil data dari repeater bernama 'items'
                                    $items = $get('../../items') ?? []; 
                                    
                                    $totalTagihan = collect($items)->sum(function ($item) {
                                        return ($item['harga_jual'] ?? 0) * ($item['jml'] ?? 0);
                                    });

                                    // Set nilai ke field total_dibayar yang ada di luar repeater
                                    $set('../../total_dibayar', $totalTagihan);
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
                            

                            //tambahan form simpan sementara
                            // **Tombol Simpan Sementara**
                           // Forms\Components\Actions::make([
                                //Forms\Components\Actions\Action::make('Simpan Sementara')
                                    //->action(function ($get) {
                                        //$penjualan = Penjualan::updateOrCreate(
                                           // ['no_faktur' => $get('no_faktur')],
                                            //[
                                              //  'tgl' => $get('tgl'),
                                                //'pelanggan_id' => $get('pelanggan_id'),
                                                //'status' => 'pesan',
                                                //'total_dibayar' => 0
                                            //]
                                        //);

                                        // Simpan data barang
                                        //foreach ($get('items') as $item) {
                                           // PenjualanProduk::updateOrCreate(
                                             //   [
                                               //     'penjualan_id' => $penjualan->id,
                                                 //   'produk_id' => $item['produk_id']
                                               // ],
                                                //[
                                                  //  'harga_produk' => $item['harga_produk'],
                                                    //'harga_jual' => $item['harga_jual'],
                                                    //'jml' => $item['jml'],
                                                    //'tgl' => $item['tgl'],
                                                //]
                                            //);

                                            // Kurangi stok barang di tabel barang
                                            //$produk = Produk::find($item['produk_id']);
                                            //if ($produk) {
                                               // $produk->decrement('stok', $item['jml']); // Kurangi stok sesuai jumlah barang yang dibeli
                                            //}
                                        //}

                                        // Hitung total tagihan
                                        //$totalTagihan = PenjualanProduk::where('penjualan_id', $penjualan->id)
                                          //  ->sum(DB::raw('harga_jual * jml'));

                                        // Update tagihan di tabel penjualan2
                                        //$penjualan->update(['total_dibayar' => $totalTagihan]);
                                          //                          })
                                        
                                        //->label('Proses')
                                        //>color('primary'),
                                                            
                                   
       
                        
                    ])
                    ,
                    Wizard\Step::make('Pembayaran')
                        ->schema([
                            Placeholder::make('Tabel Pembayaran')
                                   ->content(function ($get) {

                $items = $get('items') ?? [];

                $total = collect($items)->sum(function ($item) {
                    return ($item['harga_jual'] ?? 0) * ($item['jml'] ?? 0);
                });

                return view('filament.components.penjualan-table', [
                    'no_faktur' => $get('no_faktur'),
                    'tgl_bayar' => $get('tgl'),
                    'total_dibayar' => $total,
                ]);
            }),
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
                TextColumn::make('pelanggan.nama_pelanggan') // Relasi ke nama pembeli
                    ->label('Nama Pelanggan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bayar' => 'success',
                        'pesan' => 'warning',
                    }),
                TextColumn::make('total_dibayar')
                    ->label('Total Dibayar')
                    ->getStateUsing(fn ($record) => $record->total_dibayar)
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->extraAttributes(['class' => 'text-right']) // Tambahkan kelas CSS untuk rata kanan
                    ->sortable()
                    ->alignment('end') // Rata kanan
                ,
                TextColumn::make('created_at')->label('Tanggal')->date(),
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
                    $penjualan = Penjualan::all();

                    $pdf = Pdf::loadView('pdf.penjualan', ['penjualan' => $penjualan]);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'penjualan-list.pdf'
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
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }
}