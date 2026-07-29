@extends('layouts.app')

@section('title', 'Pembayaran Tiket - ' . $transaction->event->title)

@section('content')
<main class="max-w-3xl mx-auto px-6 py-16 text-center">
    <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 md:p-12 shadow-xl inline-block w-full max-w-lg space-y-6">
        
        <!-- Header Icon & Title -->
        <div>
            <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-black text-slate-900">Selesaikan Pembayaran</h2>
            <p class="text-slate-500 text-sm mt-1">Selesaikan transaksi tiket untuk event <strong>{{ $transaction->event->title }}</strong>.</p>
        </div>

        <!-- Detail Tagihan Card -->
        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total Tagihan Tiket</p>
            <h3 class="text-4xl font-black text-indigo-600">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-500 font-mono">Order ID: {{ $transaction->order_id }}</p>
        </div>

        <!-- Auto Polling Alert Badge -->
        <div id="polling-status-badge" class="p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl text-xs font-bold flex items-center justify-center gap-2">
            <span class="w-2.5 h-2.5 bg-amber-500 rounded-full animate-ping"></span>
            <span>Mengecek status pelunasan pembayaran secara otomatis...</span>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3 pt-2">
            <button id="pay-button" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-lg shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition transform active:scale-95">
                💳 Buka Kode QRIS / Metode Bayar Midtrans
            </button>

            <button id="check-status-btn" onclick="checkPaymentStatusNow()" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-extrabold text-sm shadow-lg shadow-emerald-200 transition flex items-center justify-center gap-2">
                <span>✅ Saya Sudah Bayar / Cek Status Sekarang</span>
            </button>
        </div>

    </div>
</main>

@php
    $clientKey = env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-XAUKQ0ohIJm9S4JM');
    $snapUrl = \Illuminate\Support\Str::startsWith($clientKey, 'Mid-client-') && !\Illuminate\Support\Str::startsWith($clientKey, 'SB-')
        ? 'https://app.midtrans.com/snap/snap.js' 
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp
<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
<script type="text/javascript">
    const checkUrl = "{{ route('checkout.check', $transaction->order_id) }}";

    // 1. Fungsi Buka Popup Midtrans saat Tombol Ditekan
    document.getElementById('pay-button').onclick = function () {
        if (typeof snap !== 'undefined' && snap.pay) {
            snap.pay('{{ $transaction->snap_token }}', {
                onSuccess: function(result){
                    window.location.href = "{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement";
                },
                onPending: function(result){
                    checkPaymentStatusNow();
                },
                onError: function(result){
                    checkPaymentStatusNow();
                }
            });
        } else {
            checkPaymentStatusNow();
        }
    };

    // 2. Fungsi Pengecekan Status Real-Time via AJAX
    function checkPaymentStatusNow() {
        const badge = document.getElementById('polling-status-badge');
        badge.className = "p-3 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-2xl text-xs font-bold flex items-center justify-center gap-2";
        badge.innerHTML = "<span>🔄 Memverifikasi pelunasan ke Midtrans...</span>";

        fetch(checkUrl)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' || data.status === 'settlement') {
                    badge.className = "p-3 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-2xl text-xs font-bold flex items-center justify-center gap-2";
                    badge.innerHTML = "<span>🎉 Pembayaran Berhasil! Mengalihkan ke tiket...</span>";
                    setTimeout(() => {
                        window.location.href = data.redirect || "{{ route('checkout.success', $transaction->order_id) }}";
                    }, 500);
                } else {
                    setTimeout(() => {
                        badge.className = "p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl text-xs font-bold flex items-center justify-center gap-2";
                        badge.innerHTML = "<span class='w-2.5 h-2.5 bg-amber-500 rounded-full animate-ping'></span><span>Menunggu pelunasan pembayaran QRIS / Bank...</span>";
                    }, 1000);
                }
            })
            .catch(err => {
                console.error(err);
            });
    }

    // 3. Auto Polling Setiap 3 Detik (Mengecek otomatis saat pengunjung scan QRIS)
    const pollingInterval = setInterval(checkPaymentStatusNow, 3000);

    // 4. Buka popup 1x saat halaman pertama dibuka
    window.onload = function() {
        setTimeout(function() {
            snap.pay('{{ $transaction->snap_token }}', {
                onSuccess: function(result){
                    window.location.href = "{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement";
                },
                onPending: function(result){
                    checkPaymentStatusNow();
                }
            });
        }, 300);
    }
</script>
@endsection
