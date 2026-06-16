<x-filament-widgets::widget>
    <x-filament::section>

        <div class="overflow-x-auto">

            <!-- FILTER -->
            <form wire:submit.prevent="filterJurnal" class="flex gap-4 items-center mb-4">
                <div>
                    <label>Periode Awal:</label>
                    <input type="month" wire:model="periode_awal" class="border rounded px-2 py-1">
                </div>

                <div>
                    <label>Periode Akhir:</label>
                    <input type="month" wire:model="periode_akhir" class="border rounded px-2 py-1">
                </div>

                <div>
                    <label>COA:</label>
                    <select wire:model="coa_id" class="border rounded px-2 py-1">
                        <option value="">-- Pilih Akun --</option>
                        @foreach (\App\Models\Coa::all() as $akun)
                            <option value="{{ $akun->id }}">
                                {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="bg-green-500 text-black px-3 py-1 rounded mt-5">
                    Filter
                </button>
            </form>

            <!-- HEADER -->
            <div class="text-center mb-4">
                <b>Toko Mukena</b><br>
                <b>Buku Besar</b><br>

                <b>
                    Periode
                    @if($periode_awal && $periode_akhir)
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $periode_awal)->translatedFormat('F Y') }}
                        -
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $periode_akhir)->translatedFormat('F Y') }}
                    @else
                        {{ now()->translatedFormat('F Y') }}
                    @endif
                </b>
            </div>

            <!-- TABLE -->
            <table class="w-full text-sm border">

                <thead class="bg-gray-200">
                    <tr>
                        <th>ID Jurnal</th>
                        <th>Tanggal</th>
                        <th>Akun</th>
                        <th>Reff</th>
                        <th>Debet</th>
                        <th>Kredit</th>
                    </tr>
                </thead>

                <tbody class="text-xs uppercase">

                    <!-- SALDO AWAL -->
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="4" class="text-right">Saldo Awal</td>
                        <td colspan="2" class="text-right">
                            {{ rupiah($saldoAwal) }}
                        </td>
                    </tr>

                    <!-- DATA -->
                    @foreach($jurnals as $jurnal)
                        @foreach($jurnal->details ?? [] as $detail)

                            <tr>
                                <td>{{ $jurnal->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('Y-m-d') }}</td>

                                <td>{{ $detail->coa->nama_akun ?? '-' }}</td>
                                <td>{{ $jurnal->no_ref ?? '-' }}</td>

                                <td class="text-right">
                                    {{ $detail->debit != 0 ? rupiah($detail->debit) : '' }}
                                </td>

                                <td class="text-right">
                                    {{ $detail->kredit != 0 ? rupiah($detail->kredit) : '' }}
                                </td>
                            </tr>

                        @endforeach
                    @endforeach

                </tbody>

                <!-- FOOTER -->
                <tfoot class="bg-gray-200 font-bold">

                    @php
                        $totalDebit = $jurnals->flatMap->details->sum('debit');
                        $totalKredit = $jurnals->flatMap->details->sum('kredit');
                        $saldoAkhir = $saldoAwal + ($totalDebit - $totalKredit);
                    @endphp

                    <tr>
                        <td colspan="4" class="text-right">Total</td>
                        <td class="text-right">{{ rupiah($totalDebit) }}</td>
                        <td class="text-right">{{ rupiah($totalKredit) }}</td>
                    </tr>

                    <tr>
                        <td colspan="4" class="text-right">Saldo Akhir</td>
                        <td colspan="2" class="text-right">
                            {{ rupiah($saldoAkhir) }}
                        </td>
                    </tr>

                </tfoot>

            </table>

        </div>

    </x-filament::section>
</x-filament-widgets::widget>