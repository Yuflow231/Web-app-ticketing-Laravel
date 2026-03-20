@extends('layout.main')

@section('title')
    <title>Project creation - Ticketing App</title>
@endsection

@section('resources')
    <script src="{{ asset("utils/js/side-bar.js") }}" defer></script>
    @php use Illuminate\Support\Facades\Storage; @endphp
@endsection

@section('content')
    @include('layout.nav')
    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1>Project creation</h1>
        </header>

        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1rem;">
                <ul style="margin: 0; padding-left: 1.2rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="project-form" class="form-box" method="POST" action="{{ route('projects.project-store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-2elements">
                <div class="form-item-stacked">
                    <label for="project-name">Project's name *</label>
                    <input type="text" id="project-name" name="name" placeholder="Project's name" value="{{ old('name') }}" required>
                </div>

                <div class="form-item-stacked">
                    <label>Owner *</label>
                    {{-- Hidden input fo owner_id --}}
                    <input type="hidden" id="owner-id" name="owner_id" value="{{ Auth::id() }}">

                    @if(Auth::user()->isAdmin())
                        {{-- Admin: Can select the owner --}}
                        <div id="selected-owner" class="user-profile-inline modal-select" onclick="document.getElementById('owner-modal').showModal()">
                            <img id="owner-avatar" src="{{ Auth::user()->profile_pic ? Storage::url(Auth::user()->profile_pic) : asset('assets/images/icon.png') }}" class="profile-pic-mini" alt="profile-pic">
                            <span id="owner-name" style="margin-left: var(--spacing-sm)">{{ Auth::user()->full_name }}</span>
                            <i class="fa-solid fa-pencil icon" style="margin-left: auto; color: var(--primary-color);"></i>
                        </div>
                        <p style="font-size: var(--font-size-sm); color: var(--text-secondary);">Click to select a different owner</p>
                    @else
                        {{-- Guest: Lock on self --}}
                        <div class="user-profile-inline" style="padding: var(--spacing-sm); border: 1px solid #ddd; border-radius: var(--radius-md); opacity: 0.8;">
                            <img src="{{ Auth::user()->profile_pic ? Storage::url(Auth::user()->profile_pic) : asset('assets/images/icon.png') }}" class="profile-pic-mini" alt="profile-pic">
                            <span style="margin-left: var(--spacing-sm)">{{ Auth::user()->full_name }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-2elements">
                <div class="form-item-stacked">
                    <label for="end-date">Closing date</label>
                    <input type="date" id="end-date" name="closing_date" value="{{ old('closing_date') }}">
                </div>
            </div>

            <div class="form-item-stacked">
                <label for="project-status">Status *</label>
                <select id="project-status" name="status" required>
                    <option value="" selected hidden disabled>Select a status</option>
                    <option value="New" {{ old('status') == 'New' ? 'selected' : '' }}>New</option>
                    <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="On Hold" {{ old('status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                </select>
            </div>

            <div class="form-item-stacked">
                <label for="project-description">Description</label>
                <textarea id="project-description" name="description" rows="6" placeholder="Project's description">{{ old('description') }}</textarea>
            </div>

            <div class="form-item-stacked">
                <label for="drop-file">Attached files</label>
                <div id="drop-zone">
                    <p>Drag and drop files here or click to select files</p>
                    <input type="file" id="drop-file" name="contract" style="display: none;">
                </div>
                <ul id="file-list"></ul>
            </div>

            <div class="centered" style="display: flex; gap: var(--spacing-md); justify-content: center;">
                <button onclick="location.href = '{{ route('projects.projects') }}'" type="button" class="btn btn--outline">Cancel</button>
                <button id="actions" class="btn" type="submit">Create project</button>
            </div>
        </form>
    </main>
@endsection

@section('modal')
    {{-- Modal to select owner (admin only) --}}
    @if(Auth::user()->isAdmin())
        <dialog id="owner-modal" class="modal-container">
            <div style="display: flex; justify-content: space-between">
                <h2 style="margin-bottom: 0.5rem;">Select Project Owner</h2>
                <button type="button" class="icon" onclick="document.getElementById('owner-modal').close()" style="font-size: 1.5rem; color: var(--text-secondary);">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Search bar --}}
            <div class="form-item-stacked">
                <input type="text" id="owner-search" placeholder="Search by name or email...">
            </div>

            {{-- User list --}}
            <div id="users-list" class="modal-list">
                @foreach($users as $user)
                    <div
                        class="modal-list-selectable"
                        data-user-id="{{ $user->id }}"
                        data-user-name="{{ $user->full_name }}"
                        data-user-email="{{ $user->email }}"
                        data-user-avatar="{{ $user->profile_pic ? Storage::url($user->profile_pic) : asset('assets/images/icon.png') }}"
                        onclick="selectOwner(this)">
                        <img src="{{ $user->profile_pic ? Storage::url($user->profile_pic) : asset('assets/images/icon.png') }}" class="profile-pic-mini" alt="profile-pic">
                        <div style="flex: 1;">
                            <div class="modal-list-name">{{ $user->full_name }}</div>
                            <div class="modal-list-subname">{{ $user->email }}</div>
                        </div>
                        @if($user->isAdmin())
                            <span class="badge blue">Admin</span>
                        @endif
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 1rem; display: flex; justify-content: center;">
                <button type="button" class="btn btn--outline" onclick="document.getElementById('owner-modal').close()">
                    Cancel
                </button>
            </div>
        </dialog>
    @endif
@endsection

@section('js_page')
    <script type="module">
        import * as FormVerifier from "{{ asset("utils/js/form-verifs.js") }}";
        import * as DragDrop from "{{ asset("utils/js/drag-n-drop.js") }}";

        let formTitle  = document.getElementById("project-name");
        let formEnd    = document.getElementById("end-date");
        let formStatus = document.getElementById("project-status");

        let canPress = true;
        let formButton = document.getElementById("actions");

        formButton.addEventListener("click", (e) => {
            e.preventDefault();
            if (canPress) verifyForm();
        });

        function verifyForm() {
            let formValidation = true;
            FormVerifier.resetFormState([formTitle, formEnd, formStatus]);

            formValidation &= FormVerifier.checkField(formTitle, formTitle, [FormVerifier.verifyEmptyness("Please enter a project's name")]);
            formValidation &= FormVerifier.checkField(formStatus, formStatus, [FormVerifier.verifyEmptyness("Please select the project's status")]);

            if (formValidation) {
                canPress = false;
                FormVerifier.validateForm("Creating project ...");
                document.getElementById("project-form").submit();
            }
        }

        @if(Auth::user()->isAdmin())
        // Function to select owner
        window.selectOwner = function(element) {
            const userId = element.dataset.userId;
            const userName = element.dataset.userName;
            const userAvatar = element.dataset.userAvatar;

            // Update hidden field
            document.getElementById('owner-id').value = userId;

            // Update render
            document.getElementById('owner-avatar').src = userAvatar;
            document.getElementById('owner-name').textContent = userName;

            // Close modal
            document.getElementById('owner-modal').close();
        };

        // Search in user list
        const searchInput = document.getElementById('owner-search');
        const userItems = document.querySelectorAll('.modal-list-selectable');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            userItems.forEach(item => {
                const name = item.dataset.userName.toLowerCase();
                const email = item.dataset.userEmail.toLowerCase();

                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }

            });
        });
        @endif
    </script>
@endsection
