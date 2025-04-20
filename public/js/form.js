document.addEventListener('DOMContentLoaded', function() {
    // Función para validar email
    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    // Función para validar contraseña
    function validatePassword(password) {
        return password.length >= 8;
    }

    // Obtener todos los inputs
    const inputs = document.querySelectorAll('.input-group-outline input');
    
    // Agregar eventos a cada input
    inputs.forEach(input => {
        // Verificar si el input ya tiene valor al cargar
        if (input.value !== '') {
            const inputGroup = input.closest('.input-group-outline');
            inputGroup.classList.add('is-filled');
            
            if (input.type === 'email') {
                if (validateEmail(input.value)) {
                    inputGroup.classList.add('is-valid');
                    inputGroup.classList.remove('is-invalid');
                } else {
                    inputGroup.classList.add('is-invalid');
                    inputGroup.classList.remove('is-valid');
                }
            } else if (input.type === 'password') {
                // Si es confirmación de contraseña, verificar que coincida
                if (input.id === 'password-confirmation') {
                    const password = document.getElementById('password');
                    if (password && input.value === password.value) {
                        inputGroup.classList.add('is-valid');
                        inputGroup.classList.remove('is-invalid');
                    } else {
                        inputGroup.classList.add('is-invalid');
                        inputGroup.classList.remove('is-valid');
                    }
                } else {
                    // Validación normal de contraseña
                    if (validatePassword(input.value)) {
                        inputGroup.classList.add('is-valid');
                        inputGroup.classList.remove('is-invalid');
                    } else {
                        inputGroup.classList.add('is-invalid');
                        inputGroup.classList.remove('is-valid');
                    }
                    
                    // Si existe confirmación de contraseña, validarla también
                    const confirmPassword = document.getElementById('password-confirmation');
                    if (confirmPassword && confirmPassword.value !== '') {
                        const confirmGroup = confirmPassword.closest('.input-group-outline');
                        if (confirmPassword.value === input.value) {
                            confirmGroup.classList.add('is-valid');
                            confirmGroup.classList.remove('is-invalid');
                        } else {
                            confirmGroup.classList.add('is-invalid');
                            confirmGroup.classList.remove('is-valid');
                        }
                    }
                }
            } else {
                // Para otros tipos de input
                if (input.value.trim() !== '') {
                    inputGroup.classList.add('is-valid');
                    inputGroup.classList.remove('is-invalid');
                } else {
                    inputGroup.classList.add('is-invalid');
                    inputGroup.classList.remove('is-valid');
                }
            }
        }
        
        // Evento focus para manejar el estado de los inputs
        input.addEventListener('focus', function() {
            const inputGroup = this.closest('.input-group-outline');
            inputGroup.classList.add('is-focused');
        });
        
        // Evento input para validación en tiempo real
        input.addEventListener('input', function() {
            const inputGroup = this.closest('.input-group-outline');
            
            if (this.value !== '') {
                inputGroup.classList.add('is-filled');
            } else {
                inputGroup.classList.remove('is-filled');
            }
            
            if (this.type === 'email') {
                if (this.value === '') {
                    inputGroup.classList.remove('is-valid');
                    inputGroup.classList.remove('is-invalid');
                } else if (validateEmail(this.value)) {
                    inputGroup.classList.add('is-valid');
                    inputGroup.classList.remove('is-invalid');
                } else {
                    inputGroup.classList.add('is-invalid');
                    inputGroup.classList.remove('is-valid');
                }
            } else if (this.type === 'password') {
                // Si es confirmación de contraseña
                if (this.id === 'password-confirmation') {
                    const password = document.getElementById('password');
                    if (this.value === '') {
                        inputGroup.classList.remove('is-valid');
                        inputGroup.classList.remove('is-invalid');
                    } else if (password && this.value === password.value) {
                        inputGroup.classList.add('is-valid');
                        inputGroup.classList.remove('is-invalid');
                    } else {
                        inputGroup.classList.add('is-invalid');
                        inputGroup.classList.remove('is-valid');
                    }
                } else {
                    // Validación normal de contraseña
                    if (this.value === '') {
                        inputGroup.classList.remove('is-valid');
                        inputGroup.classList.remove('is-invalid');
                    } else if (validatePassword(this.value)) {
                        inputGroup.classList.add('is-valid');
                        inputGroup.classList.remove('is-invalid');
                    } else {
                        inputGroup.classList.add('is-invalid');
                        inputGroup.classList.remove('is-valid');
                    }
                    
                    // Si existe confirmación de contraseña, validarla también
                    const confirmPassword = document.getElementById('password-confirmation');
                    if (confirmPassword && confirmPassword.value !== '') {
                        const confirmGroup = confirmPassword.closest('.input-group-outline');
                        if (confirmPassword.value === this.value) {
                            confirmGroup.classList.add('is-valid');
                            confirmGroup.classList.remove('is-invalid');
                        } else {
                            confirmGroup.classList.add('is-invalid');
                            confirmGroup.classList.remove('is-valid');
                        }
                    }
                }
            } else {
                // Para otros tipos de input
                if (this.value === '') {
                    inputGroup.classList.remove('is-valid');
                    inputGroup.classList.remove('is-invalid');
                } else if (this.value.trim() !== '') {
                    inputGroup.classList.add('is-valid');
                    inputGroup.classList.remove('is-invalid');
                } else {
                    inputGroup.classList.add('is-invalid');
                    inputGroup.classList.remove('is-valid');
                }
            }
        });
        
        // Evento blur para validar cuando el usuario sale del campo
        input.addEventListener('blur', function() {
            const inputGroup = this.closest('.input-group-outline');
            inputGroup.classList.remove('is-focused');
            
            if (this.value === '') {
                inputGroup.classList.remove('is-filled');
                inputGroup.classList.remove('is-valid');
                inputGroup.classList.remove('is-invalid');
            } else {
                inputGroup.classList.add('is-filled');
                
                if (this.type === 'email' && !validateEmail(this.value)) {
                    inputGroup.classList.add('is-invalid');
                    inputGroup.classList.remove('is-valid');
                } else if (this.type === 'password') {
                    // Si es confirmación de contraseña
                    if (this.id === 'password-confirmation') {
                        const password = document.getElementById('password');
                        if (password && this.value !== password.value) {
                            inputGroup.classList.add('is-invalid');
                            inputGroup.classList.remove('is-valid');
                        } else {
                            inputGroup.classList.add('is-valid');
                            inputGroup.classList.remove('is-invalid');
                        }
                    } else if (!validatePassword(this.value)) {
                        inputGroup.classList.add('is-invalid');
                        inputGroup.classList.remove('is-valid');
                    } else {
                        inputGroup.classList.add('is-valid');
                        inputGroup.classList.remove('is-invalid');
                    }
                } else {
                    inputGroup.classList.add('is-valid');
                    inputGroup.classList.remove('is-invalid');
                }
            }
        });
    });

    // Validación del formulario antes de enviar
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            const emailInput = form.querySelector('input[type="email"]');
            const passwordInput = form.querySelector('input[type="password"][id="password"]');
            const confirmPasswordInput = form.querySelector('input[type="password"][id="password-confirmation"]');
            
            if (emailInput && !validateEmail(emailInput.value)) {
                const inputGroup = emailInput.closest('.input-group-outline');
                inputGroup.classList.add('is-invalid');
                inputGroup.classList.remove('is-valid');
                isValid = false;
            }
            
            if (passwordInput && !validatePassword(passwordInput.value)) {
                const inputGroup = passwordInput.closest('.input-group-outline');
                inputGroup.classList.add('is-invalid');
                inputGroup.classList.remove('is-valid');
                isValid = false;
            }
            
            // Verificar que las contraseñas coincidan
            if (passwordInput && confirmPasswordInput && passwordInput.value !== confirmPasswordInput.value) {
                const inputGroup = confirmPasswordInput.closest('.input-group-outline');
                inputGroup.classList.add('is-invalid');
                inputGroup.classList.remove('is-valid');
                isValid = false;
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    });
});