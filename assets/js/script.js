document.addEventListener("DOMContentLoaded", function () {
    window.toggleForms = function (isRegister) {
        const loginFields = document.getElementById("login-fields");
        const registerFields = document.getElementById("register-fields");
        const formTitle = document.getElementById("form-title");

        if (isRegister) {
            formTitle.innerText = "Registrati ora";
            document.title = "LeadAI - Registrati ora"; // Aggiorna il titolo della pagina

            loginFields.classList.add("hidden");
            loginFields.classList.remove("active");

            registerFields.classList.add("active");
            registerFields.classList.remove("hidden");

            setTimeout(() => {
                const registerInputs = registerFields.querySelectorAll(".input-container, .password-container");
                registerInputs.forEach(input => input.classList.add("show"));
            }, 100);
        } else {
            formTitle.innerText = "Login";
            document.title = "LeadAI - Accedi";

            registerFields.classList.add("hidden");
            registerFields.classList.remove("active");

            loginFields.classList.add("active");
            loginFields.classList.remove("hidden");

            setTimeout(() => {
                const loginInputs = loginFields.querySelectorAll(".input-container, .password-container");
                loginInputs.forEach(input => input.classList.add("show"));
            }, 100);
        }
    };
});

document.addEventListener("DOMContentLoaded", function () {
    const togglePassword = document.getElementById("toggle-password");
    const passwordField = document.getElementById("password");

    togglePassword.addEventListener("click", function () {
        if (passwordField.type === "password") {
            passwordField.type = "text";
            togglePassword.classList.remove("fa-eye");
            togglePassword.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            togglePassword.classList.remove("fa-eye-slash");
            togglePassword.classList.add("fa-eye");
        }
    });

    const toggleRegisterPassword = document.getElementById("toggle-register-password");
    const registerPasswordField = document.getElementById("register-password");

    toggleRegisterPassword.addEventListener("click", function () {
        if (registerPasswordField.type === "password") {
            registerPasswordField.type = "text";
            toggleRegisterPassword.classList.remove("fa-eye");
            toggleRegisterPassword.classList.add("fa-eye-slash");
        } else {
            registerPasswordField.type = "password";
            toggleRegisterPassword.classList.remove("fa-eye-slash");
            toggleRegisterPassword.classList.add("fa-eye");
        }
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const registerPasswordField = document.getElementById("register-password");
    const strengthBarContainer = document.querySelector(".password-strength");
    const progressBar = document.getElementById("progress-bar");
    const strengthText = document.getElementById("strength-text");

    if (registerPasswordField) {
        registerPasswordField.addEventListener("input", function () {
            const password = this.value;

            if (password.length > 0) {
                strengthBarContainer.style.display = "block";
            } else {
                strengthBarContainer.style.display = "none";
                return;
            }

            const strength = calculatePasswordStrength(password);

            let color;
            if (strength < 2) {
                color = "#ff4d4d";
                strengthText.innerText = "Complessità password: debole";
            } else if (strength < 4) {
                color = "#ffcc00";
                strengthText.innerText = "Complessità password: media";
            } else {
                color = "#4caf50";
                strengthText.innerText = "Complessità password: forte";
            }

            progressBar.style.backgroundColor = color;
            progressBar.style.width = `${(strength / 5) * 100}%`;
        });
    }

    function calculatePasswordStrength(password) {
        let score = 0;

        // Complessità password
        if (password.length >= 8) score++;

        if (/[A-Z]/.test(password)) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        return score;
    }
});