@extends('layout.main')

@section('title')
    <title>Account creation - Ticketing App</title>
@endsection

@section('resources')
    <script src="{{ asset("utils/js/password-toggle.js") }}" defer></script>
@endsection

@section('content')
    <!-- Main Content -->
    <div id="login-box">
        <form id="creation-form" class="form-content card-wo-hover" method="POST" action="{{ route('registering') }}">
            @csrf
            <h2 style="text-align: center">Account creation</h2>

            @if ($errors->any())
                <div class="alert alert--error">
                    <ul style="margin: 0; padding-left: 1.2em;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="display: flex; gap: var(--spacing-md);">
                <div class="form-item-stacked" style="flex: 1;">
                    <label for="form-first">First Name</label>
                    <input type="text" id="form-first" name="first_name" placeholder="First Name" value="{{ old('first_name') }}">
                </div>

                <div class="form-item-stacked" style="flex: 1;">
                    <label for="form-last">Last Name</label>
                    <input type="text" id="form-last" name="last_name" placeholder="Last name" value="{{ old('last_name') }}">
                </div>
            </div>

            <div class="form-item-stacked">
                <label for="form-email">Email</label>
                <input type="email" id="form-email" name="email" placeholder="Email" required value="{{ old('email') }}">
            </div>

            <div class="form-item-stacked">
                <label for="form-pass">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="form-pass" name="password" placeholder="Password">
                    <span id="toggle-password" class="toggle-password"><i id="pass-icon" class="fa-solid fa-eye"></i></span>
                </div>
            </div>

            <div class="form-item-stacked">
                <label for="form-pass-confirm">Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" id="form-pass-confirm" name="password_confirmation" placeholder="Confirm Password">
                    <span id="toggle-confirm-pass" class="toggle-password"><i id="pass-icon" class="fa-solid fa-eye"></i></span>
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

        let formFirst   = document.getElementById("form-first");
        let formLast    = document.getElementById("form-last");
        let formMail    = document.getElementById("form-email");
        let formPass    = document.getElementById("form-pass");
        let formConfirm = document.getElementById("form-pass-confirm");

        let canPress = true;

        let formButton = document.getElementById("actions");
        formButton.addEventListener("click", (e) => {
            e.preventDefault();
            verifyForm();
        });

        function verifyForm(){
            let formValidation = true;
            FormVerifier.resetFormState([formMail, formFirst, formLast, formPass, formConfirm]);

            formValidation &= FormVerifier.checkField(formFirst,   formFirst,   [FormVerifier.verifyEmptyness("Please enter your first name"),  FormVerifier.verifyLengthName("First name must be lesser than 40 characters")]);
            formValidation &= FormVerifier.checkField(formLast,    formLast,    [FormVerifier.verifyEmptyness("Please enter your last name"),  FormVerifier.verifyLengthName("Last name must be lesser than 40 characters")]);
            formValidation &= FormVerifier.checkField(formMail,    formMail,    [FormVerifier.verifyEmptyness("Please enter your mail"), FormVerifier.verifyMail("Email is invalid")]);
            formValidation &= FormVerifier.checkField(formPass,    formPass,    [FormVerifier.verifyEmptyness("Please enter a password"), FormVerifier.verifyLength("Password must be 8 characters long")]);
            formValidation &= FormVerifier.checkField(formConfirm, formConfirm, [FormVerifier.verifyEmptyness("Please confirm your password")]);

            if(formValidation && formPass.value !== formConfirm.value){
                FormVerifier.resetFormState([formConfirm]);
                FormVerifier.checkField(formConfirm, formConfirm, [() => "Passwords do not match"]);
                formValidation = false;
            }

            if(formValidation){
                canPress = false;
                FormVerifier.validateForm("Creating account ...");
                document.getElementById("creation-form").submit();
            }
        }
    </script>
@endsection
