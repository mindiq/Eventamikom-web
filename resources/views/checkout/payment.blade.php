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
            <button onclick="showCustomSnapModal()" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-base shadow-xl shadow-indigo-200 transition transform active:scale-95 flex items-center justify-center gap-2">
                <span>💳 Buka Popup Pembayaran Midtrans Snap</span>
            </button>

            <a href="{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement" 
               class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-extrabold text-sm shadow-md shadow-emerald-100 transition flex items-center justify-center gap-2">
                <span>✅ Konfirmasi Pelunasan Tiket</span>
            </a>
        </div>

    </div>
</main>

<!-- PIXEL-PERFECT MIDTRANS SNAP MODAL (SIMULATED EXACTLY LIKE THE 2 SCREENSHOTS) -->
<div id="snap-modal-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <!-- TEST BANNER RIBBON -->
    <div class="fixed top-0 right-0 z-50 pointer-events-none">
        <div class="bg-yellow-400 text-slate-900 text-[10px] font-black tracking-wider py-1 px-8 transform rotate-45 translate-x-6 translate-y-3 shadow-md border-b border-yellow-500">
            TEST
        </div>
    </div>

    <!-- MODAL CONTAINER -->
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-[420px] overflow-hidden text-left border border-slate-200 animate-in fade-in zoom-in duration-200 relative">
        
        <!-- MODAL HEADER -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <span class="font-bold text-sm text-slate-800 tracking-tight">Hafidh Irfan</span>
            <button onclick="closeCustomSnapModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold p-1">&times;</button>
        </div>

        <!-- AMOUNT & ORDER DETAILS -->
        <div class="px-5 py-4 bg-white border-b border-slate-100">
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-black text-slate-900">Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                <span class="text-xs text-indigo-600 font-bold">💳</span>
            </div>
            <div class="flex items-center justify-between text-[11px] text-slate-400 mt-0.5">
                <span class="font-mono">Order ID #{{ $transaction->order_id }}</span>
                <button onclick="toggleDetails()" class="text-indigo-600 font-semibold hover:underline flex items-center gap-0.5">
                    Details <span class="text-[9px]">▼</span>
                </button>
            </div>
        </div>

        <!-- COUNTDOWN TIMER BANNER -->
        <div class="bg-slate-50 py-2 px-4 text-center border-b border-slate-100">
            <p class="text-[11px] text-slate-500 font-medium">Choose within <span id="snap-timer" class="font-bold text-slate-700">00:14:55</span></p>
        </div>

        <!-- VIEW 1: SELECT PAYMENT METHOD -->
        <div id="snap-view-methods" class="max-h-[360px] overflow-y-auto divide-y divide-slate-100">
            
            <!-- LAST PAYMENT METHOD -->
            <div class="px-4 py-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                Last payment method
            </div>
            <button onclick="showQrisView()" class="w-full px-5 py-3.5 flex items-center justify-between hover:bg-slate-50 transition group">
                <div>
                    <p class="font-bold text-sm text-slate-800">GoPay QRIS</p>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="px-1.5 py-0.5 bg-sky-50 text-sky-600 font-extrabold text-[9px] rounded border border-sky-100">gopay</span>
                        <span class="px-1.5 py-0.5 bg-blue-50 text-blue-600 font-extrabold text-[9px] rounded border border-blue-100">gopay later</span>
                        <span class="px-1.5 py-0.5 bg-red-50 text-red-600 font-extrabold text-[9px] rounded border border-red-100">QRIS</span>
                    </div>
                </div>
                <span class="text-slate-300 group-hover:text-slate-500 font-bold">&rsaquo;</span>
            </button>

            <!-- ALL PAYMENT METHODS -->
            <div class="px-4 py-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                All payment methods
            </div>
            <button onclick="showQrisView()" class="w-full px-5 py-3.5 flex items-center justify-between hover:bg-slate-50 transition group">
                <div>
                    <p class="font-bold text-sm text-slate-800">GoPay QRIS</p>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="px-1.5 py-0.5 bg-sky-50 text-sky-600 font-extrabold text-[9px] rounded border border-sky-100">gopay</span>
                        <span class="px-1.5 py-0.5 bg-blue-50 text-blue-600 font-extrabold text-[9px] rounded border border-blue-100">gopay later</span>
                        <span class="px-1.5 py-0.5 bg-red-50 text-red-600 font-extrabold text-[9px] rounded border border-red-100">QRIS</span>
                    </div>
                </div>
                <span class="text-slate-300 group-hover:text-slate-500 font-bold">&rsaquo;</span>
            </button>

            <!-- VIRTUAL ACCOUNT -->
            <button onclick="showQrisView()" class="w-full px-5 py-3.5 flex items-center justify-between hover:bg-slate-50 transition group">
                <div>
                    <p class="font-bold text-sm text-slate-800">Virtual account</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="font-black text-blue-800 text-[10px]">BCA</span>
                        <span class="font-black text-red-800 text-[10px]">Mandiri</span>
                        <span class="font-black text-orange-600 text-[10px]">BNI</span>
                        <span class="font-black text-blue-600 text-[10px]">BRI</span>
                        <span class="text-slate-400 text-[10px]">+2</span>
                    </div>
                </div>
                <span class="text-slate-300 group-hover:text-slate-500 font-bold">&rsaquo;</span>
            </button>

            <!-- CARD PAYMENT -->
            <button onclick="showQrisView()" class="w-full px-5 py-3.5 flex items-center justify-between hover:bg-slate-50 transition group">
                <div>
                    <p class="font-bold text-sm text-slate-800">Card Payment</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="font-bold text-blue-600 text-[10px]">VISA</span>
                        <span class="font-bold text-red-500 text-[10px]">Mastercard</span>
                        <span class="font-bold text-blue-400 text-[10px]">JCB</span>
                    </div>
                </div>
                <span class="text-slate-300 group-hover:text-slate-500 font-bold">&rsaquo;</span>
            </button>

            <!-- SHOPEEPAY QRIS -->
            <button onclick="showQrisView()" class="w-full px-5 py-3.5 flex items-center justify-between hover:bg-slate-50 transition group">
                <div>
                    <p class="font-bold text-sm text-slate-800">ShopeePay QRIS</p>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="px-1.5 py-0.5 bg-orange-50 text-orange-600 font-extrabold text-[9px] rounded">ShopeePay</span>
                        <span class="px-1.5 py-0.5 bg-red-50 text-red-600 font-extrabold text-[9px] rounded">QRIS</span>
                    </div>
                </div>
                <span class="text-slate-300 group-hover:text-slate-500 font-bold">&rsaquo;</span>
            </button>

        </div>

        <!-- VIEW 2: QRIS CODE DISPLAY (MATCHING SCREENSHOT 2 EXACTLY) -->
        <div id="snap-view-qris" class="hidden p-5 text-center space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <span class="font-bold text-sm text-slate-900">GoPay QRIS</span>
                <div class="flex items-center gap-1.5">
                    <span class="px-1.5 py-0.5 bg-sky-50 text-sky-600 font-extrabold text-[9px] rounded">gopay</span>
                    <span class="px-1.5 py-0.5 bg-blue-50 text-blue-600 font-extrabold text-[9px] rounded">gopay later</span>
                    <span class="px-1.5 py-0.5 bg-red-50 text-red-600 font-extrabold text-[9px] rounded">QRIS</span>
                </div>
            </div>

            <!-- OFFICIAL MIDTRANS GOPAY QRIS FRAME CARD -->
            <div class="bg-white p-4 border border-slate-200 rounded-xl inline-block shadow-sm relative w-full max-w-[260px] mx-auto text-center">
                <!-- TOP RED CORNER ACCENT & LOGOS -->
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-1">
                        <span class="font-black text-red-600 text-xs">QRIS</span>
                        <span class="text-[8px] font-bold text-slate-500 border-l border-slate-300 pl-1">QR Code Standar Pembayaran Nasional</span>
                    </div>
                    <span class="font-bold text-red-600 text-[10px]">GPN</span>
                </div>

                <p class="text-xs font-black text-slate-900 mb-2">Hafidh Irfan</p>

                <!-- DYNAMIC QR CODE WITH RED CORNER BRACKETS -->
                <div class="relative p-2 bg-white inline-block border border-slate-100 rounded-lg">
                    <!-- Top Left Corner -->
                    <div class="absolute -top-1 -left-1 w-4 h-4 border-t-2 border-l-2 border-red-600"></div>
                    <!-- Bottom Right Corner -->
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 border-b-2 border-r-2 border-red-600"></div>

                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=https://eventamikom-web.vercel.app/checkout/{{ $transaction->order_id }}/success" 
                         alt="Scan QRIS Pembayaran Midtrans" 
                         class="w-40 h-40 mx-auto object-contain">
                </div>

                <p class="text-[10px] text-slate-400 font-medium mt-2">Dicetak oleh: GoPay</p>
            </div>

            <div class="text-[11px] text-slate-500 font-medium pt-1">
                <button type="button" class="text-indigo-600 font-semibold hover:underline flex items-center justify-center gap-1 mx-auto">
                    <span>ℹ️ How to pay</span>
                    <span class="text-[9px]">s</span>
                </button>
            </div>

            <!-- BUTTONS MATCHING SCREENSHOT 2 -->
            <div class="space-y-2 pt-2">
                <button onclick="finishSimulatedPayment()" class="w-full py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-50 transition">
                    Download QRIS
                </button>
                <button onclick="finishSimulatedPayment()" class="w-full py-2.5 bg-slate-800 text-white rounded-lg text-xs font-bold hover:bg-slate-900 transition">
                    Check status
                </button>
            </div>
        </div>

    </div>
