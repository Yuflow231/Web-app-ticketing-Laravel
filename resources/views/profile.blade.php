@extends('layout.main')

@section('title')
    <title>Profile - Ticketing App</title>
@endsection

@section('resources')
    @php use Illuminate\Support\Facades\Storage; @endphp
    <script src="{{ asset("utils/js/side-bar.js") }}" defer></script>
    <script src="{{ asset("utils/js/password-toggle.js") }}" defer></script>
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
                        <button onclick="location.href = '{{ route('profile.edit') }}'" type="button" class="btn">Edit</button>
                    </div>
                </div>
            </section>

            <div class="detail-side">
                <section class="detail-card">
                    <h2>Preferences</h2>
                    <div class="detail-item">
                        <label>Language</label>
                        <p>
                            @switch(auth()->user()->language ?? 'en')
                                @case('en')
                                    English
                                    @break
                                @case('fr')
                                    Français
                                    @break
                            @endswitch
                        </p>
                    </div>
                </section>

                <section class="detail-card">
                    <h2>Security</h2>
                    <div class="detail-item">
                        <label>Password</label>
                        <p style="margin-bottom: 1rem;">••••••••••••</p>
                        <button onclick="document.getElementById('password-modal').showModal()" type="button" class="btn btn--outline" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                            <i class="fa-solid fa-key"></i>
                            Change Password
                        </button>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection

@section('modal')
    {{-- Update password modal --}}
    <dialog id="password-modal" class="modal-container">
        <div style="display: flex; justify-content: space-between;">
            <h2 style="margin-bottom: 0.5rem;">Change password</h2>
            <button type="button" class="icon" onclick="document.getElementById('password-modal').close()" style="font-size: 1.5rem; color: var(--text-secondary);">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('api.profile.update-password') }}" id="password-form">
            @csrf
            <div class="form-item-stacked">
                <label for="current-password">Current password</label>
                <div class="password-wrapper">
                    <input type="password" id="current-password" name="current_password" required placeholder="Your current password">
                    <span id="toggle-password-current" class="toggle-password"><i class="fa-solid fa-eye"></i></span>
                </div>
            </div>
            <div class="form-item-stacked">
                <label for="new-password">New password</label>
                <div class="password-wrapper">
                    <input type="password" id="new-password" name="password" required placeholder="Your new password">
                    <span id="toggle-password-new" class="toggle-password"><i class="fa-solid fa-eye"></i></span>
                </div>
            </div>
            <div class="form-item-stacked">
                <label for="confirm-password">Confirm password</label>
                <div class="password-wrapper">
                    <input type="password" id="confirm-password" name="password_confirmation" required placeholder="Confirm password">
                    <span id="toggle-password-confirm" class="toggle-password"><i class="fa-solid fa-eye"></i></span>
                </div>
            </div>

            <div class="inline-elements">
                <button type="button" class="btn btn--outline" onclick="document.getElementById('password-modal').close()">Cancel</button>
                <button type="submit" id="confirm-action" class="btn btn--danger">Change password</button>
            </div>
        </form>
    </dialog>
@endsection

@section('js_page')
    <script type="module">
        import * as FormVerifier from "{{ asset("utils/js/form-verifs.js") }}";
        import Toast from "{{ asset('utils/js/toast.js') }}";

        const passwordForm = document.getElementById('password-form');
        const passwordModal = document.getElementById('password-modal');

        const passwordCurrent = document.getElementById('current-password');
        const passwordNew = document.getElementById('new-password');
        const passwordConfirm = document.getElementById('confirm-password');


        const button = document.getElementById('confirm-action');

        button.addEventListener('click', async (e) => {
            e.preventDefault();

            const formData = new FormData(passwordForm);
            const data = Object.fromEntries(formData.entries());

            if(formVerif()){
                try {
                    const response = await fetch(passwordForm.action, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': data._token
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (response.ok) {
                        Toast(result.message || 'Password updated!', 'success');
                        passwordForm.reset();
                        passwordModal.close();
                    } else {
                        // Extract validation errors (422) or logic errors (406)
                        let errorMsg = result.error || result.message || 'An error occurred';
                        if (result.errors) {
                            errorMsg = Object.values(result.errors).flat().join(' ');
                        }
                        Toast(errorMsg, 'error');
                    }
                } catch (error) {
                    Toast('Failed to connect to the server.', 'error');
                }
            }
        });

        function formVerif() {
            let formValidation = true;
            FormVerifier.resetFormState([passwordCurrent , passwordNew, passwordConfirm]);

            formValidation &= FormVerifier.checkField(passwordCurrent,    passwordCurrent,    [FormVerifier.verifyEmptyness("Please enter your current password")]);
            formValidation &= FormVerifier.checkField(passwordNew,    passwordNew,    [FormVerifier.verifyEmptyness("Please enter a password"), FormVerifier.verifyLength("Password must be 8 characters long")]);
            if (passwordNew.value !== passwordConfirm.value){
                formValidation = false;
                passwordNew.classList.add("error-field");
                passwordConfirm.classList.add("error-field");
                Toast("Password confirmation don't match the new password entered." , "error");
            }
            return formValidation;
        }
    </script>
@endsection
