@extends('layout.main')

@section('title')
    <title>Ticket creation - Ticketing App</title>
@endsection

@section('resources')
    <script src="{{ asset("utils/js/side-bar.js") }}" defer></script>
@endsection

@section('content')
    @include('layout.nav')
    <main class="main-content">
        <header class="page-header">
            <h1>Ticket creation</h1>
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

        <form id="ticket-form" class="form-box" method="POST"  action="{{ route('tickets.ticket-store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-2elements">
                <div class="form-item-stacked">
                    <label for="ticket-title">Ticket's object *</label>
                    <input type="text" id="ticket-title" name="name" placeholder="Ticket's object">
                </div>

                <input type="hidden" id="project-id" name="project_id" value="{{ old('project_id') }}">
                <div class="form-item-stacked">
                    <label>Associated project *</label>

                    <div id="selected-project" class="project-selector modal-select" onclick="document.getElementById('project-modal').showModal()">
                        <div id="project-content" style="flex: 1; display: flex;">
                            <span id="project-name" style="color: var(--text-secondary);">
                                Select a project
                            </span>
                        </div>
                        <i class="fa-solid fa-chevron-down icon" style="color: var(--primary-color);"></i>
                    </div>
                    <p style="font-size: var(--font-size-sm); color: var(--text-secondary); margin-top: 0.25rem;">
                        Click to select a project
                    </p>
                </div>
            </div>

            <div class="form-2elements">
                <div class="form-item-stacked">
                    <label for="ticket-priority">Priority *</label>
                    <select id="ticket-priority" name="priority">
                        <option value="" disabled selected hidden>Select a priority</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>

                <div class="form-item-stacked">
                    <label for="ticket-status">Status *</label>
                    <select id="ticket-status" name="status">
                        <option value="" disabled selected hidden>Select a status</option>
                        <option value="New">New</option>
                        <option value="In Progress">In Progress</option>
                        <option value="On Hold">On Hold</option>
                        <option value="Completed">Completed</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
            </div>

            @if( auth()->user()->isAdmin() )
                <div class="form-item-stacked">
                    <label for="ticket-type">Type *</label>
                    <select id="ticket-type" name="type">
                        <option value="" disabled selected hidden>Select a type</option>
                        <option value="Included">Included</option>
                        <option value="Billed">Billed</option>
                    </select>
                </div>
            @endif

            <div class="form-item-stacked">
                <label for="ticket-description">Description</label>
                <textarea id="ticket-description" name="description" rows="8" placeholder="Ticket's description"></textarea>
            </div>

            <div class="form-item-stacked">
                <label for="drop-file">Attached files <span style="font-size: var(--font-size-sm); color: var(--text-secondary);">(max 64MB per file)</span></label>
                <div id="drop-zone">
                    <p>Drag and drop files here or click to select files</p>
                    <input type="file" id="drop-file" name="attachments[]" multiple style="display: none;">
                </div>
                <ul id="file-list"></ul>
            </div>

            <div class="centered" style="display: flex; gap: var(--spacing-md); justify-content: center;">
                <button onclick="location.href = '{{ route("tickets.tickets") }}'" type="button" class="btn btn--outline">Cancel</button>
                <button id="actions" class="btn" type="submit">Create ticket</button>
            </div>
        </form>
    </main>
@endsection

@section('modal')
    {{-- Modal to select the associated project --}}
    <dialog id="project-modal" class="modal-container">
        <div style="display: flex; justify-content: space-between;">
            <h2 style="margin-bottom: 0.5rem;">Select Project</h2>
            <button type="button" class="icon" onclick="document.getElementById('project-modal').close()" style="font-size: 1.5rem; color: var(--text-secondary);">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Search bar --}}
        <div class="form-item-stacked" style="margin-bottom: 1rem;">
            <input type="text" id="project-search" placeholder="Search by project name...">
        </div>

        {{-- Projects list --}}
        <div id="projects-list" class="modal-list">
            @foreach($projects as $project)
                <div class="modal-list-selectable" onclick="selectProject(this)"
                    data-project-id="{{ $project->id }}"
                    data-project-name="{{ $project->name }}"
                    data-project-status="{{ $project->status }}">

                    <div style="flex: 1; min-width: 0;">
                        <div class="modal-list-name">{{ $project->name }}</div>
                        <div class="modal-list-subname">
                            @if($project->description)
                                {{ Str::limit($project->description, 50) }}
                            @else
                                No description
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 1rem; display: flex; justify-content: center;">
            <button type="button" class="btn btn--outline" onclick="document.getElementById('project-modal').close()">
                Cancel
            </button>
        </div>
    </dialog>
@endsection

@section('js_page')
    <script type="module">
        import * as FormVerifier from "{{ asset("utils/js/form-verifs.js") }}";
        import * as DragDrop from "{{ asset("utils/js/drag-n-drop.js") }}";
        import Toast from "{{ asset("utils/js/toast.js") }}";


        let fileInput = document.getElementById('drop-file');
        let fileDrop = document.getElementById('drop-zone');

        let formTitle = document.getElementById("ticket-title");
        let formPriority = document.getElementById("ticket-priority");
        let formStatus = document.getElementById("ticket-status");

        let formProject = document.getElementById("project-id");
        let selectedProjectDiv = document.getElementById("selected-project");
        @if( auth()->user()->isAdmin() )
            let formType = document.getElementById("ticket-type");
        @endif

        let canPress = true;
        let formButton = document.getElementById("actions");

        formButton.addEventListener("click", (e) => {
            e.preventDefault();
            if (canPress){
                verifyForm();
            }
        });

        function verifyForm(){
            let formValidation = true;
            @if( auth()->user()->isAdmin() )
                FormVerifier.resetFormState([formTitle, formPriority, formStatus, formType, fileDrop]);
            @else
                FormVerifier.resetFormState([formTitle, formPriority, formStatus, fileDrop]);
            @endif

            formValidation &= FormVerifier.checkField(formTitle, formTitle, [FormVerifier.verifyEmptyness("Please enter a ticket title")]);
            formValidation &= FormVerifier.checkField(formPriority, formPriority, [FormVerifier.verifyEmptyness("Please chose the priority level")]);
            formValidation &= FormVerifier.checkField(formStatus, formStatus, [FormVerifier.verifyEmptyness("Please chose the status")]);
            if (!formProject.value) {
                selectedProjectDiv.classList.add("error-field");
                Toast("Please select a project", "error");
                formValidation = false;
            }

            @if( auth()->user()->isAdmin() )
                formValidation &= FormVerifier.checkField(formType, formType, [FormVerifier.verifyEmptyness("Please chose the type")]);
            @endif

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

            if(formValidation){
                canPress = false;
                FormVerifier.validateForm("Creating ticket ...");
                document.getElementById("ticket-form").submit();
            }
        }

        // Function to select associated project
        window.selectProject = function(element) {
            const projectId = element.dataset.projectId;
            const projectName = element.dataset.projectName;

            // Update hidden field
            document.getElementById('project-id').value = projectId;

            // Update render
            const projectContent = document.getElementById('project-content');
            projectContent.innerHTML = `${projectName}`;

            // Close modal
            document.getElementById('project-modal').close();
        };

        // Search in project list
        const searchInput = document.getElementById('project-search');
        const projectItems = document.querySelectorAll('.modal-list-selectable');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            projectItems.forEach(item => {
                const name = item.dataset.projectName.toLowerCase();

                if (name.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
@endsection
