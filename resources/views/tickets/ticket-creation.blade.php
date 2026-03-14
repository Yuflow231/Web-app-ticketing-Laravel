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
                <div class="form-item-stacked">
                    <label for="ticket-project">Associated project *</label>
                    <select id="ticket-project" name="project_id">
                        <option value="" disabled selected hidden>Select a project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
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

@section('js_page')
    <script type="module">
        import * as FormVerifier from "{{ asset("utils/js/form-verifs.js") }}";
        import * as DragDrop from "{{ asset("utils/js/drag-n-drop.js") }}";
        import Toast from "{{ asset("utils/js/toast.js") }}";


        let fileInput = document.getElementById('drop-file');
        let fileDrop = document.getElementById('drop-zone');

        let formTitle = document.getElementById("ticket-title");
        let formProject = document.getElementById("ticket-project");
        let formPriority = document.getElementById("ticket-priority");
        let formStatus = document.getElementById("ticket-status");
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
                FormVerifier.resetFormState([formTitle, formProject, formPriority, formStatus, formType, fileDrop]);
            @else
                FormVerifier.resetFormState([formTitle, formProject, formPriority, formStatus, fileDrop]);
            @endif

            formValidation &= FormVerifier.checkField(formTitle, formTitle, [FormVerifier.verifyEmptyness("Please enter a ticket title")]);
            formValidation &= FormVerifier.checkField(formProject, formProject, [FormVerifier.verifyEmptyness("Please chose the associated project")]);
            formValidation &= FormVerifier.checkField(formPriority, formPriority, [FormVerifier.verifyEmptyness("Please chose the priority level")]);
            formValidation &= FormVerifier.checkField(formStatus, formStatus, [FormVerifier.verifyEmptyness("Please chose the status")]);
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
    </script>
@endsection
