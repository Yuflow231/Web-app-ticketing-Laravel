<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectsController extends Controller
{
    /**
     * Afficher la liste des projets
     */
    public function index()
    {
        $projects = Project::with(['teamMembers', 'tickets'])
            ->latest()
            ->paginate(15);

        return view('projects.projects', compact('projects'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $users = User::all();
        return view('projects.project-creation', compact('users'));
    }

    /**
     * Alias pour create (pour la route /project-creation)
     */
    public function creation()
    {
        return $this->create();
    }

    /**
     * Créer un nouveau projet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status' => 'required|in:New,In Progress,On Hold,Completed,Closed',
            'estimated_time' => 'nullable|numeric|min:0',
            'contract' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
            'team_roles' => 'nullable|array',
        ]);

        // Gérer l'upload du contrat
        if ($request->hasFile('contract')) {
            $contractPath = $request->file('contract')->store('contracts', 'public');
            $validated['contract'] = $contractPath;
        }

        $validated['creation_date'] = now();
        $validated['progress_percent'] = 0;
        $validated['spent_time'] = 0;

        $project = Project::create($validated);

        // Attacher les membres de l'équipe
        if ($request->has('team_members')) {
            foreach ($request->team_members as $index => $userId) {
                $role = $request->team_roles[$index] ?? 'Maintainer';
                $project->teamMembers()->attach($userId, ['role' => $role]);
            }
        }

        // Ajouter le créateur comme Owner s'il n'est pas déjà dans l'équipe
        if (!$project->teamMembers->contains(Auth::id())) {
            $project->teamMembers()->attach(Auth::id(), ['role' => 'Owner']);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès');
    }

    /**
     * Afficher les détails d'un projet
     */
    public function show(Project $project)
    {
        $project->load(['teamMembers', 'tickets.workers']);

        return view('projects.project-details', compact('project'));
    }

    /**
     * Alias pour show (pour la route /project-details)
     * Affiche le premier projet ou redirige vers la liste si aucun projet
     */
    public function details()
    {
        $project = Project::with(['teamMembers', 'tickets.workers'])->first();

        if (!$project) {
            return redirect()->route('projects.index')
                ->with('info', 'Aucun projet disponible. Créez-en un nouveau.');
        }

        return view('projects.project-details', compact('project'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Project $project)
    {
        $users = User::all();
        $project->load('teamMembers');

        return view('projects.project-edit', compact('project', 'users'));
    }

    /**
     * Mettre à jour un projet
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status' => 'required|in:New,In Progress,On Hold,Completed,Closed',
            'progress_percent' => 'required|integer|min:0|max:100',
            'estimated_time' => 'nullable|numeric|min:0',
            'contract' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
            'team_roles' => 'nullable|array',
        ]);

        // Gérer l'upload du contrat
        if ($request->hasFile('contract')) {
            // Supprimer l'ancien contrat
            if ($project->contract) {
                Storage::disk('public')->delete($project->contract);
            }
            $contractPath = $request->file('contract')->store('contracts', 'public');
            $validated['contract'] = $contractPath;
        }

        // Si le projet est fermé, ajouter la date de clôture
        if ($validated['status'] === 'Closed' && !$project->closing_date) {
            $validated['closing_date'] = now();
        }

        $project->update($validated);

        // Mettre à jour les membres de l'équipe
        if ($request->has('team_members')) {
            $syncData = [];
            foreach ($request->team_members as $index => $userId) {
                $role = $request->team_roles[$index] ?? 'Maintainer';
                $syncData[$userId] = ['role' => $role];
            }
            $project->teamMembers()->sync($syncData);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet mis à jour avec succès');
    }

    /**
     * Supprimer un projet
     */
    public function destroy(Project $project)
    {
        // Supprimer le contrat s'il existe
        if ($project->contract) {
            Storage::disk('public')->delete($project->contract);
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projet supprimé avec succès');
    }
}
