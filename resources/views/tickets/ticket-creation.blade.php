@extends('layout.main')

@section('title')
    <title>Ticket creation - Ticketing App</title>
@endsection

@section('resources')
    <script src="{{ asset("utils/js/side-bar.js") }}" defer></script>
@endsection

@section('content')
    @include('layout.nav')
    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1>Ticket creation</h1>
        </header>

        <form id="ticket-form" class="form-box" method="POST" enctype="multipart/form-data">
            <div class="form-2elements">
                <div class="form-item-stacked">
                    <label for="ticket-title">Ticket's object *</label>
                    <input type="text" id="ticket-title" name="ticket-title" placeholder="Ticket's object">
                </div>
                <div class="form-item-stacked">
                    <label for="ticket-project">Associated project *</label>
                    <select id="ticket-project" name="ticket-project">
                        <option value="" disabled selected hidden>Select a project</option>
                        <option value="Skyblocker">Skyblocker</option>
                    </select>
                </div>
            </div>

            <div class="form-2elements">
                <!--
                <div class="form-item-stacked">
                    <label for="due-date">Due to *</label>
                    <input type="date" id="due-date" name="due-date">
                </div>
                -->
                <div class="form-item-stacked">
                    <label for="ticket-priority">Priority *</label>
                    <select id="ticket-priority" name="ticket-priority">
                        <option value="" disabled selected hidden>Select a priority</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
            </div>

            <div class="form-item-stacked">
                <label for="ticket-description">Description</label>
                <textarea id="ticket-description" name="ticket-description" rows="8" placeholder="Ticket's description"></textarea>
            </div>

            <div class="form-item-stacked">
                <label for="drop-file">Attached files</label>
                <div id="drop-zone">
                    <p>Drag and drop files here or click to select files</p>
                    <input type="file" id="drop-file" name="drop-file[]" multiple style="display: none;">
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
        // Set as module to allow imports
        import * as FormVerifier from "{{ asset("utils/js/form-verifs.js") }}";
        import * as DragDrop from "{{ asset("utils/js/drag-n-drop.js") }}";

        // Get references to form's field
        let formTitle = document.getElementById("ticket-title");
        let formProject = document.getElementById("ticket-project");

        // let formDate = document.getElementById("due-date");
        let formPriority = document.getElementById("ticket-priority");

        // prevent multiple request when the infos are correct
        let canPress = true;

        // Get reference to form button
        let formButton = document.getElementById("actions");

        // Add click event on the previously got button
        formButton.addEventListener("click", (e) => {
            e.preventDefault();
            if (canPress){
                verifyForm();
            }
        });

        function verifyForm(){
            let formValidation = true;
            // Clear previous highlights
            FormVerifier.resetFormState([formTitle, formProject, formPriority]);

            // check each field with its corresponding checks
            formValidation &= FormVerifier.checkField(formTitle, formTitle, [FormVerifier.verifyEmptyness("Please enter a ticket title")]);
            formValidation &= FormVerifier.checkField(formProject, formProject, [FormVerifier.verifyEmptyness("Please chose the associated project")]);
            // formValidation &= FormVerifier.checkField(formDate, formDate, [FormVerifier.verifyEmptyness("Please select a due date"), FormVerifier.verifyDate("Due date cannot be in the past")]);
            formValidation &= FormVerifier.checkField(formPriority, formPriority, [FormVerifier.verifyEmptyness("Please chose the priority level")]);

            // if everything checks out
            if(formValidation){
                canPress = false;

                // DragDrop.syncFilesToInput();
                FormVerifier.validateForm("Creating ticket ...", "{{ route("tickets.tickets") }}");
            }
        }
    </script>
@endsection
