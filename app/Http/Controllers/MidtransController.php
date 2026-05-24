<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 

class MidtransController extends Controller
{
    public function midtrans(Request $request)
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $transaksi = DB::table('pendapatan')
            ->join('pelanggan', 'pendapatan.pelanggan_id', '=', 'pelanggan.id')
            ->select('pendapatan.*', 'pelanggan.nama_pelanggan', 'pelanggan.no_hp')
            ->orderBy('pendapatan.id', 'desc')
            ->first();

        if (!$transaksi || $transaksi->total <= 0) {
            return "Error: Data transaksi tidak ditemukan atau nominal total masih 0.";
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $transaksi->no_faktur, // Midtrans mengenali ini sebagai order_id
                'gross_amount' => (int)$transaksi->total, 
            ],
            'customer_details' => [
                'first_name' => $transaksi->nama_pelanggan,
                'phone'      => $transaksi->no_hp,
            ],
            'item_details' => [
                [
                    'id'       => $transaksi->no_faktur,
                    'price'    => (int)$transaksi->total,
                    'quantity' => 1,
                    'name'     => "Layanan Barbershop #" . $transaksi->no_faktur
                ]
            ],
        ];
         
        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return view('midtrans.viewsampel', [
                'snap_token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            return "Gagal menyambung ke Midtrans: " . $e->getMessage();
        }
    } 

    public function handleCallback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        
        // Verifikasi Signature (Gunakan order_id sesuai kiriman Midtrans)
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        try {
            // 1. Log Transaksi (Opsional)
            DB::table('payment_logs')->updateOrInsert(
                ['order_id' => $request->order_id],
                [
                    'transaction_status' => $request->transaction_status,
                    'payment_type'       => $request->payment_type,
                    'gross_amount'       => $request->gross_amount,
                    'raw_response'       => json_encode($request->all()),
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]
            );

            if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
                
                // Cari data pendapatan berdasarkan nomor faktur (order_id)
                $pendapatan = DB::table('pendapatan')->where('no_faktur', $request->order_id)->first();
                
                if ($pendapatan) {
                    // 2. Hitung ulang total dari tabel detail
                    $totalReal = DB::table('pendapatan_jasa')
                        ->where('pendapatan_id', $pendapatan->id)
                        ->sum(DB::raw('harga_paket * jml'));

                    // 3. Update status di tabel pendapatan
                    DB::table('pendapatan')
                        ->where('id', $pendapatan->id)
                        ->update([
                            'status' => 'bayar',
                            'total'  => $totalReal
                        ]);

                    // 4. Input ke tabel pembayaran (PASTIKAN KOLOM SESUAI)
                    DB::table('pembayaran')->updateOrInsert(
                        ['order_id' => $request->order_id], // Gunakan nomor faktur sebagai kunci
                        [
                            'pendapatan_id'    => $pendapatan->id, // ID angka
                            'order_id'         => $request->order_id, // Nomor faktur (F-0000X)
                            'jenis_pembayaran' => 'non tunai',
                            'tgl_bayar'        => now(),
                            'transaction_time' => now(),
                            'gross_amount'     => $request->gross_amount,
                            'updated_at'       => now(),
                        ]
                    );
                }
            }
            return response()->json(['message' => 'Success'], 200);

        } catch (\Exception $e) {
            Log::error('Callback Error: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function midtranscallback(Request $request)
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        $transaksi = DB::table('pendapatan')->orderBy('id', 'desc')->first();
        
        if (!$transaksi) return "Data transaksi kosong";

        $oid = $transaksi->no_faktur;
        $grossAmount = (int)$transaksi->total;
        $signatureAsli = hash("sha512", $oid . '200' . $grossAmount . \Midtrans\Config::$serverKey);

        $params = [
            'transaction_details' => ['order_id' => $oid, 'gross_amount' => $grossAmount],
            'customer_details' => ['first_name' => 'Customer Barbershop'],
        ];
         
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return view('midtrans.viewsampelcallback', [
            'snap_token'   => $snapToken,
            'order_id'     => $oid,
            'gross_amount' => $grossAmount,
            'signature'    => $signatureAsli
        ]);
    }
}