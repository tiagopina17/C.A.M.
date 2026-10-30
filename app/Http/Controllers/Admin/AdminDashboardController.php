<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the backoffice administration dashboard.
     */
    public function index(Request $request): View
    {
        $stats = [
            'total_users' => User::count(),
            'recent_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'total_stores' => Store::count(),
            'total_categories' => Category::count(),
            'total_services' => Service::count(),
        ];

        $recentUsers = User::with(['ownedStores', 'employedStores'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $categories = Category::withCount('services')
            ->orderBy('services_count', 'desc')
            ->get();

        $recentStores = Store::with(['owners'])
            ->withCount('users')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'categories', 'recentStores'));
    }
}

