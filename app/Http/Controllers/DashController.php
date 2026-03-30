<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class DashController extends Controller
{
    /**
     * Show dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Base queries
        $projectsQuery = Project::with(['teamMembers', 'tickets']);
        $ticketsQuery = Ticket::with('workers');

        // Restrict for non-admin users (same logic as list pages)
        if (!$user->isAdmin()) {
            $projectsQuery->whereRelation('teamMembers', 'users.id', $user->id);
            $ticketsQuery->whereRelation('workers', 'users.id', $user->id);
        }

        // Scoped stats
        $stats = [
            'total_projects' => $projectsQuery->count(),
             'active_projects' => $projectsQuery->active()->count(),
             'total_tickets' => $ticketsQuery->count(),
             'active_tickets' => $ticketsQuery->active()->count(),
        ];

        // Recent projects
        $recentProjects = $projectsQuery->latest()
            ->take(6)
            ->get();

        // Recent tickets
        $recentTickets = $ticketsQuery->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'stats',
            'recentProjects',
            'recentTickets',
        ));
    }
}
