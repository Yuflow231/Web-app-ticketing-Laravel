@extends('layout.main')

@section('title')
    <title>Project creation - Ticketing App</title>
@endsection

@section('resources')
    <script src="{{ asset("utils/js/side-bar.js") }}" defer></script>
@endsection

@section('content')
    @include('layout.nav')
    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1>Project creation</h1>
        </header>

        <form id="project-form" class="form-box" method="POST" enctype="multipart/form-data">
            <div class="form-2elements">
                <div class="form-item-stacked">
                    <label for="project-name">Project's name *</label>
                    <input type="text" id="project-name" name="project-name" placeholder="Project's name" required>
                </div>
                <div class="form-item-stacked">
                    <label>Owner *</label>
                    <!-- Guest: locked to self -->
                    <div class="user-profile-inline" style="padding: var(--spacing-sm); border: 1px solid #ddd; border-radius: var(--radius-md); opacity: 0.8;">
                        <img src="{{ asset("utils/images/yuflow.jpg") }}" class="profile-pic-mini" alt="profile-pic">
                        <span style="margin-left: var(--spacing-sm)">Yuflow</span>
                        <span>Furry</span>
                    </div>
                </div>
            </div>

            <div class="form-2elements">
                <!--
                <div class="form-item-stacked">
                    <label for="start-date">Starting date</label>
                    <input type="date" id="start-date" name="start-date">
                </div> -->
                <div class="form-item-stacked">
                    <label for="end-date">Estimated ending date</label>
                    <input type="date" id="end-date" name="end-date">
                </div>
            </div>

            <div class="form-item-stacked">
                <label for="project-status">Status</label>
                <select id="project-status" name="project-status">
                    <option value="" selected hidden disabled>Select a status</option>
                    <option value="New">New</option>
                    <option value="In Progress">In Progress</option>
                    <option value="On Hold">On Hold</option>
                </select>
            </div>

            <div class="form-item-stacked">
                <label for="project-description">Description</label>
                <textarea id="project-description" name="project-description" rows="6" placeholder="Project's description"></textarea>
            </div>

            <div class="form-item-stacked">
                <label for="drop-file">Attached files</label>
                <div id="drop-zone">
                    <p>Drag and drop files here or click to select files</p>
                    <input type="file" id="drop-file" name="drop-file[]" style="display: none;">
                </div>
                <ul id="file-list"></ul>
            </div>

            <div class="centered" style="display: flex; gap: var(--spacing-md); justify-content: center;">
                <button onclick="location.href = '{{ route("projects.projects") }}'" type="button" class="btn btn--outline">Cancel</button>
                <button id="actions" class="btn" type="submit">Create project</button>
            </div>
        </form>
    </main>
@endsection

@section('js_page')
    <script type="module">
        import * as FormVerifier from "{{ asset("utils/js/form-verifs.js") }}";
        import * as DragDrop from "{{ asset("utils/js/drag-n-drop.js") }}";

        // Form fields
        let formTitle  = document.getElementById("project-name");
        // let formStart  = document.getElementById("start-date");
        let formEnd    = document.getElementById("end-date");
        let formStatus = document.getElementById("project-status");
        let formFiles  = document.getElementById("file-list");
        let formDrop   = document.getElementById("drop-zone");

        let canPress = true;
        let formButton = document.getElementById("actions");

        formButton.addEventListener("click", (e) => {
            e.preventDefault();
            if (canPress) verifyForm();
        });

        function verifyForm() {
            let formValidation = true;
            FormVerifier.resetFormState([formTitle, formEnd, formStatus, formDrop]);

            formValidation &= FormVerifier.checkField(formTitle, formTitle, [FormVerifier.verifyEmptyness("Please enter a project's name")]);
            // formValidation &= FormVerifier.checkField(formStart, formStart, [FormVerifier.verifyEmptyness("Please select a starting date"), FormVerifier.verifyDate("Starting date cannot be in the past")]);
            formValidation &= FormVerifier.checkField(formEnd, formEnd, [FormVerifier.verifyEmptyness("Please select an ending date"), FormVerifier.verifyDate("Ending date cannot be in the past")]);
            // formValidation &= FormVerifier.checkField([formStart, formEnd], formEnd, [FormVerifier.verifyDateDiff("The ending date must be after the starting date")]);
            formValidation &= FormVerifier.checkField(formStatus, formStatus, [FormVerifier.verifyEmptyness("Please select the project's status")]);
            formValidation &= FormVerifier.checkField(formFiles, formDrop, [FormVerifier.verifyFile("Please send the project contract")]);

            if (formValidation) {
                canPress = false;
                //DragDrop.syncFilesToInput();
                FormVerifier.validateForm("Creating project ...", "{{ route("projects.projects") }}");
            }
        }
    </script>
@endsection

