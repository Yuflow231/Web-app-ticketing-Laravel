<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Project;
use App\Models\User;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketsController extends Controller
{
    /**
     * Show the ticket list
     */
    public function index()
    {
        $user = Auth::user();

        $query = Ticket::with(['project', 'workers']);

        if (!$user->isAdmin()) {
            $query->whereRelation('workers', 'users.id', $user->id);
        }

        $tickets = $query->latest()->get();

        return view('tickets.tickets', compact('tickets'));
    }

    /**
     * Show the creation from
     */
    public function showCreate()
    {
        $user = Auth::user();
        if($user->isAdmin()){
            $projects = Project::all();
        }
        else{
            $projects = $user->projects;
        }

        return view('tickets.ticket-creation', compact('projects'));
    }

    /**
     * Create a ticket
     */
    public function store(Request $request)
    {
        if (!Auth::user()?->isAdmin() && !$request->filled('type')) {
            $request->merge(['type' => 'Included']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'status' => 'required|in:New,In Progress,On Hold,Completed,Closed',
            'priority' => 'required|in:High,Medium,Low',
            'type' => 'required|in:Billed,Included',
            'attachments.*' => 'nullable|file|max:65536',
        ]);

        $validated['estimated_time'] = 0;
        $validated['spent_time'] = 0;

        $ticket = Ticket::create($validated);

        // Add the creator as the "Ticket Creator"
        if (!$ticket->workers->contains(Auth::id())) {
            $ticket->workers()->attach(Auth::id(), [
                'role' => 'Ticket Creator',
                'spent_time' => 0,
            ]);
        }

        // Handle attachments — store with readable name, save full path in DB
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension    = $file->getClientOriginalExtension();

                $safeBaseName = Str::of($originalName)
                    ->ascii()
                    ->replaceMatches('/[^A-Za-z0-9]+/', '_')
                    ->trim('_')
                    ->value();

                if ($safeBaseName === '') {
                    $safeBaseName = 'attachment';
                }

                $fileName = $safeBaseName . '_' . now()->format('Ymd_His') . '.' . $extension;

                // Store in attachments/ folder — storeAs returns "attachments/fileName"
                $path = $file->storeAs('attachments', $fileName, 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'file_name' => $path, // full path: "attachments/fileName.ext"
                ]);
            }
        }

        $ticket->project->calculatePercent();

        return redirect()->route('tickets.ticket-details', $ticket->id)
            ->with('success', 'Ticket created successfully.');
    }

    /**
     * Show ticket details from an id
     */
    public function details(int $id)
    {
        $user = Auth::user();

        $ticket = Ticket::with(['project.teamMembers', 'workers', 'attachments'])->find($id);
        if (!$ticket) {
            return redirect()->route('tickets.tickets')
                ->with('info', 'Ticket not found or no longer exists.');
        }

        $isMember = $ticket->workers()->where('users.id', $user->id)->exists();
        $isAdmin = $user->isAdmin();

        if ( !$isMember && !$isAdmin ) {
            return redirect()->route('tickets.tickets')
                ->with('error', 'Access denied.');
        }

        return view('tickets.ticket-details', compact('ticket'));
    }

    /**
     * Show the edit form
     */
    public function showEdit(int $id)
    {
        $user = Auth::user();

        $ticket = Ticket::with(['project.teamMembers', 'workers', 'attachments'])->find($id);
        if (!$ticket) {
            return redirect()->route('tickets.tickets')
                ->with('info', 'Ticket not found or no longer exists.');
        }

        $isMember = $ticket->workers()->where('users.id', $user->id)->exists();
        $isAdmin = $user->isAdmin();

        if ( !$isMember && !$isAdmin ) {
            return redirect()->route('tickets.tickets')
                ->with('error', 'Access denied.');
        }

        // get the authenticated user with the ticket relation if not an admin
        if ($isMember) {
            $user = $ticket->workers->find($user->id);
        }

        return view('tickets.ticket-edit', compact('ticket', 'user'));
    }

    /**
     * Update the ticket
     */
    public function update(Request $request, int $ticketId)
    {
        $ticket = Ticket::with(['project.teamMembers', 'workers', 'attachments'])->find($ticketId);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'status' => 'required|in:New,In Progress,On Hold,Completed,Closed',
            'priority' => 'required|in:High,Medium,Low',
            'type' => 'required|in:Billed,Included',
            'estimated_time' => 'nullable|numeric|min:0',
            'user_spent_time' => 'numeric|min:0',
            'workers' => 'nullable|array',
            'workers.*' => 'exists:users,id',
            'worker_roles' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:65536',
        ]);

        // Extract the user spent time so we can apply it to the pivot table later
        $userSpentTime = $request->input('user_spent_time');
        unset($validated['user_spent_time']);

        $ticket->update($validated);

        // Update the workers and their pivot data
        if ($request->has('workers') && is_array($request->workers) && count($request->workers) > 0) {
            $syncData = [];
            // Fetch existing pivot data so we don't wipe out other users' tracked time
            $existingWorkers = $ticket->workers->pluck('pivot.spent_time', 'id')->toArray();

            foreach ($request->workers as $index => $userId) {
                $role = $request->worker_roles[$index] ?? '';
                // Keep the existing spent_time, or default to 0 if it's a new worker
                $spentTime = $existingWorkers[$userId] ?? 0;
                // If the looped worker is the currently authenticated user, update THEIR time
                if ($userId == Auth::id() && $userSpentTime !== null) {
                    $spentTime = $userSpentTime;
                }
                $syncData[$userId] = [
                    'role' => $role,
                    'spent_time' => $spentTime
                ];
            }
            $ticket->workers()->sync($syncData);
        } else {
            // Detach all workers if none selected
            $ticket->workers()->detach();
        }

        // Update the overall ticket spent_time based on the sum of all users
        $ticket->spent_time = $ticket->workers()->sum('ticket_workers.spent_time');
        $ticket->save();


        // Delete marked attachments
        if ($request->has('delete_attachments') && is_array($request->delete_attachments)) {
            foreach ($request->delete_attachments as $attachmentId) {
                $attachment = TicketAttachment::where('id', $attachmentId)
                    ->where('ticket_id', $ticket->id)  // Security: verify it belongs to this ticket
                    ->first();

                if ($attachment) {
                    // Delete file from storage
                    Storage::disk('public')->delete($attachment->file_name);
                    // Delete record from database
                    $attachment->delete();
                }
            }
        }

        // Handle the new attachments — store with readable name, save full path in DB
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension    = $file->getClientOriginalExtension();

                $safeBaseName = Str::of($originalName)
                    ->ascii()
                    ->replaceMatches('/[^A-Za-z0-9]+/', '_')
                    ->trim('_')
                    ->value();

                if ($safeBaseName === '') {
                    $safeBaseName = 'attachment';
                }

                $fileName = $safeBaseName . '_' . now()->format('Ymd_His') . '.' . $extension;
                $path = $file->storeAs('attachments', $fileName, 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'file_name' => $path,
                ]);
            }
        }

        // CRITICAL: Update parent project's times based on ALL tickets
        $ticket->project->calculateSpentTime();
        $ticket->project->calculateEstimatedTime();
        $ticket->project->calculatePercent();

        return redirect()->route('tickets.ticket-edit', $ticket->id)
            ->with('success', 'Ticket updated successfully.');
    }

    /**
     * Delete a ticket based on an id
     */
    public function destroy(int $id)
    {
        $ticket = Ticket::findOrFail($id);
        $project = $ticket->project;

        foreach ($ticket->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_name);
            $attachment->delete();
        }

        $ticket->delete();

        // CRITICAL: Update parent project's times based on ALL tickets
        $project->calculateSpentTime();
        $project->calculateEstimatedTime();
        $project->calculatePercent();

        return redirect()->route('tickets.tickets')
            ->with('success', 'Ticket deleted successfully.');
    }
}
