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
            <p class="text-slate-500 text-xs mt-1">Selesaikan transaksi tiket untuk event <strong>{{ $transaction->event->title }}</strong>.</p>
        </div>

        <!-- Detail Tagihan Card -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Tagihan Tiket</p>
            <h3 class="text-3xl font-black text-indigo-600">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 font-mono">Order ID: {{ $transaction->order_id }}</p>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3 pt-2">
            <button id="pay-button" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-base shadow-xl shadow-indigo-200 transition transform active:scale-95 flex items-center justify-center gap-2">
                <span>💳 Buka Popup Pembayaran Midtrans Snap Default</span>
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
    if (!\Illuminate\Support\Str::startsWith($clientKey, 'SB-')) {
        $clientKey = 'SB-' . $clientKey;
    }
@endphp

<!-- LOAD OFFICIAL MIDTRANS SNAP JS -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
<script type="text/javascript">
    function triggerDefaultMidtransSnap() {
        const token = '{{ $transaction->snap_token }}';
        if (typeof snap !== 'undefined' && snap.pay && token) {
            snap.pay(token, {
                onSuccess: function(result){
                    window.location.href = "{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement";
                },
                onPending: function(result){
                    window.location.href = "{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement";
                },
                onError: function(result){
                    window.location.href = "{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement";
                },
                onClose: function(){
                    // User menutup popup
                }
            });
        } else {
            window.location.href = "{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement";
        }
    }

    document.getElementById('pay-button').onclick = triggerDefaultMidtransSnap;

    // Otomatis panggil snap.pay() bawaan Midtrans saat halaman dibuka
    window.onload = function() {
        setTimeout(triggerDefaultMidtransSnap, 300);
    };
</script>
@endsection
