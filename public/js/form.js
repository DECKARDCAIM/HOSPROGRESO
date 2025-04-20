document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const nameInput = document.getElementById('name');

    const emailGroup = document.getElementById('emailGroup');
    const passwordGroup = document.getElementById('passwordGroup');
    const confirmGroup = document.getElementById('passwordConfirmationGroup');
    const nameGroup = document.getElementById('nameGroup');

    function validateInput(input, group, condition) {
        if (!input || !group) return;
        group.classList.remove('is-valid', 'is-invalid');
        if (input.value.trim() === '') return;
        group.classList.add(condition ? 'is-valid' : 'is-invalid');
    }

    function validateEmail() {
        const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim());
        validateInput(emailInput, emailGroup, valid);
    }

    function validatePassword() {
        const valid = passwordInput.value.trim().length >= 8;
        validateInput(passwordInput, passwordGroup, valid);
    }

    function validateConfirmPassword() {
        if (!confirmInput || !confirmGroup || !passwordInput) return; // <- evita el error
        const valid = passwordInput.value === confirmInput.value && confirmInput.value.trim().length >= 8;
        validateInput(confirmInput, confirmGroup, valid);
    }

    function validateName() {
        const valid = nameInput.value.trim().length >= 3;
        validateInput(nameInput, nameGroup, valid);
    }

    if (emailInput) {
        emailInput.addEventListener('input', validateEmail);
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', () => {
            validatePassword();
            validateConfirmPassword();
        });
    }

    if (confirmInput) {
        confirmInput.addEventListener('input', validateConfirmPassword);
    }

    if (nameInput) {
        nameInput.addEventListener('input', validateName);
    }
});