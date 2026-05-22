<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold">Daftar Pembelian Produk</h2>
            <a href="{{ route('pembelian-produk.wizard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                + Tambah Baru
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No Faktur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembeli</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tagihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pembelianProduk as $item)
                    <tr>
                        <td class="px-6 py-4">{{ $item->no_faktur }}</td>
                        <td class="px-6 py-4">{{ $item->pembeli->nama ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $item->tgl->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs 
                                {{ $item->status == 'pending' ? 'bg-yellow-200 text-yellow-800' : '' }}
                                {{ $item->status == 'proses' ? 'bg-blue-200 text-blue-800' : '' }}
                                {{ $item->status == 'selesai' ? 'bg-green-200 text-green-800' : '' }}
                                {{ $item->status == 'batal' ? 'bg-red-200 text-red-800' : '' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">Rp {{ number_format($item->tagihan, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4">
            {{ $pembelianProduk->links() }}
        </div>
    </div>
</div>