@extends('layout.main')

@section('title')
    <title>Login - Ticketing App</title>
@endsection

@section('resources')
@endsection

@section('content')
    <!-- Main Content -->
    <div id="login-box">
        <form id="login-form" class="form-content card-wo-hover" method="POST">
            <h2 style="text-align: center">Login</h2>

            <div class="form-item-stacked">
                <label for="form-email">Email</label>
                <input type="email" id="form-email" name="email" placeholder="Email">
            </div>

            <div class="form-item-stacked">
                <label for="form-password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="form-password" name="password" placeholder="Password">
                    <span id="toggle-password" class="toggle-password">
                        <i id="pass-icon" class="fa-solid fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="password">
                <a href="{{ route("reset-password") }}">Forgot password ?</a>
            </div>

            <div style="display: flex;flex-direction: column">
                <button class="btn centered" id="actions" type="submit">
                    Login
                </button>
                <button onclick="location.href = '{{ route("create-account") }}'" class="btn centered btn--outline" type="button" style="margin-top: var(--spacing-md);">
                    Create account
                </button>
            </div>
        </form>
    </div>
@endsection

@section('js_page')
    <script type="module">
        // Set as module to allow imports
        import * as FormVerifier from "{{ asset("utils/js/form-verifs.js") }}";

        // get reference to form's fields
        const formMail = document.getElementById("form-email");
        const formPass = document.getElementById("form-password");

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
            formValidation &= FormVerifier.checkField(formMail, formMail, [FormVerifier.verifyEmptyness("Please enter your mail"), FormVerifier.verifyMail("Email is invalid") ]);
            formValidation &= FormVerifier.checkField(formPass, formPass, [FormVerifier.verifyEmptyness("Please enter your password")]);

            // if everything checks out
            if(formValidation){
                canPress = false;

                FormVerifier.validateForm("Logging in ...", "{{ route("dashboard") }}");
            }
        }

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
