<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Null_;

class ProjectsController extends Controller
{
    /**
     * Show project list
     */
    public function index()
    {
        $user = Auth::user();

        $query = Project::with(['teamMembers', 'tickets', 'owner']);

        if (!$user || !$user->isAdmin()) {
            $query->whereHas('teamMembers', function ($q) use ($user) {
                $q->where('users.id', $user?->id);
            });
        }

        $projects = $query->latest()->paginate(15);

        return view('projects.projects', compact('projects'));
    }

    /**
     * Show project creation form
     */
    public function create()
    {
        $users = User::all();
        return view('projects.project-creation', compact('users'));
    }

    /**
     * Alias for create (route /project-creation)
     */
    public function creation()
    {
        return $this->create();
    }

    /**
     * Create a project
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status' => 'required|in:New,In Progress,On Hold,Completed,Closed',
            'closing_date' => 'nullable|date',
            'contract' => 'nullable|file|max:10240',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
            'team_roles' => 'nullable|array',
        ]);

        // Handle contract upload
        if ($request->hasFile('contract')) {
            $file = $request->file('contract');

            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $safeBaseName = Str::of($originalName)
                ->ascii()
                ->replaceMatches('/[^A-Za-z0-9]+/', '_')
                ->trim('_')
                ->value();

            if ($safeBaseName === '') {
                $safeBaseName = 'contract';
            }

            $timestamp = now()->format('Ymd_His');
            $fileName = "{$safeBaseName}_{$timestamp}.{$extension}";

            $contractPath = $file->storeAs('contracts', $fileName, 'public');
            $validated['contract'] = $contractPath;
        }

        $validated['creation_date'] = now();
        $validated['closing_date'] = $validated['closing_date'] ?? null;
        $validated['progress_percent'] = 0;
        $validated['spent_time'] = 0;
        $validated['estimated_time'] = 0;

        $project = Project::create($validated);

        // Attach the team member
        if ($request->has('team_members')) {
            foreach ($request->team_members as $index => $userId) {
                $role = $request->team_roles[$index] ?? 'Maintainer';
                $project->teamMembers()->attach($userId, ['role' => $role]);
            }
        }

        // Add the creator as "Owner" if not already in the team
        if (!$project->teamMembers->contains(Auth::id())) {
            $project->teamMembers()->attach(Auth::id(), ['role' => 'Owner']);
        }

        return redirect()->route('projects.project-details', $project->id)
            ->with('success', 'Project created successfully.');
    }

    /**
     * Show project details from an id
     */
    public function details(int $id)
    {
        $user = Auth::user();

        $project = Project::with(['teamMembers', 'tickets.workers'])->find($id);
        if (!$project) {
            return redirect()->route('projects.index')
                ->with('info', 'Project not found or no longer exists.');
        }

        $isMember = $project->teamMembers()->where('users.id', $user->id)->exists();
        $isAdmin = $user->isAdmin();

        if ( !$isMember && !$isAdmin ) {
            return redirect()->route('projects.index')
                ->with('error', 'You are not allowed to access this project.');
        }

        return view('projects.project-details', compact('project'));
    }

    /**
     * Show project edition form
     */
    public function edit(Project $project)
    {
        $users = User::all();
        $project->load('teamMembers');

        return view('projects.project-edit', compact('project', 'users'));
    }

    /**
     * Update a project
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status' => 'required|in:New,In Progress,On Hold,Completed,Closed',
            'progress_percent' => 'required|integer|min:0|max:100',
            'estimated_time' => 'nullable|numeric|min:0',
            'contract' => 'nullable|file|max:10240',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
            'team_roles' => 'nullable|array',
        ]);
// Handle contract upload
        // Handle contract upload
        if ($request->hasFile('contract')) {
            // Delete old contract
            if ($project->contract) {

                Storage::disk('public')->delete($project->contract);
            }

            $file = $request->file('contract');

            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $safeBaseName = Str::of($originalName)
                ->ascii()
                ->replaceMatches('/[^A-Za-z0-9]+/', '_')
                ->trim('_')
                ->value();

            if ($safeBaseName === '') {
                $safeBaseName = 'contract';
            }

            $timestamp = now()->format('Ymd_His');
            $fileName = "{$safeBaseName}_{$timestamp}.{$extension}";

            $contractPath = $file->storeAs('contracts', $fileName, 'public');
            $validated['contract'] = $contractPath;
        }

        // If project is closed, add closing date
        if ($validated['status'] === 'Closed' && !$project->closing_date) {
            $validated['closing_date'] = now();
        }

        $project->update($validated);

        // Update team members
        if ($request->has('team_members')) {
            $syncData = [];
            foreach ($request->team_members as $index => $userId) {
                $role = $request->team_roles[$index] ?? 'Maintainer';
                $syncData[$userId] = ['role' => $role];
            }
            $project->teamMembers()->sync($syncData);
        }

        return redirect()->route('projects.project-details', $project->id)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Delete a project
     */
    public function destroy(Project $project)
    {
        // Delete the contract if it exists
        if ($project->contract) {
            Storage::disk('public')->delete($project->contract);
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
