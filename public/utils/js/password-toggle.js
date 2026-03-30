// Select all toggle spans in the document
const togglePasswordSpans = document.querySelectorAll('.toggle-password');

togglePasswordSpans.forEach(span => {
    span.addEventListener('click', function () {
        // Find the input field within this specific wrapper
        // '.previousElementSibling' works because the input is right before the span
        const input = this.parentElement.querySelector('input');
        // console.log(this);
        const icon = this.querySelector('svg');

        // Toggle the input type
        const isPassword = input.getAttribute('type') === 'password';
        input.setAttribute('type', isPassword ? 'text' : 'password');

        // Toggle the icon classes
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
});
