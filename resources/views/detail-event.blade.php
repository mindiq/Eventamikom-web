@extends('layouts.app')
 @section('content')

    <main class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Left: Poster -->
        <div class="lg:col-span-1">
            <div class="sticky top-32">
                @if(isset($event) && $event->poster_path)
                    <img src="{{ \Illuminate\Support\Str::startsWith($event->poster_path, 'http') ? $event->poster_path : asset('storage/' . $event->poster_path) }}" 
                         alt="{{ $event->title }}" 
                         onerror="this.onerror=null; this.src='https://res.cloudinary.com/omojhwbn/image/upload/v1785315287/hack_zfwqh4.png';"
                         class="w-full rounded-[2.5rem] shadow-2xl border-8 border-white">
                @else
                    <img src="https://res.cloudinary.com/omojhwbn/image/upload/v1785315287/hack_zfwqh4.png" alt="Concert Poster" class="w-full rounded-[2.5rem] shadow-2xl border-8 border-white">
                @endif
                <div class="mt-8 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-bold">Penyelenggara</h4>
                        <a href="{{ route('organizer.show', urlencode($event->organizer->name ?? 'ABP Productions')) }}" class="text-xs font-bold text-indigo-600 hover:underline">Lihat Profil &rarr;</a>
                    </div>
                    <a href="{{ route('organizer.show', urlencode($event->organizer->name ?? 'ABP Productions')) }}" class="flex items-center gap-4 group">
                        <div class="w-12 h-12 bg-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition rounded-full flex items-center justify-center text-indigo-600 font-bold">
                            {{ strtoupper(substr($event->organizer->name ?? 'ABP Productions', 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 group-hover:text-indigo-600 transition">{{ $event->organizer->name ?? 'ABP Productions' }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-500">Verified Organizer</span>
                                <span class="text-xs font-bold text-amber-500 flex items-center gap-0.5">★ 4.9</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right: Details -->
        <div class="lg:col-span-2 space-y-12">
            <div class="space-y-4">
                <span
                    class="px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">{{ $event->category->name ?? 'Umum' }}</span>
                <h1 class="text-4xl md:text-5xl font-black leading-tight">{{ $event->title }}</h1>
                <div class="flex flex-wrap gap-6 text-slate-500 font-medium">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span>{{ date('d M Y, H:i', strtotime($event->date)) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $event->location }}</span>
                    </div>
                </div>
            </div>

            <div class="prose prose-slate max-w-none">
                <h3 class="text-2xl font-bold mb-4">Deskripsi Event</h3>
                <p class="text-lg text-slate-600 leading-relaxed">
                    {{ $event->description }}
                </p>
            </div>

            <div
                class="bg-indigo-600 rounded-[2.5rem] p-8 md:p-12 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                    <div>
                        <p class="text-indigo-200 font-bold uppercase tracking-widest text-sm mb-2">Harga Tiket</p>
                        <h2 class="text-5xl font-black">
                            {{ $event->price > 0 ? 'Rp ' . number_format($event->price, 0, ',', '.') : 'Gratis' }}
                            <span class="text-lg font-medium text-indigo-200">/ orang</span>
                        </h2>
                        <p class="mt-4 text-indigo-100 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Sisa stok: <span class="font-bold underline">{{ $event->stock }} Tiket lagi!</span>
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('checkout.create', $event->id) }}"
                            class="inline-block px-10 py-5 bg-white text-indigo-600 rounded-2xl font-black text-xl hover:scale-105 transition-transform shadow-xl">
                            Pesan Sekarang
                        </a>
                    </div>
                </div>
                <!-- Decoration -->
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-white opacity-10 rounded-full"></div>
                <div class="absolute -left-10 -top-10 w-32 h-32 bg-indigo-400 opacity-20 rounded-full"></div>
            </div>

            <div class="space-y-4">
                <h3 class="text-xl font-bold">Kebijakan Tiket</h3>
                <ul class="space-y-3 text-slate-500">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        E-Ticket akan dikirimkan otomatis setelah pembayaran berhasil.
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Tiket dapat discan di pintu masuk (Check-in).
                    </li>
                    <li class="flex items-start gap-2 text-rose-500">
                        <svg class="w-5 h-5 text-rose-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tiket yang sudah dibeli tidak dapat direfund.
                    </li>
                </ul>
            </div>

            <!-- Section Rating & Review Pasca-Acara -->
            <div class="pt-8 border-t border-slate-200 space-y-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-2xl font-black text-slate-900">Penilaian & Testimoni Peserta</h3>
                        <p class="text-slate-500 text-sm mt-1">Ulasan riil dari peserta pasca-acara diselenggarakan.</p>
                    </div>
                    <a href="{{ route('organizer.show', 'ABP Productions') }}" class="px-4 py-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-xl text-xs font-bold transition flex items-center gap-2">
                        <span>Lihat Rekam Jejak Penyelenggara</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <!-- Form Beri Ulasan Pasca Acara -->
                <div class="bg-slate-50 rounded-3xl border border-slate-200 p-6 md:p-8 shadow-sm">
                    <h4 class="text-lg font-bold text-slate-800 mb-2 flex items-center gap-2">
                        <span>★ Beri Penilaian & Testimoni</span>
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-2.5 py-0.5 rounded-full font-semibold">Peserta Pasca-Acara</span>
                    </h4>
                    <p class="text-xs text-slate-500 mb-6">Bagikan pengalaman Anda mengikuti acara ini untuk membantu calon pembeli lainnya.</p>

                    <form action="{{ route('reviews.store', $event->id ?? 1) }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Penilaian Bintang (1 - 5)</label>
                            <div class="flex items-center gap-3">
                                <select name="rating" class="px-4 py-3 bg-white border border-slate-200 rounded-xl font-bold text-amber-500 outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="5">★★★★★ (5 - Sangat Memuaskan)</option>
                                    <option value="4">★★★★☆ (4 - Bagus)</option>
                                    <option value="3">★★★☆☆ (3 - Cukup)</option>
                                    <option value="2">★★☆☆☆ (2 - Kurang)</option>
                                    <option value="1">★☆☆☆☆ (1 - Buruk)</option>
                                </select>
                            </div>
                        </div>

                        @guest
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap</label>
                                    <input type="text" name="customer_name" placeholder="Nama Anda" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-medium" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Pemesan</label>
                                    <input type="email" name="customer_email" placeholder="email@contoh.com" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-medium" required>
                                </div>
                            </div>
                        @else
                            <div class="p-3 bg-white rounded-xl border border-slate-200 text-xs text-slate-600 flex items-center justify-between">
                                <span>Mengulas sebagai: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }})</span>
                                <span class="text-emerald-600 font-bold">✓ Terverifikasi Login</span>
                            </div>
                        @endguest

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ulasan / Testimoni Anda</label>
                            <textarea name="comment" rows="3" placeholder="Tuliskan ulasan jujur Anda mengenai jalannya acara, pelayanan, atau fasilitas..." class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-medium" required></textarea>
                        </div>

                        <button type="submit" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-200 transition">
                            Kirim Ulasan & Penilaian
                        </button>
                    </form>
                </div>

                <!-- Testimoni List Real & Fallback -->
                <div class="space-y-4">
                    @forelse($reviews as $review)
                        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($review->customer_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-slate-800 text-sm">{{ $review->customer_name }}</h5>
                                        <p class="text-[10px] text-slate-400">Terverifikasi • {{ $review->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-amber-500 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">★ {{ number_format($review->rating, 1) }}</span>
                            </div>
                            <p class="text-slate-600 text-xs leading-relaxed">"{{ $review->comment }}"</p>
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h5 class="font-bold text-slate-800 text-sm">Bagus Prasetyo</h5>
                                    <p class="text-[10px] text-slate-400">Terverifikasi • 2 hari yang lalu</p>
                                </div>
                                <span class="text-xs font-bold text-amber-500 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">★ 5.0</span>
                            </div>
                            <p class="text-slate-600 text-xs leading-relaxed">"Acara sangat seru dan tertata rapi! Penyelenggara ABP Productions luar biasa profesional. Sangat direkomendasikan untuk event-event berikutnya."</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-slate-900 text-slate-400 py-12 px-6 mt-20">
        <div class="max-w-7xl mx-auto text-center">
            <p>&copy; 2024 AmikomEventHub. Prototype Demo.</p>
        </div>
    </footer>

</body>

</html>
@endsection