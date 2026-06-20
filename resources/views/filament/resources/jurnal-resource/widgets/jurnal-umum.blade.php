<x-filament-widgets::widget>
    <x-filament::section>

        <div class="overflow-x-auto">

            <!-- Filter Periode Jurnal -->
            <!-- Akhir Filter Periode Jurnal-->

            <!-- Tambahan filter -->
            <div class="row">


                <form wire:submit.prevent="filterJurnal">
                    <label for="periode">Pilih Periode:</label>
                    <input type="month" wire:model="periode" id="periode" class="border rounded px-2 py-1">
                    <button type="submit" class="ml-2 bg-green-500 text-black px-3 py-1 rounded">Filter</button>
                </form>


                <br><br>

                <div class="col-sm-12" style="background-color:white;" align="center">
                    <b>Barbershop Jacko's</b><br>
                    <b>Jurnal Umum</b><br>
                    <b>Periode
                        {{ $periode ? \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') : now()->translatedFormat('F Y') }}
                    </b><br>
                </div>
                <br>
            </div>
            <!-- Akhir Tambahan Filter -->

            <table class="w-full text-sm text-left border border-gray-200">
                <thead class="bg-gray-100 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-2 border">ID Jurnal</th>
                        <th class="px-4 py-2 border">Tanggal</th>
                        <th class="px-4 py-2 border">Akun</th>
                        <th class="px-4 py-2 border">Reff</th>
                        <th class="px-4 py-2 border">Debet</th>
                        <th class="px-4 py-2 border">Kredit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-900 dark:text-gray-100">
                    @forelse($jurnals as $jurnal)
                        @foreach($jurnal->jurnaldetail as $detail)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                {{-- ID Jurnal dan Tanggal tampil di setiap baris transaksi --}}
                                <td class="px-4 py-2 border dark:border-gray-700 text-center font-semibold bg-gray-50/20">
                                    {{ $jurnal->id }}
                                </td>
                                <td class="px-4 py-2 border dark:border-gray-700 text-center font-semibold bg-gray-50/20">
                                    {{ \Carbon\Carbon::parse($jurnal->tgl)->format('Y-m-d') }}
                                </td>

                                {{-- Logika akun dan nominal berdasarkan posisi Debit / Kredit --}}
                                @if($detail->debit != 0)
                                    {{-- Baris Akun Debit --}}
                                    <td class="px-4 py-2 border dark:border-gray-700 font-medium">
                                        {{ $detail->coa->nama_akun ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 border dark:border-gray-700 text-center">
                                        {{ $jurnal->no_referensi }}
                                    </td>
                                    <td class="px-4 py-2 border dark:border-gray-700 text-right pr-4 font-mono">
                                        {{ \Illuminate\Support\Number::currency($detail->debit, 'IDR', 'id') }}
                                    </td>
                                    <td class="px-4 py-2 border dark:border-gray-700 text-right pr-4"></td>
                                @else
                                    {{-- Baris Akun Kredit (Nama akun dikasih spasi inden agar menjorok ke dalam) --}}
                                    <td class="px-4 py-2 border dark:border-gray-700 pl-8 text-gray-600 dark:text-gray-400 italic">
                                        {{ $detail->coa->nama_akun ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 border dark:border-gray-700 text-center">
                                        {{ $jurnal->no_referensi }}
                                    </td>
                                    <td class="px-4 py-2 border dark:border-gray-700 text-right pr-4"></td>
                                    <td class="px-4 py-2 border dark:border-gray-700 text-right pr-4 font-mono">
                                        {{ \Illuminate\Support\Number::currency($detail->credit, 'IDR', 'id') }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 border text-center text-gray-500 italic">
                                Tidak ada data transaksi untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="font-semibold bg-gray-100">
                        <td colspan="4" class="text-right px-4 py-2 border">Total</td>
                        <td class="text-right px-4 py-2 border">
                            {{ rupiah($jurnals->flatMap->jurnaldetail->sum('debit')) }}
                        </td>
                        <td class="text-right px-4 py-2 border">
                            {{ rupiah($jurnals->flatMap->jurnaldetail->sum('credit')) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>