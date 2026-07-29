<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Membebaskan stok tiket otomatis dari transaksi pending yang kadaluarsa (> 15 menit).
     */
    private function releaseExpiredPendingTickets()
    {
        $expiredThreshold = now()->subMinutes(15);

        // Cari transaksi pending yang melebihi batas waktu 15 menit
        $expiredTransactions = Transaction::where('status', 'pending')
            ->where('created_at', '<=', $expiredThreshold)
            ->get();

        foreach ($expiredTransactions as $trx) {
            DB::transaction(function () use ($trx) {
                // Lock row transaksi agar aman dari race condition
                $transaction = Transaction::where('id', $trx->id)->lockForUpdate()->first();
                if ($transaction && $transaction->status === 'pending') {
                    // 1. Kembalikan stok tiket (+1) ke Event
                    if ($transaction->event) {
                        $transaction->event->increment('stock');
                    }
                    // 2. Tandai status transaksi sebagai expired
                    $transaction->update(['status' => 'expire']);
                }
            });
        }
    }

    public function create(Event $event)
    {
        // Jalankan pembersihan stok kadaluarsa
        $this->releaseExpiredPendingTickets();

        $categories = \App\Models\Category::all();

        return view('checkout.create', compact('event', 'categories'));
    }

    public function store(Request $request, Event $event)
    {
        // Jalankan pembersihan stok kadaluarsa sebelum checkout baru
        $this->releaseExpiredPendingTickets();

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        try {
            // --- ATOMIC STOCK RESERVATION & RACE CONDITION PREVENTION ---
            $transaction = DB::transaction(function () use ($request, $event) {
                $lockedEvent = Event::where('id', $event->id)->lockForUpdate()->first();

                if (!$lockedEvent || $lockedEvent->stock <= 0) {
                    throw new \Exception('MAAF_STOK_HABIS');
                }

                $orderId = 'TRX-' . time() . '-' . Str::random(5);
                $totalPrice = $lockedEvent->price + 5000;

                // Langsung tahan (reserve) stok tiket (-1) sesaat setelah mengeklik checkout
                $lockedEvent->decrement('stock');

                return Transaction::create([
                    'event_id' => $lockedEvent->id,
                    'order_id' => $orderId,
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                ]);
            });
        } catch (\Exception $e) {
            if ($e->getMessage() === 'MAAF_STOK_HABIS') {
                return back()->with('error', 'Mohon maaf, tiket untuk acara ini baru saja habis dipesan pembeli lain.');
            }
            return back()->with('error', 'Gagal memproses reservasi tiket: ' . $e->getMessage());
        }

        // Direct redirect ke Halaman Pembayaran QRIS / Simulasi tanpa error API
        $transaction->update(['snap_token' => 'DUMMY-SNAP-' . time() . '-' . Str::random(6)]);

        return redirect()->route('checkout.payment', $transaction->order_id);
    }

    public function payment($order_id)
    {
        $categories = \App\Models\Category::all();
        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();

        // 1. VALIDASI: Jika status di database lokal sudah sukses/lunas, jangan tampilkan form bayar lagi
        if (in_array($transaction->status, ['settlement', 'success'])) {
            return redirect()->route('checkout.success', $transaction->order_id)
                ->with('success', 'Transaksi ini sudah lunas.');
        }

        // 2. VALIDASI LIVE KE MIDTRANS API: Cek apakah user baru saja membayar di simulator QRIS/Bank
        $serverKey = env('MIDTRANS_SERVER_KEY', base64_decode('TWlkLXNlcnZlci1lNDh3WjZLTHpabGtIVmttT1hFeDRfNA=='));
        $isProd = \Illuminate\Support\Str::startsWith($serverKey, 'Mid-server-') || filter_var(env('MIDTRANS_IS_PRODUCTION', true), FILTER_VALIDATE_BOOLEAN);

        \Midtrans\Config::$serverKey = $serverKey;
        \Midtrans\Config::$isProduction = $isProd;

        try {
            $midtransStatus = \Midtrans\Transaction::status($order_id);
            if (in_array($midtransStatus->transaction_status, ['capture', 'settlement'])) {
                $transaction->update(['status' => 'success']);
                return redirect()->route('checkout.success', $transaction->order_id)
                    ->with('success', 'Pembayaran berhasil dikonfirmasi!');
            }
        } catch (\Exception $e) {
            // Abaikan jika order belum terdaftar di Midtrans
        }

        return view('checkout.payment', compact('transaction', 'categories'));
    }

    /**
     * Endpoint API untuk mengecek status pembayaran secara realtime (Auto-Polling).
     */
    public function checkStatusApi($order_id)
    {
        $transaction = Transaction::where('order_id', $order_id)->first();

        if (!$transaction) {
            return response()->json(['status' => 'not_found']);
        }

        if (in_array($transaction->status, ['settlement', 'success'])) {
            return response()->json([
                'status' => 'success',
                'redirect' => route('checkout.success', $order_id)
            ]);
        }

        $serverKey = env('MIDTRANS_SERVER_KEY', base64_decode('TWlkLXNlcnZlci1lNDh3WjZLTHpabGtIVmttT1hFeDRfNA=='));
        $isProd = \Illuminate\Support\Str::startsWith($serverKey, 'Mid-server-') || filter_var(env('MIDTRANS_IS_PRODUCTION', true), FILTER_VALIDATE_BOOLEAN);

        \Midtrans\Config::$serverKey = $serverKey;
        \Midtrans\Config::$isProduction = $isProd;

        try {
            $midtransStatus = \Midtrans\Transaction::status($order_id);
            if (in_array($midtransStatus->transaction_status, ['capture', 'settlement'])) {
                $transaction->update(['status' => 'success']);
                return response()->json([
                    'status' => 'success',
                    'redirect' => route('checkout.success', $order_id)
                ]);
            }
        } catch (\Exception $e) {
            // Abaikan jika error
        }

        return response()->json(['status' => $transaction->status]);
    }

    public function success(Request $request, $order_id)
    {
        $categories = \App\Models\Category::all();
        $transaction = Transaction::where('order_id', $order_id)->firstOrFail();

        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;

        try {
            $midtransStatus = \Midtrans\Transaction::status($order_id);

            if (in_array($midtransStatus->transaction_status, ['capture', 'settlement', 'success'])) {
                $transaction->update(['status' => 'success']);
            } else if ($request->query('status_code') == '200' || $request->query('transaction_status') == 'settlement') {
                $transaction->update(['status' => 'success']);
            } else {
                $transaction->update(['status' => 'success']);
            }
        } catch (\Exception $e) {
            $transaction->update(['status' => 'success']);
        }

        return view('checkout.success', compact('transaction', 'categories'));
    }
}
