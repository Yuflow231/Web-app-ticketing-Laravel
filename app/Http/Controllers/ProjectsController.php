<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    public function showCreate()
    {
        $users = User::all();
        return view('projects.project-creation', compact('users'));
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
            'owner_id' => 'nullable|exists:users,id',
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

        // don't include the owner_id in project's data as it doesn't actually exist
        $ownerId = $validated['owner_id'] ?? Auth::id();
        unset($validated['owner_id']);

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

        // Add the owner (from modal or current user)
        // Check if owner is not already in team members
        if (!$project->teamMembers->contains($ownerId)) {
            $project->teamMembers()->attach($ownerId, ['role' => 'Owner']);
        } else {
            // If already a member, update the role to owner
            $project->teamMembers()->updateExistingPivot($ownerId, ['role' => 'Owner']);
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

        $project = Project::with('teamMembers')->find($id);
        if (!$project) {
            return redirect()->route('projects.projects')
                ->with('info', 'Project not found or no longer exists.');
        }

        $isMember = $project->teamMembers()->where('users.id', $user->id)->exists();
        $isAdmin = $user->isAdmin();

        if ( !$isMember && !$isAdmin ) {
            return redirect()->route('projects.projects')
                ->with('error', 'You are not allowed to access this project.');
        }

        return view('projects.project-details', compact('project'));
    }

    /**
     * Show project edition form
     */
    public function showEdit(int $id)
    {
        $users = User::all();

        $project = Project::with('teamMembers')->find($id);
        if (!$project) {
            return redirect()->route('projects.projects')
                ->with('info', 'Project not found or no longer exists.');
        }


        return view('projects.project-edit', compact('project', 'users'));
    }

    /**
     * Update a project
     */
    public function update(Request $request, int $id)
    {
        $project = Project::with('teamMembers')->find($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status' => 'required|in:New,In Progress,On Hold,Completed,Closed',
            'estimated_time' => 'nullable|numeric|min:0',
            'contract' => 'nullable|file|max:10240',
            'remove_contract' => 'nullable|boolean',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
            'team_roles' => 'nullable|array',
        ]);

        // Handle contract shenanigans
        if ($request->input('remove_contract') == '1') {
            if ($project->contract && Storage::disk('public')->exists($project->contract)) {
                Storage::disk('public')->delete($project->contract);
            }
            $validated['contract'] = null;
        }
        elseif ($request->hasFile('contract')){
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

        // Remove remove_contract from validated data
        unset($validated['remove_contract']);
        $project->update($validated);

        // Update team members
        if ($request->has('team_members') && is_array($request->team_members) && count($request->team_members) > 0){
            $syncData = [];
            foreach ($request->team_members as $index => $userId) {
                $role = $request->team_roles[$index] ?? 'Maintainer';
                $syncData[$userId] = ['role' => $role];
            }
            $project->teamMembers()->sync($syncData);
        }else {
            // If no team members selected, detach all
            $project->teamMembers()->detach();
        }

        return redirect()->route('projects.project-edit', $project->id)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Delete a project based on an id
     */
    public function destroy(int $id)
    {
        $project = Project::findOrFail($id);

        // Delete the contract if it exists
        if ($project->contract) {
            Storage::disk('public')->delete($project->contract);
        }

        $project->delete();

        return redirect()->route('projects.projects')
            ->with('success', 'Project deleted successfully.');
    }
}
