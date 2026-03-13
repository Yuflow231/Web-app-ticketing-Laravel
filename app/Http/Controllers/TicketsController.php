<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Project;
use App\Models\User;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketsController extends Controller
{
    /**
     * Afficher la liste des tickets
     */
    public function index()
    {
        $tickets = Ticket::with(['project', 'workers'])
            ->latest()
            ->paginate(20);

        return view('tickets.tickets', compact('tickets'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $projects = Project::all();
        $users = User::all();

        return view('tickets.ticket-creation', compact('projects', 'users'));
    }

    /**
     * Alias pour create (pour la route /ticket-creation)
     */
    public function creation()
    {
        return $this->create();
    }

    /**
     * Créer un nouveau ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'status' => 'required|in:New,In Progress,On Hold,Completed,Closed',
            'priority' => 'required|in:High,Medium,Low',
            'type' => 'required|in:Billed,Included',
            'estimated_time' => 'nullable|numeric|min:0',
            'workers' => 'nullable|array',
            'workers.*' => 'exists:users,id',
            'worker_roles' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $validated['spent_time'] = 0;

        $ticket = Ticket::create($validated);

        // Attacher les travailleurs
        if ($request->has('workers')) {
            foreach ($request->workers as $index => $userId) {
                $role = $request->worker_roles[$index] ?? '';
                $ticket->workers()->attach($userId, ['role' => $role]);
            }
        }

        // Ajouter le créateur comme "Ticket Creator" s'il n'est pas déjà dans les workers
        if (!$ticket->workers->contains(Auth::id())) {
            $ticket->workers()->attach(Auth::id(), ['role' => 'Ticket Creator']);
        }

        // Gérer les pièces jointes
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('attachments', $filename, 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'file_name' => $filename,
                ]);
            }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket créé avec succès');
    }

    /**
     * Afficher les détails d'un ticket
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['project', 'workers', 'attachments']);

        return view('tickets.ticket-details', compact('ticket'));
    }

    /**
     * Alias pour show (pour la route /ticket-details)
     * Affiche le premier ticket ou redirige vers la liste si aucun ticket
     */
    public function details()
    {
        $ticket = Ticket::with(['project', 'workers', 'attachments'])->first();

        if (!$ticket) {
            return redirect()->route('tickets.index')
                ->with('info', 'Aucun ticket disponible. Créez-en un nouveau.');
        }

        return view('tickets.ticket-details', compact('ticket'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Ticket $ticket)
    {
        $projects = Project::all();
        $users = User::all();
        $ticket->load(['workers', 'attachments']);

        return view('tickets.ticket-edit', compact('ticket', 'projects', 'users'));
    }

    /**
     * Mettre à jour un ticket
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'status' => 'required|in:New,In Progress,On Hold,Completed,Closed',
            'priority' => 'required|in:High,Medium,Low',
            'type' => 'required|in:Billed,Included',
            'estimated_time' => 'nullable|numeric|min:0',
            'spent_time' => 'required|numeric|min:0',
            'workers' => 'nullable|array',
            'workers.*' => 'exists:users,id',
            'worker_roles' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $ticket->update($validated);

        // Mettre à jour les travailleurs
        if ($request->has('workers')) {
            $syncData = [];
            foreach ($request->workers as $index => $userId) {
                $role = $request->worker_roles[$index] ?? '';
                $syncData[$userId] = ['role' => $role];
            }
            $ticket->workers()->sync($syncData);
        }

        // Gérer les nouvelles pièces jointes
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('attachments', $filename, 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'file_name' => $filename,
                ]);
            }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket mis à jour avec succès');
    }

    /**
     * Supprimer un ticket
     */
    public function destroy(Ticket $ticket)
    {
        // Supprimer les pièces jointes
        foreach ($ticket->attachments as $attachment) {
            Storage::disk('public')->delete('attachments/' . $attachment->file_name);
            $attachment->delete();
        }

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket supprimé avec succès');
    }

    /**
     * Supprimer une pièce jointe
     */
    public function deleteAttachment(TicketAttachment $attachment)
    {
        Storage::disk('public')->delete('attachments/' . $attachment->file_name);
        $attachment->delete();

        return back()->with('success', 'Pièce jointe supprimée avec succès');
    }
}
