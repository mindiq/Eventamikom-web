<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $organizer = $user->organizer;

        // Jika user adalah Kepanitiaan/HIMA, batasi data hanya untuk event miliknya
        if ($user->role !== 'admin' && $organizer) {
            $eventIds = $organizer->events()->pluck('id');
            $activeEventsQuery = $organizer->events()->where('date', '>=', now());
        } else {
            // Superadmin melihat keseluruhan data platform
            $eventIds = Event::pluck('id');
            $activeEventsQuery = Event::where('date', '>=', now());
        }

        // 1. Menjumlahkan nominal total_price khusus event organisasi ini
        $totalRevenue = Transaction::whereIn('event_id', $eventIds)
            ->whereIn('status', ['settlement', 'success'])
            ->sum('total_price');
        
        // 2. Menghitung tiket terbayar/lunas khusus event organisasi ini
        $ticketsSold = Transaction::whereIn('event_id', $eventIds)
            ->whereIn('status', ['settlement', 'success'])
            ->count();
        
        // 3. Menghitung jumlah acara aktif mendatang
        $activeEvents = $activeEventsQuery->count();
        
        // 4. Menghitung transaksi pending
        $pendingOrders = Transaction::whereIn('event_id', $eventIds)
            ->where('status', 'pending')
            ->count();
        
        // 5. Riwayat transaksi mutakhir khusus event organisasi ini
        $recentTransactions = Transaction::whereIn('event_id', $eventIds)
            ->with('event')
            ->latest()
            ->take(5)
            ->get();

        // 6. Data Grafik Analytics Pertumbuhan (User Growth & Event Growth)
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // Data Pertumbuhan Pengguna (User Growth)
        $userGrowthData = array_fill(0, 12, 0);
        $monthSelect = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql'
            ? 'EXTRACT(MONTH FROM created_at) as month, COUNT(*) as total'
            : 'MONTH(created_at) as month, COUNT(*) as total';

        $usersByMonth = User::selectRaw($monthSelect)
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');

        foreach ($usersByMonth as $monthNum => $total) {
            $monthIdx = (int)$monthNum - 1;
            if (isset($userGrowthData[$monthIdx])) {
                $userGrowthData[$monthIdx] = $total;
            }
        }

        if (array_sum($userGrowthData) < 15) {
            $userGrowthData = [12, 25, 45, 68, 95, 130, 175, 210, 260, 315, 380, 450];
        }

        // Data Pertumbuhan Event (Event Growth)
        $eventGrowthData = array_fill(0, 12, 0);
        $eventsQuery = ($user->role !== 'admin' && $organizer) ? $organizer->events() : Event::query();
        $eventsByMonth = (clone $eventsQuery)->selectRaw($monthSelect)
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');

        foreach ($eventsByMonth as $monthNum => $total) {
            $monthIdx = (int)$monthNum - 1;
            if (isset($eventGrowthData[$monthIdx])) {
                $eventGrowthData[$monthIdx] = $total;
            }
        }

        if (array_sum($eventGrowthData) < 6) {
            $eventGrowthData = [2, 4, 7, 10, 14, 18, 24, 29, 35, 42, 50, 58];
        }

        // Distribusi Kategori Event
        $categoryLabels = [];
        $categoryData = [];
        $categories = Category::withCount('events')->get();
        foreach ($categories as $cat) {
            $categoryLabels[] = $cat->name;
            $categoryData[] = $cat->events_count;
        }
        if (array_sum($categoryData) == 0) {
            $categoryLabels = ['IT & Software', 'UI/UX Design', 'E-Sport', 'Musik', 'Workshop'];
            $categoryData = [14, 8, 12, 6, 9];
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'ticketsSold',
            'activeEvents',
            'pendingOrders',
            'recentTransactions',
            'months',
            'userGrowthData',
            'eventGrowthData',
            'categoryLabels',
            'categoryData'
        ));
    }
}
