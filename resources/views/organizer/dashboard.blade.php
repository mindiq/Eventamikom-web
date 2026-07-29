@extends('layouts.app')

@section('title', 'Dashboard Organisasi - ' . ($organizer->name ?? 'Kepanitiaan'))

@section('content')
<main class="max-w-7xl mx-auto px-6 py-12 space-y-10">
    <!-- Header Banner Tenant -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-900 text-white rounded-[2.5rem] p-8 md:p-10 shadow-2xl relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                <span>🏢 Panel Tenant Organisasi</span>
            </div>
            <h1 class="text-3xl md:text-4xl font-black">{{ $organizer->name ?? 'Kepanitiaan / HIMA' }}</h1>
            <p class="text-indigo-200 text-sm mt-1 max-w-xl">Kelola acara mandiri dan pantau analitik real-time pendapatan event Anda.</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('admin.events.create') }}" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold rounded-2xl shadow-xl shadow-indigo-500/30 transition flex items-center gap-2">
                <span>+ Buat Event Baru</span>
            </a>
            <a href="{{ route('organizer.show', $organizer->name ?? 'ABP Productions') }}" class="px-5 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-2xl border border-white/20 transition">
                Lihat Profil Publik
            </a>
        </div>
    </div>

    <!-- Scoped Revenue Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Pendapatan Acara Organisasi</p>
            <h3 class="text-3xl font-extrabold text-indigo-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <p class="text-xs text-emerald-600 font-bold mt-2 flex items-center gap-1">
                <span>↑ Transaksi Lunas</span>
            </p>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Tiket Terjual</p>
            <h3 class="text-3xl font-extrabold text-slate-900">{{ $ticketsSold }}</h3>
            <p class="text-xs text-slate-400 mt-2">peserta terdaftar</p>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Acara Aktif</p>
            <h3 class="text-3xl font-extrabold text-emerald-600">{{ $activeEvents }}</h3>
            <p class="text-xs text-slate-400 mt-2">mendatang</p>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Status Organisasi</p>
            <div class="mt-2">
                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-800 rounded-full font-bold text-xs">
                    ✓ Verified & Active
                </span>
            </div>
            <p class="text-[10px] text-slate-400 mt-3">Disetujui oleh Superadmin</p>
        </div>
    </div>

    <!-- Tenant Events Table -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm space-y-6">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold text-slate-900">Daftar Acara Milik Organisasi Anda</h3>
            <a href="{{ route('admin.events.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Kelola Semua Event &rarr;</a>
        </div>

        @if($events->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                            <th class="py-4 px-4">Event</th>
                            <th class="py-4 px-4">Tanggal</th>
                            <th class="py-4 px-4">Harga Tiket</th>
                            <th class="py-4 px-4">Sisa Stok</th>
                            <th class="py-4 px-4">Rating & Ulasan</th>
                            <th class="py-4 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-medium">
                        @foreach($events as $event)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-4 flex items-center gap-3">
                                    <img src="{{ $event->poster_path ? (\Illuminate\Support\Str::startsWith($event->poster_path, 'http') ? $event->poster_path : asset('storage/' . $event->poster_path)) : asset('assets/concert.png') }}" alt="Poster" class="w-12 h-12 rounded-xl object-cover">
                                    <div>
                                        <p class="font-bold text-slate-800 line-clamp-1">{{ $event->title }}</p>
                                        <p class="text-xs text-slate-400">{{ $event->category->name ?? 'Umum' }}</p>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-slate-600">{{ \Illuminate\Support\Carbon::parse($event->date)->format('d M Y') }}</td>
                                <td class="py-4 px-4 font-bold text-indigo-600">Rp {{ number_format($event->price, 0, ',', '.') }}</td>
                                <td class="py-4 px-4 font-bold text-slate-700">{{ $event->stock }} Tiket</td>
                                <td class="py-4 px-4 font-bold text-amber-500">
                                    ★ {{ $event->averageRating() }} ({{ $event->reviewsCount() }})
                                </td>
                                <td class="py-4 px-4 text-right space-x-2">
                                    <a href="{{ route('admin.events.edit', $event->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-slate-400">
                <p class="font-bold">Belum ada acara yang dibuat oleh organisasi Anda.</p>
                <a href="{{ route('admin.events.create') }}" class="inline-block mt-3 px-5 py-2.5 bg-indigo-600 text-white font-bold text-xs rounded-xl hover:bg-indigo-700 transition">+ Buat Event Pertama</a>
            </div>
        @endif
    </div>

    <!-- Recent Transactions Scoped to Tenant -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm space-y-6">
        <h3 class="text-xl font-bold text-slate-900">Transaksi Terakhir Pembelian Tiket</h3>
        @if($recentTransactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-4">Order ID</th>
                            <th class="py-3 px-4">Pemesan</th>
                            <th class="py-3 px-4">Event</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentTransactions as $tx)
                            <tr>
                                <td class="py-3.5 px-4 font-mono text-xs font-bold text-indigo-600">{{ $tx->order_id }}</td>
                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-slate-800">{{ $tx->customer_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $tx->customer_email }}</p>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-700">{{ $tx->event->title ?? '-' }}</td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase {{ in_array($tx->status, ['settlement', 'success']) ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $tx->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-slate-400 text-xs text-center py-6">Belum ada riwayat transaksi tiket.</p>
        @endif
    </div>
</main>
@endsection
