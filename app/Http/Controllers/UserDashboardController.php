<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    /**
     * Display the user's dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // 1. Get the currently logged-in user.
        $user = Auth::user();

        // 2. Query the user's cafes to get the counts for each status.
        // We use the 'cafes()' relationship defined in the User model.
        
        // Count all cafes owned by this user.
        $totalCafes = $user->cafes()->count();

        // Count only the cafes with 'approved' status.
        $approvedCafes = $user->cafes()->where('status', 'approved')->count();
        
        // Count only the cafes with 'pending' status.
        $pendingCafes = $user->cafes()->where('status', 'pending')->count();

        // 3. Pass these three variables to the dashboard view.
        return view('user.dashboard', [
            'totalCafes' => $totalCafes,
            'approvedCafes' => $approvedCafes,
            'pendingCafes' => $pendingCafes,
        ]);
    }
}