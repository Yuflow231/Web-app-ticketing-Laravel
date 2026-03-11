@extends('layout.main')

@section('title')
    <title>Account creation - Ticketing App</title>
@endsection

@section('resources')
@endsection

@section('content')
    <!-- Main Content -->
    <div id="login-box">
        <form id="creation-form" class="form-content card-wo-hover" method="POST">
            <h2 style="text-align: center">Account creation</h2>

            <div class="form-item-stacked">
                <label for="form-first">First Name</label>
                <input type="text" id="form-first" name="form-first" placeholder="First Name">
            </div>

            <div class="form-item-stacked">
                <label for="form-last">Last Name</label>
                <input type="text" id="form-last" name="form-last" placeholder="Last name">
            </div>

            <div class="form-item-stacked">
                <label for="form-email">Email</label>
                <input type="email" id="form-email" name="form-email" placeholder="Email" required>
            </div>

            <div class="form-item-stacked">
                <label for="form-pass">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="form-pass" name="form-pass" placeholder="Password">
                    <span id="toggle-password" class="toggle-password">
                    <i id="pass-icon" class="fa-solid fa-eye"></i>
                </span>
                </div>
            </div>

            <div class="centered">
                <button class="btn" id="actions" type="submit">Create account</button>
            </div>

            <div class="centered" style="margin-top: var(--spacing-md);">
                <button onclick="location.href = '{{ route('login') }}'" type="button" class="btn btn--outline">Back to login</button>
            </div>
        </form>
    </div>
@endsection

@section('js_page')
    <script type="module">
        import * as FormVerifier from "{{ asset("utils/js/form-verifs.js") }}";

        let formFirst = document.getElementById("form-first");
        let formLast  = document.getElementById("form-last");
        let formMail  = document.getElementById("form-email");
        let formPass  = document.getElementById("form-pass");

        let canPress = true;

        let formButton = document.getElementById("actions");
        formButton.addEventListener("click", (e) => {
            e.preventDefault();
            verifyForm();
        });

        function verifyForm(){
            let formValidation = true;
            FormVerifier.resetFormState([formMail, formFirst, formLast, formPass]);

            formValidation &= FormVerifier.checkField(formFirst, formFirst, [FormVerifier.verifyEmptyness("Please enter your first name")]);
            formValidation &= FormVerifier.checkField(formLast,  formLast,  [FormVerifier.verifyEmptyness("Please enter your last name")]);
            formValidation &= FormVerifier.checkField(formMail,  formMail,  [FormVerifier.verifyEmptyness("Please enter your mail"), FormVerifier.verifyMail("Email is invalid")]);
            formValidation &= FormVerifier.checkField(formPass,  formPass,  [FormVerifier.verifyEmptyness("Please enter a password"), FormVerifier.verifyLength("Password must be 10 characters long")]);

            if(formValidation){
                canPress = false;
                FormVerifier.validateForm("Creating account ...", "{{ route('login') }}");
            }
        }

        const togglePassword = document.getElementById('toggle-password');
        togglePassword.addEventListener('click', function () {
            const type = formPass.getAttribute('type') === 'password' ? 'text' : 'password';
            formPass.setAttribute('type', type);
            const icon = document.getElementById('pass-icon');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    </script>
@endsection
