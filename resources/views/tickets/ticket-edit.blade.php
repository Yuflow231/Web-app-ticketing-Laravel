@extends('layout.main')

@section('title')
    <title>Edit Ticket - Ticketing App</title>
@endsection

@section('resources')
    <script src="{{ asset("utils/js/side-bar.js") }}" defer></script>
    @php use Illuminate\Support\Facades\Storage; @endphp
@endsection

@section('content')
    @include('layout.nav')

    @php
        $hasPerms = $user->hasTicketRole($ticket->id, ['Ticket Creator', 'Tester']) || $user->isAdmin()
    @endphp
    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1>Edit Ticket #{{ $ticket->id }}</h1>
        </header>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1rem; padding: 1rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: var(--radius-md); color: #155724;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1rem; padding: 1rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: var(--radius-md); color: #721c24;">
                <ul style="margin: 0; padding-left: 1.2rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="ticket-form" method="POST" action="{{ route('tickets.ticket-update', $ticket->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Hidden inputs for workers --}}
            <div id="hidden-worker-inputs">
                @foreach($ticket->workers as $worker)
                    <input type="hidden" name="workers[]" value="{{ $worker->id }}">
                    <input type="hidden" name="worker_roles[]" value="{{ $worker->pivot->role }}">
                @endforeach
            </div>

            <div class="detail-container">
                <section class="detail-card">
                    <h2>Ticket Information</h2>

                    {{-- Ticket Title --}}
                    <div class="form-item-stacked">
                        <label for="ticket-title">Ticket Title *</label>
                        <input type="text" id="ticket-title" name="name" value="{{ old('name', $ticket->name) }}" placeholder="Ticket title" required>
                        @error('name')
                            <p style="color: red; font-size: var(--font-size-sm); margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Associated Project (Read-only) --}}
                    <div class="form-item-stacked">
                        <label>Associated Project</label>
                        <input type="text" value="{{ $ticket->project->name }}" disabled style="background: #f3f4f6; cursor: not-allowed;">
                        <input type="hidden" name="project_id" value="{{ $ticket->project_id }}">
                        <p style="font-size: var(--font-size-sm); color: var(--text-secondary); margin-top: 0.25rem;">
                            Project cannot be changed after creation
                        </p>
                    </div>

                    {{-- Description --}}
                    <div class="form-item-stacked">
                        <label for="ticket-description">Description</label>
                        <textarea id="ticket-description" name="description" rows="6" placeholder="Ticket description">{{ old('description', $ticket->description) }}</textarea>
                        @error('description')
                            <p style="color: red; font-size: var(--font-size-sm); margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Time Fields --}}
                    <div class="form-2elements">
                        <div class="form-item-stacked">
                            <label for="spent-time">Your Time Spent</label>
                            <input type="number" id="spent-time" name="user_spent_time" value="{{ old('user_spent_time', $user?->pivot?->spent_time ?? 0) }}" placeholder="0" min="0" step="1" required
                            @disabled(!$user->isInTicketTeam($ticket->id))>
                            <p style="font-size: var(--font-size-sm); color: var(--text-secondary); margin-top: 0.25rem;">
                                Updates parent project's spent time
                            </p>
                        </div>

                        <div class="form-item-stacked">
                            <label for="estimated-time">Estimated Time</label>
                            <input type="number" id="estimated-time" name="estimated_time" value="{{ old('estimated_time', $ticket->estimated_time) }}" placeholder="0" min="0" step="1"
                                @disabled(!$hasPerms)>
                            <p style="font-size: var(--font-size-sm); color: var(--text-secondary); margin-top: 0.25rem;">
                                Updates parent project's estimated time
                            </p>
                        </div>
                    </div>
                </section>

                <div class="detail-side">
                    {{-- Attachments --}}
                    <section class="detail-card">
                        <div class="form-item-stacked">
                            <label>Attached Files</label>

                            {{-- Hidden inputs for attachments to delete --}}
                            <div id="hidden-delete-attachments"></div>

                            {{-- Current Attachments --}}
                            @if($ticket->attachments->isNotEmpty())
                                <div style="margin-bottom: var(--spacing-sm);">
                                    <p style="font-weight: bold; margin-bottom: 0.5rem;">Current Files:</p>
                                    <div id="current-attachments">
                                        @foreach($ticket->attachments as $attachment)
                                            <div class="attachment-item" data-attachment-id="{{ $attachment->id }}" style="background: #f9fafb; border: 1px solid #ddd; border-radius: var(--radius-md); padding: var(--spacing-sm); margin-bottom: 0.5rem; display: flex; align-items: center; gap: var(--spacing-sm);">
                                                <div style="flex: 1; min-width: 0;">
                                                    <p style="margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        {{ basename($attachment->file_name) }}
                                                    </p>
                                                </div>
                                                <div style="display: flex; gap: var(--spacing-xs);">
                                                    <a href="{{ Storage::url($attachment->file_name) }}" target="_blank" class="btn btn--outline" style="padding: 0.5rem;">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn--danger" style="padding: 0.5rem;" onclick="markAttachmentForDeletion({{ $attachment->id }})">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Upload New Attachments --}}
                            <div id="drop-zone" style="margin-bottom: var(--spacing-sm);">
                                <p>Drag and drop new files or click to select (max 64MB per file)</p>
                                <input type="file" id="drop-file" name="attachments[]" multiple style="display: none;">
                            </div>
                            <ul id="file-list"></ul>
                        </div>
                    </section>
                    {{-- Classification --}}
                    <section class="detail-card">
                        <h2>Classification</h2>

                        <div class="form-item-stacked">
                            <label for="ticket-status">Status *</label>
                            <select id="ticket-status" name="status" required>
                                <option value="New" {{ old('status', $ticket->status) == 'New' ? 'selected' : '' }}>New</option>
                                <option value="In Progress" {{ old('status', $ticket->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="On Hold" {{ old('status', $ticket->status) == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Completed" {{ old('status', $ticket->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Closed" {{ old('status', $ticket->status) == 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        <div class="form-item-stacked">
                            <label for="ticket-priority">Priority *</label>
                            <select id="ticket-priority" name="priority" required>
                                <option value="Low" {{ old('priority', $ticket->priority) == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ old('priority', $ticket->priority) == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ old('priority', $ticket->priority) == 'High' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>

                        @if(auth()->user()->isAdmin())
                            <div class="form-item-stacked">
                                <label for="ticket-type">Type *</label>
                                <select id="ticket-type" name="type" required>
                                    <option value="Included" {{ old('type', $ticket->type) == 'Included' ? 'selected' : '' }}>Included</option>
                                    <option value="Billed" {{ old('type', $ticket->type) == 'Billed' ? 'selected' : '' }}>Billed</option>
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="type" value="{{ $ticket->type }}">
                        @endif
                    </section>
                </div>

                {{-- Workers Management --}}
                <section class="detail-card full-width">
                    <h2>Assigned Workers</h2>

                    <div id="selected-workers" class="workers-selector" style="padding: var(--spacing-sm); border: 1px solid #ddd; border-radius: var(--radius-md); cursor: pointer; transition: all 0.2s; min-height: 100px;" @if($hasPerms) onclick="document.getElementById('workers-modal').showModal()" @endif>
                        <div id="workers-display">
                            @if($ticket->workers->count() > 0)
                                @foreach($ticket->workers as $worker)
                                    <div class="user-profile-inline" style="margin-bottom: var(--spacing-xs);">
                                        <img src="{{ $worker->profile_pic ? Storage::url($worker->profile_pic) : asset('assets/images/icon.png') }}" class="profile-pic-mini" alt="profile-pic">
                                        <div style="flex: 1; min-width: 0; margin-left: var(--spacing-sm);">
                                            <div style="font-weight: 600;">{{ $worker->full_name }}</div>
                                            <div style="font-size: var(--font-size-sm); color: var(--text-secondary);">{{ $worker->pivot->role ? $worker->pivot->role : "No role" }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                    <i class="fa-solid fa-users" style="font-size: 2rem; opacity: 0.3; margin-bottom: 0.5rem;"></i>
                                    <p>No workers assigned</p>
                                </div>
                            @endif
                        </div>
                        <div style="text-align: center; margin-top: var(--spacing-sm); padding-top: var(--spacing-sm); border-top: 1px solid #eee;">


                            @if($hasPerms)
                                <i class="fa-solid fa-user-plus" style="color: var(--primary-color); margin-right: 0.5rem;"></i>
                                <span style="color: var(--primary-color); font-weight: bold;">Click to manage workers</span>
                            @else
                                <span style="color: var(--text-secondary); font-weight: bold;">Your current role doesn't allow you to manage the ticket's workers</span>
                            @endif
                        </div>
                    </div>

                    <p style="font-size: var(--font-size-sm); color: var(--text-secondary); margin-top: 0.25rem;">
                        Click to add or remove workers
                    </p>
                </section>

                {{-- Danger Zone --}}
                <section class="detail-card full-width" style="border: 1px solid var(--danger-color);">
                    <h2 style="color: var(--danger-color);">Danger Zone</h2>

                    <div class="detail-item">
                        <label style="color: var(--danger-color); font-weight: 600;">Delete Ticket</label>
                        <p style="margin-bottom: 1rem; font-size: var(--font-size-sm);">
                            Once you delete your ticket, there is no going back. Please be certain.
                        </p>
                        <button type="button" class="btn btn--danger" onclick="document.getElementById('delete-modal').showModal()">
                            <i class="fa-solid fa-trash"></i>
                            Delete Ticket
                        </button>
                    </div>
                </section>
            </div>

            {{-- Action Buttons --}}
            <div style="display: flex; gap: var(--spacing-md); justify-content: center; margin-top: 2rem;">
                <a href="{{ route('tickets.ticket-details', $ticket->id) }}" class="btn btn--outline">
                    <i class="fa-solid fa-xmark"></i>
                    Cancel
                </a>
                <button type="submit" id="save-btn" class="btn">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </main>
@endsection

@section('modal')
    {{-- Workers Management Modal --}}
    <dialog id="workers-modal" class="modal-container" style="max-width: 700px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="margin: 0;">Manage Ticket Workers</h2>
            <button type="button" class="icon" onclick="document.getElementById('workers-modal').close()" style="font-size: 1.5rem; color: var(--text-secondary);">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Search Bar --}}
        <div class="form-item-stacked" style="margin-bottom: 1rem;">
            <input type="text" id="worker-search" placeholder="Search by name or email..." style="width: 100%;">
        </div>

        {{-- Users List (only project team members) --}}
        <div id="users-list" class="modal-list">
            @foreach($ticket->project->teamMembers as $user)
                <div class="modal-list-selectable worker-item" onclick="toggleWorker(this)"
                     data-user-id="{{ $user->id }}"
                     data-user-name="{{ $user->full_name }}"
                     data-user-email="{{ $user->email }}"
                     data-user-avatar="{{ $user->profile_pic ? Storage::url($user->profile_pic) : asset('assets/images/icon.png') }}">
                    <input type="checkbox" class="worker-checkbox" value="{{ $user->id }}" {{ $ticket->workers->contains($user->id) ? 'checked' : '' }} style="margin-right: var(--spacing-sm); cursor: pointer; width: 1rem; height: 1rem;">

                    <img src="{{ $user->profile_pic ? Storage::url($user->profile_pic) : asset('assets/images/icon.png') }}" class="profile-pic-mini" alt="profile-pic">
                    <div style="flex: 1; min-width: 0;">
                        <div class="modal-list-name">{{ $user->full_name }}</div>
                        <div class="modal-list-subname">{{ $user->email }}</div>
                    </div>

                    {{-- Role Input --}}
                    <select class="worker-role-select" onclick="event.stopPropagation()" style="padding: 0.25rem 0.5rem; border: 1px solid #ddd; border-radius: var(--radius-sm); width: 150px;"{{ $ticket->workers->contains($user->id) ? '' : 'disabled' }}>
                        @php
                            $currentRole = $ticket->workers->find($user->id)?->pivot->role ?? '';
                        @endphp
                        <option value="">No role</option>
                        <option value="Ticket Creator" {{ $currentRole == 'Ticket Creator' ? 'selected' : '' }}>Ticket Creator</option>
                        <option value="Developer" {{ $currentRole == 'Developer' ? 'selected' : '' }}>Developer</option>
                        <option value="Tester" {{ $currentRole == 'Tester' ? 'selected' : '' }}>Tester</option>
                        <option value="Helper" {{ $currentRole == 'Helper' ? 'selected' : '' }}>Helper</option>
                        <option value="Reviewer" {{ $currentRole == 'Reviewer' ? 'selected' : '' }}>Reviewer</option>
                        <option value="Designer" {{ $currentRole == 'Designer' ? 'selected' : '' }}>Designer</option>
                    </select>

                    @if($user->isAdmin())
                        <span class="badge blue" style="margin-left: var(--spacing-xs);">Admin</span>
                    @endif
                </div>
            @endforeach
        </div>

        <div style="margin-top: 1rem; display: flex; justify-content: space-evenly;">
            <button type="button" class="btn btn--outline" onclick="document.getElementById('workers-modal').close()">Close</button>
            <button type="button" class="btn" onclick="saveWorkerChanges()">Apply Changes</button>
        </div>
    </dialog>

    {{-- Delete ticket modal --}}
    <dialog id="delete-modal" class="modal-container">
        <h2>Delete ticket</h2>
        <p style="margin-bottom: 1rem; color: var(--text-secondary);">You are about to delete <strong> {{ $ticket->name }} </strong> </p>
        <p style="margin-bottom: 1rem; color: var(--text-secondary);text-align: center">This action is irreversible. </p>
        <form method="POST" action=" {{ route('tickets.ticket-destroy', $ticket->id) }}" id="delete-form">
            @csrf
            @method('DELETE')
            <div class="inline-elements">
                <button type="button" class="btn btn--outline" onclick="document.getElementById('delete-modal').close()">Cancel</button>
                <button type="submit" class="btn btn--danger">Confirm deletion</button>
            </div>
        </form>
    </dialog>
@endsection

@section('js_page')
    <script type="module">
        import * as FormVerifier from "{{ asset('utils/js/form-verifs.js') }}";
        import * as DragDrop from "{{ asset('utils/js/drag-n-drop.js') }}";
        import Toast from "{{ asset('utils/js/toast.js') }}";

        // Prevent form submit on Enter
        document.getElementById('ticket-form').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.type !== 'submit') {
                e.preventDefault();
                return false;
            }
        });

        // Form Validation
        const formTitle = document.getElementById('ticket-title');
        const formStatus = document.getElementById('ticket-status');
        const formPriority = document.getElementById('ticket-priority');
        const formSpentTime = document.getElementById('spent-time');

        let fileInput = document.getElementById('drop-file');
        let fileDrop = document.getElementById('drop-zone');

        let canPress = true;
        const saveButton = document.getElementById('save-btn');

        saveButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (canPress) {
                verifyForm();
            }
        });

        function verifyForm() {
            let formValidation = true;
            FormVerifier.resetFormState([formTitle, formStatus, formPriority, formSpentTime]);

            formValidation &= FormVerifier.checkField(formTitle, formTitle, [FormVerifier.verifyEmptyness("Please enter a ticket title")]);
            formValidation &= FormVerifier.checkField(formStatus, formStatus, [FormVerifier.verifyEmptyness("Please select a status")]);
            formValidation &= FormVerifier.checkField(formPriority, formPriority, [FormVerifier.verifyEmptyness("Please select a priority")]);
            formValidation &= FormVerifier.checkField(formSpentTime, formSpentTime, [FormVerifier.verifyEmptyness("Please enter time spent")]);


            // Sync files first so fileInput.files is populated before size check
            DragDrop.syncFilesToInput();

            // Fail-safe: check file sizes before submit (max 64MB per file)
            const MAX_FILE_SIZE = 64 * 1024 * 1024; // 64MB in bytes
            let filesTooLarge = [];

            if (fileInput.files.length > 0) {
                Array.from(fileInput.files).forEach(file => {
                    if (file.size > MAX_FILE_SIZE) {
                        filesTooLarge.push(`${file.name} (${(file.size / 1024 / 1024).toFixed(1)} MB)`);
                    }
                });
            }

            if (filesTooLarge.length > 0) {
                Toast("These files are too large (max 64MB)", "error");
                fileDrop.classList.add("error-field");
                formValidation = false;
            }


            if (formValidation) {
                canPress = false;
                FormVerifier.validateForm("Saving changes...");
                document.getElementById('ticket-form').submit();
            }
        }

        // Workers Management
        window.toggleWorker = function(element) {
            const checkbox = element.querySelector('.worker-checkbox');
            const roleInput = element.querySelector('.worker-role-select');

            checkbox.checked = !checkbox.checked;
            roleInput.disabled = !checkbox.checked;
        };

        window.saveWorkerChanges = function() {
            const selectedWorkers = [];
            const checkboxes = document.querySelectorAll('.worker-checkbox:checked');

            checkboxes.forEach(checkbox => {
                const item = checkbox.closest('.worker-item');
                const roleSelect = item.querySelector('.worker-role-select');

                selectedWorkers.push({
                    id: item.dataset.userId,
                    name: item.dataset.userName,
                    avatar: item.dataset.userAvatar,
                    role: roleSelect.value
                });
            });

            // Update display
            updateWorkersDisplay(selectedWorkers);

            // Update hidden inputs
            updateHiddenWorkerInputs(selectedWorkers);

            // Close modal
            document.getElementById('workers-modal').close();
        };

        function updateHiddenWorkerInputs(workers) {
            const container = document.getElementById('hidden-worker-inputs');
            container.innerHTML = '';

            workers.forEach(worker => {
                // Worker ID
                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'workers[]';
                inputId.value = worker.id;
                container.appendChild(inputId);

                // Worker Role
                const inputRole = document.createElement('input');
                inputRole.type = 'hidden';
                inputRole.name = 'worker_roles[]';
                inputRole.value = worker.role;
                container.appendChild(inputRole);
            });

            console.log('Workers updated:', workers.length, 'workers');
        }

        function updateWorkersDisplay(selectedWorkers) {
            const display = document.getElementById('workers-display');

            if (selectedWorkers.length === 0) {
                display.innerHTML = `
                    <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                        <i class="fa-solid fa-users" style="font-size: 2rem; opacity: 0.3; margin-bottom: 0.5rem;"></i>
                        <p>No workers assigned</p>
                    </div>
                `;
            } else {
                display.innerHTML = selectedWorkers.map(worker => `
                    <div class="user-profile-inline" style="margin-bottom: var(--spacing-xs);">
                        <img src="${worker.avatar}" class="profile-pic-mini" alt="profile-pic">
                        <div style="flex: 1; min-width: 0; margin-left: var(--spacing-sm);">
                            <div style="font-weight: 600;">${worker.name}</div>
                            <div style="font-size: var(--font-size-sm); color: var(--text-secondary);">${worker.role || 'No role'}</div>
                        </div>
                    </div>
                `).join('');
            }
        }

        // Worker Search
        const workerSearch = document.getElementById('worker-search');
        const workerItems = document.querySelectorAll('.worker-item');

        workerSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            workerItems.forEach(item => {
                const name = item.dataset.userName.toLowerCase();
                const email = item.dataset.userEmail.toLowerCase();

                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>


    <script type="module">
        // Attachments to delete (collected at form submit)
        window.markAttachmentForDeletion = function(attachmentId) {
            const attachmentItem = document.querySelector(`.attachment-item[data-attachment-id="${attachmentId}"]`);

            if (!attachmentItem) return;

            // Add hidden input to mark for deletion
            const hiddenContainer = document.getElementById('hidden-delete-attachments');
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'delete_attachments[]';
            hiddenInput.value = attachmentId;
            hiddenInput.id = `delete-attachment-${attachmentId}`;
            hiddenContainer.appendChild(hiddenInput);

            // Mark visually as "to be deleted"
            attachmentItem.style.opacity = '0.5';
            attachmentItem.style.backgroundColor = '#fee';
            attachmentItem.style.borderColor = '#fcc';
        };
    </script>
@endsection
