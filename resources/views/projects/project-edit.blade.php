@extends('layout.main')

@section('title')
    <title>Edit Project - Ticketing App</title>
@endsection

@section('resources')
    <script src="{{ asset("utils/js/side-bar.js") }}" defer></script>
    @php use Illuminate\Support\Facades\Storage; @endphp
@endsection

@section('content')
    @include('layout.nav')

    @php
        $hasPerms = $currentUser->hasProjectRole($project->id, ['Owner']) || $currentUser->isAdmin()
    @endphp

    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1>Edit Project: {{ $project->name }}</h1>
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

        <form id="project-form" method="POST" action="{{ route('projects.project-update', $project->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div id="hidden-team-inputs">
                @foreach($project->teamMembers as $member)
                    <input type="hidden" name="team_members[]" value="{{ $member->id }}">
                    <input type="hidden" name="team_roles[]" value="{{ $member->pivot->role }}">
                @endforeach
            </div>

            <div class="detail-container">
                <section class="detail-card">
                    <h2>Project Information</h2>

                    {{-- Project Name & Status --}}
                    <div class="form-2elements">
                        <div class="form-item-stacked">
                            <label for="project-name">Project Name *</label>
                            <input type="text" id="project-name" name="name" value="{{ old('name', $project->name) }}" placeholder="Project name" required>
                            @error('name')
                            <p style="color: red; font-size: var(--font-size-sm); margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-item-stacked">
                            <label for="project-status">Status *</label>
                            <select id="project-status" name="status" required>
                                <option value="New" {{ old('status', $project->status) == 'New' ? 'selected' : '' }}>New</option>
                                <option value="In Progress" {{ old('status', $project->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="On Hold" {{ old('status', $project->status) == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Completed" {{ old('status', $project->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Closed" {{ old('status', $project->status) == 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="form-item-stacked">
                        <label for="project-description">Description</label>
                        <textarea id="project-description" name="description" rows="6" placeholder="Project description">{{ old('description', $project->description) }}</textarea>
                    </div>

                    {{-- Estimated time and Closing Date --}}
                    <div class="form-2elements">
                        <div class="form-item-stacked">
                            <label for="closing-date">Closing Date</label>
                            <input type="date" id="closing-date" name="closing_date"
                                   value="{{ old('closing_date', optional($project->closing_date)->format('Y-m-d')) }}" @disabled(!$hasPerms)>
                        </div>

                        <div class="form-item-stacked">
                            <label for="estimated-time">Estimated Time</label>
                            <input type="number" id="estimated-time" name="estimated_time"
                                value="{{ old('estimated_time', $project->estimated_time) }}" placeholder="0" min="0" step="1" @disabled(!$hasPerms)>
                        </div>
                    </div>

                    {{-- Read-only fields --}}
                    <div class="form-2elements">
                        <div class="form-item-stacked">
                            <label>Creation Date</label>
                            <input type="text" value="{{ $project->creation_date ? $project->creation_date->format('F j, Y') : 'N/A' }}" disabled style="background: #f3f4f6; cursor: not-allowed;">
                        </div>

                        <div class="form-item-stacked">
                            <label>Time Spent</label>
                            <input type="text" value="{{ $project->spent_time }} hours" disabled style="background: #f3f4f6; cursor: not-allowed;">
                            <p style="font-size: var(--font-size-sm); color: var(--text-secondary); margin-top: 0.25rem;">
                                Calculated from tickets
                            </p>
                        </div>
                    </div>
                </section>

                <div class="detail-side">
                    {{-- Contract File --}}
                    <section class="detail-card">
                        <div class="form-item-stacked">
                            <label>Contract File</label>

                            {{-- Current Contract --}}
                            @if($project->contract)
                                <div style="background: #f9fafb; border: 1px solid #ddd; border-radius: var(--radius-md); padding: var(--spacing-sm); margin-bottom: var(--spacing-sm);">
                                    <div style="display: flex; align-items: center; gap: var(--spacing-sm);">
                                        <div style="flex: 1; min-width: 0;">
                                            <p style="font-weight: bold; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ basename($project->contract) }}
                                            </p>
                                        </div>
                                        <div style="display: flex; gap: var(--spacing-xs);">
                                            <a href="{{ Storage::url($project->contract) }}" target="_blank" class="btn btn--outline" style="padding: 0.5rem;">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn--danger" onclick="confirmRemoveContract()">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p style="color: var(--text-secondary); font-style: italic; margin-bottom: var(--spacing-sm);">
                                    No contract file attached
                                </p>
                            @endif

                            {{-- Upload New Contract --}}
                            <div id="drop-zone" style="margin-bottom: var(--spacing-sm);">
                                <p>Drag and drop a new contract or click to select</p>
                                <input type="file" id="drop-file" name="contract" style="display: none;">
                            </div>
                            <input type="hidden" id="remove-contract" name="remove_contract" value="0">
                            <ul id="file-list"></ul>
                        </div>
                    </section>

                    {{-- Statistics (Read-only) --}}
                    <section class="detail-card">
                        <h2>Statistics</h2>

                        <div class="detail-item">
                            <label>Total Tickets</label>
                            <p style="font-size: 1.5rem; font-weight: 600; color: var(--primary-color);">
                                {{ $project->tickets->count() }}
                            </p>
                        </div>

                        <div class="detail-item">
                            <label>Active Tickets</label>
                            <p style="font-size: 1.25rem; font-weight: 600;">
                                {{ $project->tickets->whereIn('status', ['New', 'In Progress'])->count() }}
                            </p>
                        </div>

                        <div class="detail-item">
                            <label>Completed Tickets</label>
                            <p style="font-size: 1.25rem; font-weight: 600;">
                                {{ $project->tickets->whereIn('status', ['Completed', 'Closed'])->count() }}
                            </p>
                        </div>
                    </section>
                </div>


                {{-- Project Team --}}
                <section class="detail-card full-width">
                    <h2>Project Team</h2>

                    <div id="selected-team" class="team-selector" style="padding: var(--spacing-sm); border: 1px solid #ddd; border-radius: var(--radius-md); cursor: pointer; transition: all 0.2s; min-height: 100px;" @if($hasPerms) onclick="document.getElementById('team-modal').showModal()" @endif>
                        <div id="team-members-display">
                            @if($project->teamMembers->count() > 0)
                                @foreach($project->teamMembers as $member)
                                    <div class="user-profile-inline" style="margin-bottom: var(--spacing-xs);">
                                        <img src="{{ $member->profile_pic ? Storage::url($member->profile_pic) : asset('assets/images/icon.png') }}" class="profile-pic-mini" alt="profile-pic">
                                        <div style="flex: 1; min-width: 0; margin-left: var(--spacing-sm);">
                                            <div style="font-weight: 600;">{{ $member->full_name }}</div>
                                            <div style="font-size: var(--font-size-sm); color: var(--text-secondary);">{{ $member->pivot->role }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                    <i class="fa-solid fa-users" style="font-size: 2rem; opacity: 0.3; margin-bottom: 0.5rem;"></i>
                                    <p>No team members</p>
                                </div>
                            @endif
                        </div>
                        <div style="text-align: center; margin-top: var(--spacing-sm); padding-top: var(--spacing-sm); border-top: 1px solid #eee;">
                            @if($hasPerms)
                                <i class="fa-solid fa-user-plus" style="color: var(--primary-color); margin-right: 0.5rem;"></i>
                                <span style="color: var(--primary-color); font-weight: bold;">Click to manage team</span>
                            @else
                                <span style="color: var(--text-secondary); font-weight: bold;">Your current role doesn't allow you to manage the project's team</span>
                            @endif
                        </div>
                    </div>

                    <p style="font-size: var(--font-size-sm); color: var(--text-secondary); margin-top: 0.25rem;">
                        Click to add or remove team members
                    </p>
                </section>

                {{-- Danger Zone --}}
                <section class="detail-card full-width" style="border: 1px solid var(--danger-color);">
                    <h2 style="color: var(--danger-color);">Danger Zone</h2>

                    <div class="detail-item">
                        <label style="color: var(--danger-color); font-weight: 600;">Delete Project</label>
                        <p style="margin-bottom: 1rem; font-size: var(--font-size-sm);">
                            Once you delete your project, there is no going back. Please be certain.
                        </p>
                        <button type="button" class="btn btn--danger" @disabled(!$hasPerms) onclick="document.getElementById('delete-modal').showModal()">
                            <i class="fa-solid fa-trash"></i>
                            Delete Project
                        </button>
                    </div>
                </section>
            </div>

            {{-- Action Buttons --}}
            <div style="display: flex; gap: var(--spacing-md); justify-content: center; margin-top: 2rem;">
                <a href="{{ route('projects.project-details', $project->id) }}" class="btn btn--outline">
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
    {{-- Team Management Modal --}}
    <dialog id="team-modal" class="modal-container" style="max-width: 700px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="margin: 0;">Manage Project Team</h2>
            <button type="button" class="icon" onclick="document.getElementById('team-modal').close()" style="font-size: 1.5rem; color: var(--text-secondary);">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Search Bar --}}
        <div class="form-item-stacked" style="margin-bottom: 1rem;">
            <input type="text" id="team-search" placeholder="Search by name or email..." style="width: 100%;">
        </div>

        {{-- Users List --}}
        <div id="users-list" class="modal-list">
            @foreach($users as $user)
                <div class="modal-list-selectable team-member-item" data-user-id="{{ $user->id }}" data-user-name="{{ $user->full_name }}" data-user-email="{{ $user->email }}" data-user-avatar="{{ $user->profile_pic ? Storage::url($user->profile_pic) : asset('assets/images/icon.png') }}" onclick="toggleTeamMember(this)">
                    <input type="checkbox" class="team-checkbox" name="team_members[]" style="margin-right: var(--spacing-sm); cursor: pointer; width: 1rem; height: 1rem;" value="{{ $user->id }}" {{ $project->teamMembers->contains($user->id) ? 'checked' : '' }}>

                    <img src="{{ $user->profile_pic ? Storage::url($user->profile_pic) : asset('assets/images/icon.png') }}" class="profile-pic-mini" alt="profile-pic">

                    <div style="flex: 1; min-width: 0;">
                        <div class="modal-list-name">{{ $user->full_name }}</div>
                        <div class="modal-list-subname">{{ $user->email }}</div>
                    </div>

                    {{-- Role Selection --}}
                    <select class="team-role-select" name="team_roles[]" onclick="event.stopPropagation()" style="padding: 0.25rem 0.5rem; border: 1px solid #ddd; border-radius: var(--radius-sm);"{{ $project->teamMembers->contains($user->id) ? '' : 'disabled' }}>
                        @php
                            $currentRole = $project->teamMembers->find($user->id)?->pivot->role ?? 'Maintainer';
                        @endphp
                        <option value="Owner" {{ $currentRole == 'Owner' ? 'selected' : '' }}>Owner</option>
                        <option value="Maintainer" {{ $currentRole == 'Maintainer' ? 'selected' : '' }}>Maintainer</option>
                    </select>

                    @if($user->isAdmin())
                        <span class="badge blue" style="margin-left: var(--spacing-xs);">Admin</span>
                    @endif
                </div>
            @endforeach
        </div>

        <div style="margin-top: 1rem; display: flex; justify-content: space-evenly;">
            <button type="button" class="btn btn--outline" onclick="document.getElementById('team-modal').close()">Close</button>
            <button type="button" class="btn" onclick="saveTeamChanges()">Apply Changes</button>
        </div>
    </dialog>

    {{-- Delete project modal --}}
    <dialog id="delete-modal" class="modal-container">
        <h2>Delete project</h2>
        <p style="margin-bottom: 1rem; color: var(--text-secondary);">You are about to delete <strong> {{ $project->name }} </strong> </p>
        <p style="margin-bottom: 1rem; color: var(--text-secondary);text-align: center">This action is irreversible. </p>
        <form method="POST" action=" {{ route('projects.project-destroy', $project->id) }}" id="delete-form">
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

        // Prevent form submit on Enter
        document.getElementById('project-form').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                return false;
            }
        });


        // Form Validation
        const formName = document.getElementById('project-name');

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
            FormVerifier.resetFormState([formName]);

            formValidation &= FormVerifier.checkField(formName, formName, [FormVerifier.verifyEmptyness("Please enter a project name")]);

            if (formValidation) {
                canPress = false;
                FormVerifier.validateForm("Saving changes...");
                document.getElementById('project-form').submit();
            }
        }

        // Contract Removal
        window.confirmRemoveContract = function() {
            document.getElementById('remove-contract').value = '1';

            // Visual feedback
            const contractDisplay = document.querySelector('[style*="background: #f9fafb"]');
            if (contractDisplay) {
                contractDisplay.style.opacity = '0.5';
                contractDisplay.innerHTML = '<p style="text-align: center; color: var(--text-secondary);">Contract will be removed on save</p>';
            }
        };

        // Team Management
        window.toggleTeamMember = function(element) {
            const checkbox = element.querySelector('.team-checkbox');
            const roleSelect = element.querySelector('.team-role-select');

            checkbox.checked = !checkbox.checked;
            roleSelect.disabled = !checkbox.checked;
        };

        window.saveTeamChanges = function() {
            const selectedMembers = [];
            const checkboxes = document.querySelectorAll('.team-checkbox:checked');

            checkboxes.forEach(checkbox => {
                const item = checkbox.closest('.team-member-item');
                const roleSelect = item.querySelector('.team-role-select');

                selectedMembers.push({
                    id: item.dataset.userId,
                    name: item.dataset.userName,
                    avatar: item.dataset.userAvatar,
                    role: roleSelect.value
                });
            });

            // Update display
            updateTeamDisplay(selectedMembers);

            // Update hidden inputs in the form
            updateHiddenTeamInputs(selectedMembers);

            // Close modal
            document.getElementById('team-modal').close();
        };

        // Update hidden inputs
        function updateHiddenTeamInputs(members) {
            const container = document.getElementById('hidden-team-inputs');
            container.innerHTML = ''; // Clear existing

            members.forEach(member => {
                // Add hidden input for user ID
                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'team_members[]';
                inputId.value = member.id;
                container.appendChild(inputId);

                // Add hidden input for role
                const inputRole = document.createElement('input');
                inputRole.type = 'hidden';
                inputRole.name = 'team_roles[]';
                inputRole.value = member.role;
                container.appendChild(inputRole);
            });
        }

        function updateTeamDisplay(selectedMembers) {
            const display = document.getElementById('team-members-display');

            if (selectedMembers.length === 0) {
                display.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                <i class="fa-solid fa-users" style="font-size: 2rem; opacity: 0.3; margin-bottom: 0.5rem;"></i>
                <p>No team members</p>
            </div>
        `;
            } else {
                display.innerHTML = selectedMembers.map(member => `
            <div class="user-profile-inline" style="margin-bottom: var(--spacing-xs);">
                <img src="${member.avatar}" class="profile-pic-mini" alt="profile-pic">
                <div style="flex: 1; min-width: 0; margin-left: var(--spacing-sm);">
                    <div style="font-weight: 600;">${member.name}</div>
                    <div style="font-size: var(--font-size-sm); color: var(--text-secondary);">${member.role}</div>
                </div>
            </div>
        `).join('');
            }
        }

        // Team Search
        const teamSearch = document.getElementById('team-search');
        const teamItems = document.querySelectorAll('.team-member-item');

        teamSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            teamItems.forEach(item => {
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
@endsection
