<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $orders = $user->orders()->latest()->take(5)->get();

        $stats = [
            'total_orders' => $user->orders()->count(),
            'pending_orders' => $user->orders()->whereIn('status', ['pending', 'processing'])->count(),
            'completed_orders' => $user->orders()->where('status', 'completed')->count(),
        ];

        return view('dashboard', compact('orders', 'stats'));
    }
}
