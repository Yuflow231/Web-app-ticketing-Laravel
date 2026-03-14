@extends('layout.main')

@section('title')
    <title>Profile - Ticketing App</title>
@endsection

@section('resources')
    @php use Illuminate\Support\Facades\Storage; @endphp
@endsection

@section('content')
    @include('layout.nav')
    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1>User Profile</h1>
        </header>

        <div class="detail-container">
            <section class="detail-card">
                <header class="profile-header">
                    <div class="name-group">
                        <div class="username" data-type="first-name">{{ auth()->user()->first_name }}</div>
                        <div class="username" data-type="last-name">{{ auth()->user()->last_name }}</div>
                        <p class="user-role">{{ auth()->user()->role }}</p>
                    </div>


                    <!-- <img src="{{ asset("assets/images/yuflow.jpg") }}" alt="User Profile" class="profile-pic" > -->
                    <img src="{{ !empty(auth()->user()->profile_pic) ? Storage::url(auth()->user()->profile_pic) : asset('assets/images/icon.png') }}" alt="User Profile" class="profile-pic">

                </header>

                <div>
                    <div class="detail-item">
                        <label>Email Address</label>
                        <p>{{ auth()->user()->email }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Member Since</label>
                        <p>{{ optional(auth()->user()->created_at)->format("Y-m-d") }}</p>
                    </div>
                    <div style="display: flex; gap: var(--spacing-sm);">
                        <button type="button" class="btn">Edit</button>

                        @if(!auth()->user()->isAdmin())
                            <button type="button" class="btn btn--danger" onclick="document.getElementById('delete-modal').showModal()">Delete account</button>
                        @endif
                    </div>
                </div>
            </section>

            @if(!auth()->user()->isAdmin())
            {{-- Delete account modal --}}
            <dialog id="delete-modal" class="modal-container">
                <h2>Delete account</h2>
                <p style="margin-bottom: 1rem; color: var(--text-secondary);">This action is irreversible. Enter your password to confirm.</p>

                @if($errors->has('password'))
                    <p style="color: red; margin-bottom: 0.5rem; font-size: var(--font-size-sm);">{{ $errors->first('password') }}</p>
                @endif

                <form method="POST" action="{{ route('profile') }}" id="delete-form">
                    @csrf
                    @method('DELETE')
                    <div class="form-item-stacked">
                        <label for="delete-password">Password</label>
                        <input type="password" id="delete-password" name="password" required placeholder="Your current password">
                    </div>
                    <div class="inline-elements">
                        <button type="button" class="btn btn--outline" onclick="document.getElementById('delete-modal').close()">Cancel</button>
                        <button type="submit" id="actions" class="btn btn--danger">Confirm deletion</button>
                    </div>
                </form>
            </dialog>
            @endif

            <div class="detail-side">
                <section class="detail-card">
                    <h2>Preferences</h2>
                    <div class="form-item" style="width: 10rem;">
                        <label for="language-select">Language</label>
                        <select id="language-select">
                            <option value="en" selected>English</option>
                            <option value="fr">French</option>
                        </select>
                    </div>
                </section>

                <section class="detail-card">
                    <h2>Security</h2>
                    <div class="detail-item">
                        <label>Password</label>
                        <p style="margin-bottom: 1rem;">••••••••••••</p>
                        <a href="{{ route('reset-password') }}" class="password" style="">Change Password</a>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection

@section('js_page')
    <script type="module">
        // Set as module to allow imports
        import * as FormVerifier from "{{ asset('utils/js/form-verifs.js') }}";

        // get reference to form's fields
        const formPass = document.getElementById("delete-password");
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
            FormVerifier.resetFormState([formPass]);

            // check each field with its corresponding checks
            formValidation &= FormVerifier.checkField(formPass, formPass, [FormVerifier.verifyEmptyness("Please enter your password")]);

            // if everything checks out
            if(formValidation){
                canPress = false;

                FormVerifier.validateForm("Deleting account ...");
                // loginForm.submit();
                document.getElementById("delete-form").submit();
            }
        }

        // Toggle password visibility
        const togglePassword = document.getElementById('toggle-password');

        togglePassword.addEventListener('click', function () {
            // Toggle the type of the field
            const type = formPass.getAttribute('type') === 'password' ? 'text' : 'password';
            formPass.setAttribute('type', type);

            // Toggle the icon
            const icon = document.getElementById('pass-icon');

            // switch between visual states
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    </script>
@endsection
