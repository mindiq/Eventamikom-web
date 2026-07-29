@extends('layouts.app')

@section('title', 'Pembayaran Tiket - ' . $transaction->event->title)

@section('content')
<main class="max-w-3xl mx-auto px-6 py-16 text-center">
    <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 md:p-12 shadow-2xl inline-block w-full max-w-lg space-y-6">
        
        <!-- Header Icon & Title -->
        <div>
            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-black text-slate-900">Pembayaran QRIS</h2>
            <p class="text-slate-500 text-xs mt-1">Scan kode QRIS di bawah ini menggunakan GoPay, OVO, Dana, ShopeePay, atau m-Banking.</p>
        </div>

        <!-- Detail Tagihan Card -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Tagihan Tiket</p>
            <h3 class="text-3xl font-black text-indigo-600">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 font-mono">Order ID: {{ $transaction->order_id }}</p>
        </div>

        <!-- QRIS Display Card -->
        <div class="p-6 bg-white border-2 border-dashed border-indigo-200 rounded-3xl space-y-4 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-center gap-2 mb-2">
                <span class="px-3 py-1 bg-red-600 text-white font-black text-xs rounded-md tracking-widest uppercase">QRIS</span>
                <span class="text-xs text-slate-400 font-bold">GPN / National Standard</span>
            </div>

            <!-- Dynamic QR Code Container -->
            <div class="bg-white p-4 rounded-2xl inline-block border shadow-md">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=https://eventamikom-web.vercel.app/checkout/{{ $transaction->order_id }}/success" 
                     alt="Kode QRIS Pembayaran" 
                     class="w-48 h-48 mx-auto object-contain">
            </div>

            <p class="text-[11px] text-slate-400">Scan QRIS di atas untuk melakukan simulasi pelunasan instan</p>
        </div>

        <!-- Status & Direct Action Buttons -->
        <div class="space-y-3 pt-2">
            <a href="{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement" 
               class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-base shadow-xl shadow-emerald-200 transition transform active:scale-95 flex items-center justify-center gap-2">
                <span>✅ Konfirmasi & Bayar Sekarang (Simulasi Instan)</span>
            </a>

            <a href="{{ route('home') }}" class="block text-xs font-bold text-slate-400 hover:text-slate-600 transition">
                &larr; Batalkan & Kembali ke Beranda
            </a>
        </div>

    </div>
</main>
@endsection
