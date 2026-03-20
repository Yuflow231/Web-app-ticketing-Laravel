@extends('layout.main')

@section('title')
    <title>Edit Profile - Ticketing App</title>
@endsection

@section('resources')
    @php use Illuminate\Support\Facades\Storage; @endphp
    <script src="{{ asset("utils/js/side-bar.js") }}" defer></script>
@endsection

@section('content')
    @include('layout.nav')
    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1>Edit Profile</h1>
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

        <form id="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="detail-container">
                <section class="detail-card">
                    <h2>Personal Information</h2>

                    {{-- Profile Picture Upload --}}
                    <div class="form-item-stacked" style="margin-bottom: 2rem;">
                        <label>Profile Picture</label>
                        <div style="display: flex; align-items: center; gap: var(--spacing-lg);">
                            {{-- Current Profile Picture --}}
                            <div style="position: relative;">
                                <img id="profile-preview" src="{{ auth()->user()->profile_pic ? Storage::url(auth()->user()->profile_pic) : asset('assets/images/icon.png') }}" alt="Profile" class="profile-pic">
                            </div>

                            {{-- Upload Controls --}}
                            <div style="flex: 1;">
                                <input type="file" id="profile-pic-input" name="profile_pic" style="display: none;">
                                <input type="hidden" id="remove-pic" name="remove_pic" value="0">

                                <button type="button" class="btn btn--outline" onclick="document.getElementById('profile-pic-input').click()" style="margin-bottom: 0.5rem;">
                                    <i class="fa-solid fa-upload"></i>
                                    Choose New Picture
                                </button>

                                <p style="font-size: var(--font-size-sm); color: var(--text-secondary); margin: 0;">JPG, PNG or GIF. Max size 4MB.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Name Fields --}}
                    <div class="form-2elements">
                        <div class="form-item-stacked">
                            <label for="first-name">First Name *</label>
                            <input type="text" id="first-name" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" placeholder="First name" required>
                        </div>

                        <div class="form-item-stacked">
                            <label for="last-name">Last Name *</label>
                            <input type="text" id="last-name" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" placeholder="Last name" required>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="form-item-stacked">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" placeholder="email@example.com" required>
                    </div>

                    {{-- Role (Read-only) --}}
                    <div class="form-item-stacked">
                        <label>Role</label>
                        <input type="text" value="{{ auth()->user()->role }}" disabled style="background: #f3f4f6; cursor: not-allowed;">
                    </div>

                    {{-- Member Since (Read-only) --}}
                    <div class="form-item-stacked">
                        <label>Member Since</label>
                        <input type="text" value="{{ auth()->user()->created_at ? auth()->user()->created_at->format('F j, Y') : 'N/A' }}" disabled style="background: #f3f4f6; cursor: not-allowed;">
                    </div>

                    {{-- Action Buttons --}}
                    <div style="display: flex; gap: var(--spacing-md); justify-content: center;">
                        <a href="{{ route('profile') }}" class="btn btn--outline">
                            <i class="fa-solid fa-xmark"></i>
                            Cancel
                        </a>
                        <button type="submit" id="save-button" class="btn">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Save Changes
                        </button>
                    </div>
                </section>

                <div class="detail-side">
                    <section class="detail-card">
                        <h2>Preferences</h2>

                        <div class="form-item-stacked">
                            <label for="language">Language</label>
                            <select id="language" name="language">
                                <option value="en" {{ old('language', auth()->user()->language ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                                <option value="fr" {{ old('language', auth()->user()->language ?? 'en') == 'fr' ? 'selected' : '' }}>Français</option>
                            </select>
                        </div>
                    </section>

                    <section class="detail-card">
                        <h2>Security</h2>

                        <div class="detail-item">
                            <label>Password</label>
                            <p style="margin-bottom: 1rem;">••••••••••••</p>
                            <a href="{{ route('reset-password') }}" class="btn btn--outline" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                                <i class="fa-solid fa-key"></i>
                                Change Password
                            </a>
                        </div>
                    </section>

                    <section class="detail-card" style="border: 1px solid var(--danger-color);">
                        <h2 style="color: var(--danger-color);">Danger Zone</h2>

                        <div class="detail-item">
                            <label style="color: var(--danger-color); font-weight: 600;">Delete Account</label>
                            <p style="margin-bottom: 1rem; font-size: var(--font-size-sm);">
                                Once you delete your account, there is no going back. Please be certain.
                            </p>
                            <button type="button" class="btn btn--danger" onclick="document.getElementById('delete-modal').showModal()">
                                <i class="fa-solid fa-trash"></i>
                                Delete Account
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </form>
    </main>
@endsection


@section('modal')
    {{-- Delete account modal --}}
    <dialog id="delete-modal" class="modal-container">
        <h2>Delete account</h2>
        <p style="margin-bottom: 1rem; color: var(--text-secondary);">This action is irreversible. Enter your password to confirm.</p>

        @if($errors->has('password'))
            <p style="color: red; margin-bottom: 0.5rem; font-size: var(--font-size-sm);">{{ $errors->first('password') }}</p>
        @endif

        <form method="POST" action=" {{ route("profile-delete") }}" id="delete-form">
            @csrf
            @method('DELETE')
            <div class="form-item-stacked">
                <label for="delete-password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="delete-password" name="password" required placeholder="Your current password">
                    <span id="toggle-password" class="toggle-password">
                            <i id="pass-icon" class="fa-solid fa-eye"></i>
                        </span>
                </div>
            </div>
            <div class="inline-elements">
                <button type="button" class="btn btn--outline" onclick="document.getElementById('delete-modal').close()">Cancel</button>
                <button type="submit" id="delete-action" class="btn btn--danger">Confirm deletion</button>
            </div>
        </form>
    </dialog>
@endsection

@section('js_page')
    <script type="module">
        import * as FormVerifier from "{{ asset('utils/js/form-verifs.js') }}";
        import Toast from "{{ asset('utils/js/toast.js') }}";

        // Profile Picture Preview
        const profilePicInput = document.getElementById('profile-pic-input');
        const profilePreview = document.getElementById('profile-preview');

        profilePicInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                // Validate file type
                if (!file.type.match('image.*')) {
                    Toast('Please select an image file', "error");
                    profilePicInput.value = '';
                    return;
                }

                // Validate file size (4MB)
                if (file.size > 4 * 1024 * 1024) {
                    Toast('File size must be less than 4MB', "error");
                    profilePicInput.value = '';
                    return;
                }

                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);

            }
        });

        let canPress = true;
        /* --------------------------------------
        *        ACCOUNT UPDATE FORM
        * --------------------------------------*/
        let formFirst = document.getElementById("first-name")
        let formLast = document.getElementById("last-name")
        let formMail = document.getElementById("email")
        let saveButton = document.getElementById("save-button")

        saveButton.addEventListener("click", (e) => {
            e.preventDefault();
            if (canPress){
                verifySaveForm();
            }
        });

        function verifySaveForm(){
            let formValidation = true;
            FormVerifier.resetFormState([formFirst, formLast, formMail]);
            formValidation &= FormVerifier.checkField(formFirst, formFirst, [FormVerifier.verifyEmptyness("Please enter your first name"),  FormVerifier.verifyLengthName("First name must be lesser than 40 characters")]);
            formValidation &= FormVerifier.checkField(formLast, formLast, [FormVerifier.verifyEmptyness("Please enter your last name"), FormVerifier.verifyLengthName("Last name must be lesser than 40 characters")]);
            formValidation &= FormVerifier.checkField(formMail, formMail, [FormVerifier.verifyEmptyness("Please enter a mail"), FormVerifier.verifyMail("Email is invalid")]);
            if(formValidation){
                canPress = false;
                document.getElementById("profile-form").submit();
            }
        }

        /* --------------------------------------
        *       ACCOUNT DELETION FORM
        * --------------------------------------*/
        const formPass = document.getElementById("delete-password");
        let deleteButton = document.getElementById("delete-action");

        deleteButton.addEventListener("click", (e) => {
            e.preventDefault();
            if (canPress){
                verifyDeleteForm();
            }
        });

        function verifyDeleteForm(){
            let formValidation = true;
            FormVerifier.resetFormState([formPass]);
            formValidation &= FormVerifier.checkField(formPass, formPass, [FormVerifier.verifyEmptyness("Please enter your password")]);
            if(formValidation){
                canPress = false;
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
