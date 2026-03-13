<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashController extends Controller
{
    /**
     * Afficher le dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Statistiques générales
        $stats = [
            'total_projects' => Project::count(),
            'active_projects' => Project::active()->count(),
            'total_tickets' => Ticket::count(),
            'active_tickets' => Ticket::active()->count(),
        ];

        // Projets récents
        $recentProjects = Project::with('teamMembers')
            ->latest()
            ->take(5)
            ->get();

        // Tickets récents
        $recentTickets = Ticket::with(['project', 'workers'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'stats',
            'recentProjects',
            'recentTickets',
        ));
    }
}