</div>

@php
    $clientKey = env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-1A2B3C4D5E6F7G8H');
    if (!\Illuminate\Support\Str::startsWith($clientKey, 'SB-')) {
        $clientKey = 'SB-' . $clientKey;
    }
@endphp
<!-- OFFICIAL MIDTRANS SNAP JS SDK (SANDBOX) -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>

<script type="text/javascript">
    function triggerOfficialSnapPay() {
        const snapToken = '{{ $transaction->snap_token }}';
        if (typeof snap !== 'undefined' && snap.pay && snapToken && !snapToken.startsWith('MIDTRANS-') && !snapToken.startsWith('DUMMY-')) {
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    window.location.href = "{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement";
                },
                onPending: function(result) {
                    window.location.href = "{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement";
                },
                onError: function(result) {
                    showCustomSnapModal();
                },
                onClose: function() {
                    showCustomSnapModal();
                }
            });
        } else {
            showCustomSnapModal();
        }
    }

    function showCustomSnapModal() {
        document.getElementById('snap-modal-overlay').classList.remove('hidden');
        document.getElementById('snap-view-methods').classList.remove('hidden');
        document.getElementById('snap-view-qris').classList.add('hidden');
    }

    function closeCustomSnapModal() {
        document.getElementById('snap-modal-overlay').classList.add('hidden');
    }

    function showQrisView() {
        document.getElementById('snap-view-methods').classList.add('hidden');
        document.getElementById('snap-view-qris').classList.remove('hidden');
    }

    function finishSimulatedPayment() {
        window.location.href = "{{ route('checkout.success', $transaction->order_id) }}?status_code=200&transaction_status=settlement";
    }

    function toggleDetails() {
        alert("Detail Tagihan Tiket #{{ $transaction->order_id }}: Total Rp {{ number_format($transaction->total_price, 0, ',', '.') }}");
    }

    // 15-MINUTES REALTIME COUNTDOWN TIMER & STOCK RESTORATION
    const createdAtTime = new Date("{{ $transaction->created_at->toIso8601String() }}").getTime();
    const expireTime = createdAtTime + (15 * 60 * 1000);

    function updateCountdownTimer() {
        const now = new Date().getTime();
        const distance = expireTime - now;

        if (distance <= 0) {
            document.getElementById('snap-timer').innerText = "00:00:00";
            alert("⏰ Waktu pembayaran 15 menit telah habis! Transaksi dibatalkan dan stok tiket dikembalikan.");
            window.location.href = "{{ route('home') }}";
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        const hStr = hours.toString().padStart(2, '0');
        const mStr = minutes.toString().padStart(2, '0');
        const sStr = seconds.toString().padStart(2, '0');

        document.getElementById('snap-timer').innerText = `${hStr}:${mStr}:${sStr}`;
    }

    // Jalankan timer setiap 1 detik secara real-time
    setInterval(updateCountdownTimer, 1000);
    updateCountdownTimer();

    // Auto trigger official Midtrans snap pay or custom modal
    window.onload = function() {
        setTimeout(triggerOfficialSnapPay, 300);
    };
</script>
@endsection
