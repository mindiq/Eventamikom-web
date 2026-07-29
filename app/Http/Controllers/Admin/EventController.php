<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Memeriksa apakah user berhak mengelola event ini (Strict Tenant Ownership Check).
     */
    private function checkOwnership(Event $event)
    {
        $user = Auth::user();

        // Superadmin bebas mengelola semua event
        if ($user->role === 'admin') {
            return;
        }

        // Kepanitiaan/HIMA hanya boleh mengelola event miliknya sendiri
        $organizer = $user->organizer;
        if (!$organizer || $event->organizer_id !== $organizer->id) {
            abort(403, 'Akses Ditolak: Anda hanya dapat mengelola acara milik organisasi Anda sendiri.');
        }
    }

    /**
     * Helper redirect sesuai role tenant/superadmin.
     */
    private function getRedirectRoute($message)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->organizer) {
            return redirect()->route('organizer.dashboard')->with('success', $message);
        }
        return redirect()->route('admin.events.index')->with('success', $message);
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $user->organizer) {
            // Jika user adalah Organisasi/HIMA, alihkan langsung ke Dashboard Organisasi mereka
            return redirect()->route('organizer.dashboard');
        }

        // Superadmin melihat semua event
        $events = Event::with('category', 'organizer')->latest()->get();
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'required',
            'date'        => 'required|date',
            'location'    => 'required',
            'price'       => 'required|numeric',
            'stock'       => 'required|numeric',
            'poster'      => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $user = Auth::user();
        if ($user->organizer) {
            // Otomatis mengikat event ke organizer yang sedang login
            $data['organizer_id'] = $user->organizer->id;
        }

        if ($request->hasFile('poster')) {
            if (env('CLOUDINARY_URL')) {
                try {
                    $cloudinary = new \Cloudinary\Cloudinary(env('CLOUDINARY_URL'));
                    $uploadResult = $cloudinary->uploadApi()->upload($request->file('poster')->getRealPath());
                    $data['poster_path'] = $uploadResult['secure_url'];
                } catch (\Exception $e) {
                    $data['poster_path'] = $request->file('poster')->store('posters', 'public');
                }
            } else {
                $data['poster_path'] = $request->file('poster')->store('posters', 'public');
            }
        }

        Event::create($data);
        return $this->getRedirectRoute('Event berhasil dibuat.');
    }

    public function edit(Event $event)
    {
        $this->checkOwnership($event);

        $categories = Category::all();
        return view('admin.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $this->checkOwnership($event);

        $data = $request->validate([
            'category_id' => 'required',
            'title'       => 'required',
            'description' => 'required',
            'date'        => 'required',
            'location'    => 'required',
            'price'       => 'required|numeric',
            'stock'       => 'required|numeric',
            'poster'      => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('poster')) {
            if ($event->poster_path && !\Illuminate\Support\Str::startsWith($event->poster_path, 'http')) {
                Storage::disk('public')->delete($event->poster_path);
            }

            if (env('CLOUDINARY_URL')) {
                try {
                    $cloudinary = new \Cloudinary\Cloudinary(env('CLOUDINARY_URL'));
                    $uploadResult = $cloudinary->uploadApi()->upload($request->file('poster')->getRealPath());
                    $data['poster_path'] = $uploadResult['secure_url'];
                } catch (\Exception $e) {
                    $data['poster_path'] = $request->file('poster')->store('posters', 'public');
                }
            } else {
                $data['poster_path'] = $request->file('poster')->store('posters', 'public');
            }
        }

        $event->update($data);
        return $this->getRedirectRoute('Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $this->checkOwnership($event);

        if ($event->poster_path && !\Illuminate\Support\Str::startsWith($event->poster_path, 'http')) {
            Storage::disk('public')->delete($event->poster_path);
        }
        $event->delete();
        return $this->getRedirectRoute('Event berhasil dihapus.');
    }
}
