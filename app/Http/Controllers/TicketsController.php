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
     * Show the ticket list
     */
    public function index()
    {
        $user = Auth::user();

        $query = Ticket::with(['project', 'workers']);

        if (!$user || !$user->isAdmin()) {
            $query->whereHas('workers', function ($q) use ($user) {
                $q->where('users.id', $user?->id);
            });
        }

        $tickets = $query->latest()->paginate(15);

        return view('tickets.tickets', compact('tickets'));
    }

    /**
     * Show the creation from
     */
    public function create()
    {
        $projects = Project::all();
        $users = User::all();

        return view('tickets.ticket-creation', compact('projects', 'users'));
    }

    /**
     * Alias for create (route /ticket-creation)
     */
    public function creation()
    {
        return $this->create();
    }

    /**
     * Create a ticket
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

        // Assign the ticket workers
        if ($request->has('workers')) {
            foreach ($request->workers as $index => $userId) {
                $role = $request->worker_roles[$index] ?? '';
                $ticket->workers()->attach($userId, ['role' => $role]);
            }
        }

        // Add the creator as the "Ticket Creator" if not already a worker
        if (!$ticket->workers->contains(Auth::id())) {
            $ticket->workers()->attach(Auth::id(), ['role' => 'Ticket Creator']);
        }

        // Handle attachments
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
            ->with('success', 'Ticket created successfully.');
    }

    /**
     * Show ticket details from an id
     */
    public function details(int $id)
    {
        $user = Auth::user();
        $ticket = Ticket::with(['project', 'workers', 'attachments'])->find($id);

        if (!$ticket) {
            return redirect()->route('tickets.index')
                ->with('info', 'Ticket not found or no longer exists.');
        }

        $isMember = $ticket->project->teamMembers()->where('users.id', $user->id)->exists();
        $isAdmin = $user->isAdmin();

        if (!$isMember && !$isAdmin) {
            return redirect()->route('tickets.index')
                ->with('info', 'Access denied.');
        }

        return view('tickets.ticket-details', compact('ticket'));
    }

    /**
     * Show the edit form
     */
    public function edit(Ticket $ticket)
    {
        $projects = Project::all();
        $users = User::all();
        $ticket->load(['workers', 'attachments']);

        return view('tickets.ticket-edit', compact('ticket', 'projects', 'users'));
    }

    /**
     * Update the ticket
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

        // Update the workers
        if ($request->has('workers')) {
            $syncData = [];
            foreach ($request->workers as $index => $userId) {
                $role = $request->worker_roles[$index] ?? '';
                $syncData[$userId] = ['role' => $role];
            }
            $ticket->workers()->sync($syncData);
        }

        // Handle the new attachments
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
            ->with('success', 'Ticket updated successfully.');
    }

    /**
     * Delete a ticket
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
            ->with('success', 'Ticket deleted successfully.');
    }

    /**
     * Delete an attachment
     */
    public function deleteAttachment(TicketAttachment $attachment)
    {
        Storage::disk('public')->delete('attachments/' . $attachment->file_name);
        $attachment->delete();

        return back()->with('success', 'Attachment deleted successfully.');
    }
}
