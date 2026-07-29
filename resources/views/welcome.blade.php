@extends('layouts.app')
 @section('content')
 <section class="max-w-7xl mx-auto px-6 py-20 flex flex-col md:flex-row items-center gap-12">
        <div class="flex-1 space-y-8">
            <span
                class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">#1
                Event Platform</span>
            <h1 class="text-5xl md:text-7xl font-extrabold leading-tight">
                Temukan & Pesan <span class="text-indigo-600">Tiket Event</span> Impianmu.
            </h1>
            <p class="text-lg text-slate-500 max-w-lg leading-relaxed">
                Dari konser musik hingga workshop teknologi, semua ada di genggamanmu. Pesan aman & cepat dengan
                Midtrans.
            </p>
            <div class="flex gap-4">
                <a href="#events"
                    class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-lg shadow-xl shadow-indigo-200 hover:scale-105 transition-transform">
                    Mulai Jelajah
                </a>
                <a href="#"
                    class="px-8 py-4 border-2 border-slate-200 rounded-2xl font-bold text-lg hover:border-indigo-600 hover:text-indigo-600 transition">
                    Cara Pesan
                </a>
            </div>
        </div>
        <div class="flex-1 relative">
            <div
                class="absolute -top-10 -left-10 w-64 h-64 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
            </div>
            <div
                class="absolute -bottom-10 -right-10 w-64 h-64 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000">
            </div>
            <img src="https://res.cloudinary.com/omojhwbn/image/upload/v1785315287/hack_zfwqh4.png" alt="Concert"
                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=800&auto=format&fit=crop&q=80';"
                 class="rounded-[2rem] shadow-2xl relative z-10 w-full object-cover aspect-[4/5] object-center">

            <div class="absolute -bottom-6 -left-6 glass p-6 rounded-2xl shadow-xl z-20 border border-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase">Terverifikasi</p>
                        <p class="font-bold">Pembayaran Aman via Midtrans</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SaaS Banner for Kepanitiaan/HIMA -->
    <section class="max-w-7xl mx-auto px-6 py-4">
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-900 rounded-[2.5rem] p-8 md:p-10 text-white shadow-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-2">
                <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-full text-xs font-bold uppercase tracking-wider">🏢 Platform SaaS Kepanitiaan</span>
                <h3 class="text-2xl md:text-3xl font-black">Punya Event Kepanitiaan atau HIMA?</h3>
                <p class="text-indigo-200 text-sm max-w-xl">Daftarkan organisasi Anda dan mulai jual tiket event secara mandiri dengan analitik pendapatan real-time.</p>
            </div>
            <a href="{{ route('organizer.register') }}" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold rounded-2xl shadow-xl shadow-indigo-500/30 transition hover:scale-105 shrink-0">
                Daftarkan Organisasi Anda &rarr;
            </a>
        </div>
    </section>

    <!-- Events & Category Grid -->
    <section id="kategori" class="max-w-7xl mx-auto px-6 py-20 scroll-mt-28">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl font-extrabold mb-2">Event Terdekat</h2>
                <p class="text-slate-500 font-medium">Jangan sampai ketinggalan acara seru minggu ini!</p>
            </div>
            <div class="flex gap-2">
                <button class="p-3 border rounded-xl hover:bg-white hover:shadow-md transition">Semua Kategori</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @php
                $defaultImages = [
                    'https://res.cloudinary.com/omojhwbn/image/upload/v1785315287/hack_zfwqh4.png',
                    'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&auto=format&fit=crop&q=80'
                ];
            @endphp
            @forelse($events as $item)
                @php
                    $fallbackImage = $defaultImages[$loop->index % count($defaultImages)];
                @endphp
                <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="relative overflow-hidden aspect-[3/4]">
                            @if($item->poster_path)
                                <img src="{{ \Illuminate\Support\Str::startsWith($item->poster_path, 'http') ? $item->poster_path : asset('storage/' . $item->poster_path) }}" 
                                     alt="{{ $item->title }}" 
                                     onerror="this.onerror=null; this.src='https://res.cloudinary.com/omojhwbn/image/upload/v1785315287/hack_zfwqh4.png';"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <img src="{{ \Illuminate\Support\Str::startsWith($fallbackImage, 'http') ? $fallbackImage : asset($fallbackImage) }}" 
                                     alt="{{ $item->title }}" 
                                     onerror="this.onerror=null; this.src='https://res.cloudinary.com/omojhwbn/image/upload/v1785315287/hack_zfwqh4.png';"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @endif

                            <div class="absolute top-4 left-4 px-3 py-1 bg-white/90 backdrop-blur rounded-lg text-xs font-bold uppercase text-indigo-600">
                                {{ $item->category->name ?? 'Umum' }}
                            </div>

                            @if($item->organizer)
                                <div class="absolute top-4 right-4 px-3 py-1 bg-slate-900/80 backdrop-blur rounded-lg text-[10px] font-bold text-white flex items-center gap-1">
                                    <span>🏢 {{ $item->organizer->name }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2 group-hover:text-indigo-600 transition line-clamp-2">{{ $item->title }}</h3>
                            <div class="flex items-center gap-2 text-slate-500 text-sm mb-4">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ date('d M Y, H:i', strtotime($item->date)) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 pt-0">
                        <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                            <span class="text-2xl font-black text-indigo-600">
                                {{ $item->price > 0 ? 'Rp ' . number_format($item->price, 0, ',', '.') : 'Gratis' }}
                            </span>
                            <a href="{{ route('detail-event', ['id' => $item->id]) }}" class="px-5 py-2.5 bg-indigo-50 text-indigo-600 rounded-xl font-bold hover:bg-indigo-600 hover:text-white transition text-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-slate-400">
                    <p class="font-bold">Belum ada event yang dipublikasikan.</p>
                </div>
            @endforelse
        </div>
    </section>
     @endsection    