@extends('layout.main')

@section('title')
    <title>Reset password - Ticketing App</title>
@endsection

@section('resources')
@endsection

@section('content')
    <!-- Main Content -->
    <div id="login-box">
        <form id="reset-form" class="form-content card-wo-hover" method="POST">
            <h2 style="text-align: center">Reset password</h2>
            <p style="text-align: center; color: var(--text-secondary); margin-bottom: var(--spacing-lg);">
                Enter your email address to reset your password.
            </p>

            <div class="form-item-stacked">
                <label for="form-email">Email</label>
                <input type="email" id="form-email" name="email" placeholder="Email" required>
            </div>

            <div class="centered">
                <button class="btn" id="actions" type="submit">Send link</button>
            </div>

            <div class="centered" style="margin-top: var(--spacing-md);">
                <button onclick="location.href = '{{ route('login') }}'" type="button" class="btn btn--outline">Back to login</button>
            </div>
        </form>
    </div>
@endsection

@section('js_page')
    <script type="module">
        // Set as module to allow imports
        import * as FormVerifier from "{{ asset("utils/js/form-verifs.js") }}";

        // Get references to form's field
        let formMail = document.getElementById("form-email");

        // prevent multiple request when the infos are correct
        let canPress = true;

        // Get reference to form button
        let formButton = document.getElementById("actions");

        // Add click event on the previously got button
        formButton.addEventListener("click", (e) => {
            e.preventDefault();
            verifyForm();
        });

        function verifyForm(){
            let formValidation = true;
            // Clear previous highlights
            FormVerifier.resetFormState([formMail]);

            // check each field with its corresponding checks
            formValidation &= FormVerifier.checkField(formMail, formMail, [FormVerifier.verifyEmptyness("Please enter your mail"), FormVerifier.verifyMail("Email is invalid") ]);

            // if everything checks out
            if(formValidation){
                canPress = false;
                FormVerifier.validateForm("Sending reset link ...", " {{ route('login') }} ");
            }
        }
    </script>
@endsection



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../../src/assets/css/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js" defer></script>
</head>
<body>
</body>

</html>
