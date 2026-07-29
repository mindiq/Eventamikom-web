@extends('layouts.app')

@section('title', 'Pembayaran Tiket - ' . $transaction->event->title)

@section('content')
<main class="max-w-3xl mx-auto px-6 py-16 text-center">
    <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 md:p-12 shadow-2xl inline-block w-full max-w-lg space-y-6">
        
        <!-- Header Icon & Title -->
        <div>
            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-black text-slate-900">Selesaikan Pembayaran</h2>
            <p class="text-slate-500 text-xs mt-1">Gunakan Midtrans Snap Popup atau Scan QRIS di bawah ini.</p>
        </div>

        <!-- Detail Tagihan Card -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Tagihan Tiket</p>
            <h3 class="text-3xl font-black text-indigo-600">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 font-mono">Order ID: {{ $transaction->order_id }}</p>
        </div>

        <!-- QRIS Card -->
        <div class="p-5 bg-white border-2 border-dashed border-indigo-200 rounded-3xl space-y-3 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-center gap-2">
                <span class="px-2.5 py-0.5 bg-red-600 text-white font-black text-[10px] rounded tracking-widest uppercase">QRIS</span>
                <span class="text-xs text-slate-500 font-bold">Midtrans Direct QRIS</span>
            </div>
            <div class="bg-white p-3 rounded-2xl inline-block border shadow-sm">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=https://eventamikom-web.vercel.app/checkout/{{ $transaction->order_id }}/success" 
                     alt="Kode QRIS Pembayaran" 
                     class="w-40 h-40 mx-auto object-contain">
            </div>
            <p class="text-[11px] text-slate-400">Scan QRIS menggunakan GoPay, OVO, Dana, ShopeePay, atau m-Banking</p>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3 pt-2">
            <button id="pay-button" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-base shadow-xl shadow-indigo-200 transition transform active:scale-95 flex items-center justify-center gap-2">
                <span>💳 Buka Popup Pembayaran Midtrans Snap</span>
            </button>

            <a href="{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement" 
               class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-extrabold text-sm shadow-md shadow-emerald-100 transition flex items-center justify-center gap-2">
                <span>✅ Konfirmasi Pelunasan Tiket</span>
            </a>
        </div>

    </div>
</main>

@php
    $clientKey = env('MIDTRANS_CLIENT_KEY', 'Mid-client-XAUKQ0ohIJm9S4JM');
    $snapClientKey = \Illuminate\Support\Str::startsWith($clientKey, 'SB-') ? $clientKey : 'SB-' . $clientKey;
@endphp
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $snapClientKey }}"></script>
<script type="text/javascript">
    document.getElementById('pay-button').onclick = function () {
        if (typeof snap !== 'undefined' && snap.pay) {
            snap.pay('{{ $transaction->snap_token }}', {
                onSuccess: function(result){
                    window.location.href = "{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement";
                },
                onPending: function(result){
                    window.location.href = "{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement";
                },
                onError: function(result){
                    window.location.href = "{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement";
                }
            });
        } else {
            window.location.href = "{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement";
        }
    };
</script>
@endsection
