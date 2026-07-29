@extends('layouts.app')

@section('title', 'Profil Penyelenggara - ' . $organizerName)

@section('content')
<main class="max-w-7xl mx-auto px-6 py-12 space-y-12">
    <!-- Header Profil Penyelenggara -->
    <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 rounded-[2.5rem] p-8 md:p-12 text-white shadow-2xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-8">
            <div class="w-28 h-28 bg-white text-indigo-900 font-black text-4xl rounded-3xl flex items-center justify-center shadow-xl border-4 border-indigo-400/30 shrink-0">
                {{ strtoupper(substr($organizerName, 0, 2)) }}
            </div>
            <div class="text-center md:text-left space-y-3 flex-1">
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                    <h1 class="text-3xl md:text-5xl font-black">{{ $organizerName }}</h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 rounded-full text-xs font-bold uppercase tracking-wider">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Verified Organizer
                    </span>
                </div>
                <p class="text-indigo-200 max-w-2xl font-medium">
                    Penyelenggara event profesional terpercaya dengan rekam jejak kepuasan peserta terbaik. Berkomitmen menghadirkan pengalaman acara berkualitas tinggi.
                </p>
                <div class="flex flex-wrap gap-6 pt-2 text-sm text-indigo-300">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        Yogyakarta, Indonesia
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Bergabung sejak 2024
                    </span>
                </div>
            </div>
        </div>

        <!-- Metric Grid Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 pt-8 border-t border-indigo-700/50">
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 text-center border border-white/10">
                <p class="text-indigo-300 text-xs font-bold uppercase tracking-wider">Rata-rata Rating</p>
                <div class="flex items-center justify-center gap-2 mt-1">
                    <span class="text-3xl font-black text-amber-400">{{ number_format($averageRating, 1) }}</span>
                    <div class="flex text-amber-400 text-sm">★</div>
                </div>
                <p class="text-[10px] text-indigo-300 mt-1">dari 5.0 bintang</p>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 text-center border border-white/10">
                <p class="text-indigo-300 text-xs font-bold uppercase tracking-wider">Total Testimoni</p>
                <p class="text-3xl font-black text-white mt-1">{{ $totalReviews }}</p>
                <p class="text-[10px] text-indigo-300 mt-1">ulasan terverifikasi</p>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 text-center border border-white/10">
                <p class="text-indigo-300 text-xs font-bold uppercase tracking-wider">Total Acara</p>
                <p class="text-3xl font-black text-white mt-1">{{ $events->count() }}</p>
                <p class="text-[10px] text-indigo-300 mt-1">event diselenggarakan</p>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 text-center border border-white/10">
                <p class="text-indigo-300 text-xs font-bold uppercase tracking-wider">Kepuasan Peserta</p>
                <p class="text-3xl font-black text-emerald-400 mt-1">98%</p>
                <p class="text-[10px] text-indigo-300 mt-1">rekomendasi positif</p>
            </div>
        </div>
    </div>

    <!-- Main Content: Rating & Reviews Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Ringkasan Penilaian (Left) -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                <h3 class="text-xl font-bold text-slate-900 mb-6">Rekam Jejak Penilaian</h3>
                
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                    <div class="text-center bg-amber-50 px-6 py-4 rounded-2xl border border-amber-100">
                        <span class="text-4xl font-black text-amber-500">{{ number_format($averageRating, 1) }}</span>
                        <div class="flex justify-center text-amber-400 text-sm mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <span>{{ $i <= round($averageRating) ? '★' : '☆' }}</span>
                            @endfor
                        </div>
                    </div>
                    <div>
                        <p class="font-extrabold text-slate-800 text-lg">Sangat Memuaskan</p>
                        <p class="text-xs text-slate-500">Berdasarkan ulasan peserta pasca-acara</p>
                    </div>
                </div>

                <!-- Star Rating Progress Bars -->
                <div class="space-y-3">
                    @foreach($ratingBreakdown as $star => $data)
                        <div class="flex items-center gap-3 text-sm">
                            <span class="w-12 font-bold text-slate-600 flex items-center gap-1">
                                {{ $star }} <span class="text-amber-400">★</span>
                            </span>
                            <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-400 rounded-full transition-all duration-500" style="width: {{ $data['percentage'] }}%"></div>
                            </div>
                            <span class="w-10 text-right text-xs font-bold text-slate-400">{{ $data['percentage'] }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Trust Badge Box -->
            <div class="bg-emerald-50 rounded-3xl border border-emerald-200 p-6 text-emerald-900 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center font-bold">✓</div>
                    <div>
                        <h4 class="font-extrabold text-sm">Jaminan Penyelenggara Resmi</h4>
                        <p class="text-xs text-emerald-700">Semua testimoni dikirim oleh pemesan tiket terverifikasi.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feed Testimoni & Ulasan (Right) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-black text-slate-900">Ulasan & Testimoni Peserta ({{ $totalReviews }})</h3>
            </div>

            @if($reviews->count() > 0)
                <div class="space-y-4">
                    @foreach($reviews as $review)
                        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full font-bold flex items-center justify-center text-sm">
                                        {{ strtoupper(substr($review->customer_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-base">{{ $review->customer_name }}</h4>
                                        <p class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 px-3 py-1 bg-amber-50 border border-amber-200 rounded-full">
                                    <span class="text-amber-500 font-black text-sm">★ {{ $review->rating }}.0</span>
                                </div>
                            </div>
                            
                            <p class="text-slate-600 text-sm leading-relaxed mb-4">"{{ $review->comment }}"</p>
                            
                            @if($review->event)
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-xl text-xs text-slate-500 font-medium">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5z"></path></svg>
                                    <span>Event: <strong>{{ $review->event->title }}</strong></span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Mock Testimonials fallback if DB has no reviews yet -->
                <div class="space-y-4">
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full font-bold flex items-center justify-center text-sm">B</div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-base">Bagus Prasetyo</h4>
                                    <p class="text-xs text-slate-400">2 hari lalu</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 px-3 py-1 bg-amber-50 border border-amber-200 rounded-full">
                                <span class="text-amber-500 font-black text-sm">★ 5.0</span>
                            </div>
                        </div>
                        <p class="text-slate-600 text-sm leading-relaxed mb-4">"Acara sangat tertata rapi dari awal registrasi check-in hingga penutupan. Sound system dan guest star luar biasa bagus! Pasti ikut lagi di event berikutnya!"</p>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-xl text-xs text-slate-500 font-medium">
                            <span>Event: <strong>Jazz Night 2024: A Celebration of Rhythm & Melody</strong></span>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full font-bold flex items-center justify-center text-sm">A</div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-base">Annisa Rahmawati</h4>
                                    <p class="text-xs text-slate-400">Seminggu lalu</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 px-3 py-1 bg-amber-50 border border-amber-200 rounded-full">
                                <span class="text-amber-500 font-black text-sm">★ 5.0</span>
                            </div>
                        </div>
                        <p class="text-slate-600 text-sm leading-relaxed mb-4">"Penyelenggara sangat komunikatif dan penukaran tiket via Midtrans sangat cepat tanpa antre panjang. Recommended banget penyelenggara ini!"</p>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-xl text-xs text-slate-500 font-medium">
                            <span>Event: <strong>Amikom Tech Fest 2024</strong></span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Event Diselenggarakan -->
            <div class="pt-8 border-t border-slate-200">
                <h3 class="text-2xl font-black text-slate-900 mb-6">Event Oleh Penyelenggara Ini</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($events as $event)
                        <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-sm hover:shadow-lg transition">
                            <img src="{{ $event->poster_path ? (\Illuminate\Support\Str::startsWith($event->poster_path, 'http') ? $event->poster_path : asset('storage/' . $event->poster_path)) : asset('assets/concert.png') }}" alt="Poster" class="w-full h-40 object-cover rounded-2xl mb-4">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold uppercase">{{ $event->category->name ?? 'Music' }}</span>
                            <h4 class="font-bold text-lg text-slate-800 mt-2 line-clamp-1">{{ $event->title }}</h4>
                            <p class="text-xs text-slate-500 mt-1">Rp {{ number_format($event->price, 0, ',', '.') }}</p>
                            <div class="mt-4 pt-3 border-t flex justify-between items-center">
                                <span class="text-xs font-bold text-amber-500 flex items-center gap-1">
                                    ★ {{ $event->averageRating() }} ({{ $event->reviewsCount() }} ulasan)
                                </span>
                                <a href="{{ route('detail-event') }}" class="text-xs font-bold text-indigo-600 hover:underline">Lihat Detail &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
