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

        <form id="project-form" class="form-box" method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-2elements">
                <div class="form-item-stacked">
                    <label for="project-name">Project's name *</label>
                    <input type="text" id="project-name" name="name" placeholder="Project's name" required>
                </div>
                <div class="form-item-stacked">
                    <label>Owner *</label>
                    <!-- Guest: locked to self -->
                    <div class="user-profile-inline" style="padding: var(--spacing-sm); border: 1px solid #ddd; border-radius: var(--radius-md); opacity: 0.8;">
                        <img src="{{ Auth::user()->profile_pic ? asset('assets/images/' . Auth::user()->profile_pic) : asset('assets/images/icon.png') }}" class="profile-pic-mini" alt="profile-pic">
                        <span style="margin-left: var(--spacing-sm)">{{ Auth::user()->first_name. ' ' . Auth::user()->last_name }}</span>
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
                    <label for="end-date">Closing date</label>
                    <input type="date" id="end-date" name="closing_date">
                </div>
            </div>

            <div class="form-item-stacked">
                <label for="project-status">Status</label>
                <select id="project-status" name="status">
                    <option value="" selected hidden disabled>Select a status</option>
                    <option value="New">New</option>
                    <option value="In Progress">In Progress</option>
                    <option value="On Hold">On Hold</option>
                </select>
            </div>

            <div class="form-item-stacked">
                <label for="project-description">Description</label>
                <textarea id="project-description" name="description" rows="6" placeholder="Project's description"></textarea>
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
            // date is optional in backend, do not force emptiness
            formValidation &= FormVerifier.checkField(formEnd, formEnd, [FormVerifier.verifyEmptyness("Please select an ending date"), FormVerifier.verifyDate("Ending date cannot be in the past")]);
            formValidation &= FormVerifier.checkField(formStatus, formStatus, [FormVerifier.verifyEmptyness("Please select the project's status")]);
            // contract is optional in backend, do not force file check

            if (formValidation) {
                canPress = false;
                FormVerifier.validateForm("Creating project ...");
                document.getElementById("project-form").submit();
            }
        }
    </script>
@endsection

