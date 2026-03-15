@extends('layout.main')

@section('title')
    <title>Login - Ticketing App</title>
@endsection

@section('resources')
@endsection

@section('content')
    <!-- Main Content -->
    <div id="login-box">
        <form id="login-form" class="form-content card-wo-hover" method="POST" action="{{ route('login') }}">
            @csrf

            <h2 style="text-align: center">Login</h2>

            {{-- Afficher les erreurs générales --}}
            @if ($errors->any())
                <div class="alert alert-error" style="margin-bottom: var(--spacing-md); padding: var(--spacing-sm); background-color: #fee2e2; border: 1px solid #fecaca; border-radius: 6px; color: #991b1b;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Message de succès (si redirection depuis inscription) --}}
            @if (session('success'))
                <div class="alert alert-success" style="margin-bottom: var(--spacing-md); padding: var(--spacing-sm); background-color: #d1fae5; border: 1px solid #a7f3d0; border-radius: 6px; color: #065f46;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="form-item-stacked">
                <label for="form-email">Email</label>
                <input
                    type="email"
                    id="form-email"
                    name="email"
                    placeholder="Email"
                    value="{{ old('email') }}"
                    required
                    class="@error('email') input-error @enderror"
                >
                @error('email')
                <span class="error-message" style="color: #dc2626; font-size: 0.875rem; margin-top: 4px; display: block;">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-item-stacked">
                <label for="form-password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="form-password" name="password" placeholder="Password" required class="@error('password') input-error @enderror">
                    <span id="toggle-password" class="toggle-password">
                        <i id="pass-icon" class="fa-solid fa-eye"></i>
                    </span>
                </div>
                @error('password')
                <span class="error-message" style="color: #dc2626; font-size: 0.875rem; margin-top: 4px; display: block;">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Option "Se souvenir de moi" --}}
            <div class="form-item-stacked" style="margin-top: var(--spacing-sm);">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="remember" id="remember" style="margin-right: 8px;">
                    <span>Remember me</span>
                </label>
            </div>

            <div class="password">
                <a href="{{ route('reset-password') }}">Forgot password ?</a>
            </div>

            <div style="display: flex;flex-direction: column">
                <button class="btn centered" id="actions" type="submit">
                    Login
                </button>
                <button onclick="location.href = '{{ route('register') }}'" class="btn centered btn--outline" type="button" style="margin-top: var(--spacing-md);">
                    Create account
                </button>
            </div>
        </form>
    </div>
@endsection

@section('js_page')
    <script type="module">
        // Set as module to allow imports
        import * as FormVerifier from "{{ asset('utils/js/form-verifs.js') }}";

        // get reference to form's fields
        const formMail = document.getElementById("form-email");
        const formPass = document.getElementById("form-password");
        const loginForm = document.getElementById("login-form");

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
            FormVerifier.resetFormState([formMail, formPass]);

            // check each field with its corresponding checks
            formValidation &= FormVerifier.checkField(formMail, formMail, [
                FormVerifier.verifyEmptyness("Please enter your mail"),
                FormVerifier.verifyMail("Email is invalid")
            ]);
            formValidation &= FormVerifier.checkField(formPass, formPass, [
                FormVerifier.verifyEmptyness("Please enter your password")
            ]);

            // if everything checks out
            if(formValidation){
                canPress = false;

                // Afficher un message de chargement
                formButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Logging in...';
                formButton.disabled = true;

                // Soumettre le formulaire Laravel
                loginForm.submit();
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
