<?php
session_start();
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/csrf.php';

$messaggio = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica del token CSRF
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!validate_csrf_token($csrf_token)) {
        die("Errore di sicurezza: token CSRF non valido.");
    }

    if (isset($_POST['register'])) {
        // Registrazione utente
        $email = filter_var($_POST['registra_mail'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['register_password'];
        $nome = $_POST['nome'] ?? '';
        $cognome = $_POST['cognome'] ?? '';
        $messaggio = register_user($conn, $nome, $cognome, $email, $password);
    } elseif (isset($_POST['login'])) {
        // Login utente
        $login = trim($_POST['email']);
        $password = $_POST['password'];
        if (login_user($conn, $login, $password)) {
            header("Location: dashboard.php");
            exit;
        } else {
            $messaggio = "Email o password errati. Controlla e riprova.";
        }
    }
}

// Genera token CSRF
$csrf_token = generate_csrf_token();
if (isset($conn)) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeadAI - Accedi</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Jost', sans-serif;
        }
        
        body {
            background-color: #e9ebf0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            display: flex;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 850px;
            max-width: 95%;
            position: relative;
        }
        
        .image-container {
            flex: 1;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .image-container img {
            max-width: 100%;
            max-height: 450px;
        }
        
        .login-container {
            flex: 1;
            padding: 50px 40px;
            position: relative;
        }
        
        /* Notifica per messaggi di errore/successo */
        .notification {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
            transition: opacity 0.3s;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .msg-errore {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .login-header {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .login-header h1 {
            font-size: 32px;
            font-weight: bold;
            color: #1f1c2e;
            margin-bottom: 10px;
        }
        
        .login-header p {
            font-size: 16px;
            color: #555;
        }
        
        .social-login {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .social-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .social-btn:hover {
            background-color: #f8f8f8;
        }
        
        .divisorio {
            text-align: center;
            margin: 20px 0;
            color: #777;
            position: relative;
        }
        
        .divisorio::before, .divisorio::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background-color: #ddd;
        }
        
        .divisorio::before {
            left: 0;
        }
        
        .divisorio::after {
            right: 0;
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
        }
        
        .input-container {
            position: relative;
            width: 100%;
            opacity: 1;
            transform: translateY(0);
        }
        
        .input-container input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        
        .input-container input:focus {
            border-color: #1f1c2e;
            outline: none;
        }
        
        .password-container {
            position: relative;
            width: 100%;
            opacity: 1;
            transform: translateY(0);
        }
        
        .password-container input {
            width: 100%;
            padding: 12px 15px;
            padding-right: 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .password-container input:focus {
            border-color: #1f1c2e;
            outline: none;
        }
        
        .pass-icona {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
            transition: color 0.3s;
        }
        
        .pass-icona:hover {
            color: #1f1c2e;
        }
        
        button[type="submit"] {
            background-color: #1f1c2e;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 10px;
            width: 100%;
        }
        
        button[type="submit"]:hover {
            background-color: #243244;
        }
        
        #toggle-button {
            background-color: transparent;
            color: #1f1c2e;
            border: 1px solid #1f1c2e;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        
        #toggle-button:hover {
            background-color: #1f1c2e;
            color: white;
        }
        
        .accedi-link {
            cursor: pointer;
            color: #1f1c2e;
            display: inline-block;
            font-size: 14px;
            transition: color 0.3s;
            margin-top: 15px;
        }
        
        .accedi-link:hover {
            text-decoration: underline;
        }
        
        /* Animazioni per i form */
        #login-fields, #register-fields {
            transition: transform 0.6s ease, opacity 0.6s ease;
            position: relative;
            width: 100%;
        }
        
        #login-fields.active, #register-fields.active {
            transform: translateY(0);
            opacity: 1;
            z-index: 1;
            display: block;
        }
        
        #login-fields.hidden, #register-fields.hidden {
            display: none;
            opacity: 0;
            z-index: 0;
        }
        
        /* Indicatore forza password */
        .password-strength {
            width: 100%;
            margin-top: 5px;
        }
        
        .strength-bar {
            width: 100%;
            height: 5px;
            background-color: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
        }
        
        #progress-bar {
            height: 100%;
            width: 0;
            background-color: #ff4d4d;
            transition: width 0.3s, background-color 0.3s;
        }
        
        .strength-text {
            font-size: 12px;
            margin-top: 5px;
            color: #777;
            text-align: left;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            background-color: transparent;
        }
        
        .footer-left {
            display: flex;
            gap: 10px;
        }
        
        .footer-btn {
            background-color: white;
            border: 1px solid #ddd;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            cursor: pointer;
        }
        
        .footer-right {
            display: flex;
            gap: 10px;
        }
        
        .dots {
            display: flex;
            gap: 5px;
            margin-right: 20px;
        }
        
        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #ddd;
        }
        
        .dot.active {
            background-color: #cc0044;
        }
        
        .dot:nth-child(2) {
            background-color: #0066cc;
        }
        
        .dot:nth-child(3) {
            background-color: #00cc66;
        }
        
        .dot:nth-child(4) {
            background-color: #8800cc;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-container">
            <img src="https://cdn.pixabay.com/photo/2018/09/27/09/22/artificial-intelligence-3706562_1280.jpg" alt="Robot Image">
        </div>
        <div class="login-container">
            <div class="login-header">
                <h1 id="form-title">Sign in</h1>
                <p>Accedi al tuo account LeadAI</p>
            </div>
            
            <?php if (!empty($messaggio)): ?>
                <div class="notification <?= strpos(is_array($messaggio) ? ($messaggio['message'] ?? '') : $messaggio, '✅') !== false ? 'success' : 'msg-errore'; ?>">
                    <?= htmlspecialchars(is_array($messaggio) ? ($messaggio['message'] ?? '') : $messaggio); ?>
                </div>
                <script>
                    setTimeout(() => {
                        let notification = document.querySelector('.notification');
                        if (notification) {
                            notification.style.opacity = '0';
                            setTimeout(() => notification.remove(), 300);
                        }
                    }, 3000);
                </script>
            <?php endif; ?>
            
            <div id="login-fields" class="active">
                <div class="social-login">
                    <button type="button" class="social-btn">
                        <img src="https://img.icons8.com/color/24/000000/google-logo.png" alt="Google">
                        Google
                    </button>
                    <button type="button" class="social-btn">
                        <img src="https://img.icons8.com/ios-filled/24/000000/mac-os.png" alt="Apple">
                        Apple ID
                    </button>
                </div>
                
                <div class="divisorio">Oppure accedi con email</div>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="login" value="1">
                    
                    <div class="input-container show">
                        <input type="email" name="email" placeholder="Email*" required>
                    </div>
                    
                    <div class="password-container show">
                        <input type="password" name="password" id="password" placeholder="Password*" required>
                        <i id="toggle-password" class="pass-icona fas fa-eye"></i>
                    </div>
                    
                    <button type="submit">Accedi</button>
                </form>
                
                <p class="divisorio">- oppure -</p>
                
                <button id="toggle-button" onclick="toggleForms(true)">Registrati</button>
            </div>
            
            <div id="register-fields" class="hidden">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="register" value="1">
                    
                    <div class="input-container">
                        <input type="text" name="nome" placeholder="Nome*" required>
                    </div>
                    
                    <div class="input-container">
                        <input type="text" name="cognome" placeholder="Cognome*" required>
                    </div>
                    
                    <div class="input-container">
                        <input type="email" name="registra_mail" placeholder="Email*" required>
                    </div>
                    
                    <div class="password-container">
                        <input type="password" name="register_password" id="register-password" placeholder="Password*" required>
                        <i id="toggle-register-password" class="pass-icona fas fa-eye"></i>
                    </div>
                    
                    <div class="password-strength" style="display: none;">
                        <div id="strength-bar" class="strength-bar">
                            <div id="progress-bar"></div>
                        </div>
                        <p id="strength-text" class="strength-text">Password debole</p>
                    </div>
                    
                    <button type="submit">Crea Account</button>
                </form>
                
                <p class="divisorio">- oppure -</p>
                
                <span class="accedi-link" onclick="toggleForms(false)">← Ho già un account</span>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <div class="footer-left">
            <button class="footer-btn">LEADAI</button>
            <button class="footer-btn">UI8</button>
            <button class="footer-btn">X</button>
        </div>
        <div class="footer-right">
            <div class="dots">
                <div class="dot active"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            <button class="footer-btn">SIGN IN</button>
            <button class="footer-btn">2024</button>
            <button class="footer-btn">LEADAI KIT</button>
        </div>
    </div>
    
    <script>
        // Toggle password visibility
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('toggle-password');
            const toggleRegisterPassword = document.getElementById('toggle-register-password');
            const passwordField = document.getElementById('password');
            const registerPasswordField = document.getElementById('register-password');
            const formTitle = document.getElementById('form-title');
            const strengthBar = document.querySelector('.password-strength');
            
            // Toggle login password visibility
            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
            
            // Toggle register password visibility
            if (toggleRegisterPassword) {
                toggleRegisterPassword.addEventListener('click', function() {
                    const type = registerPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    registerPasswordField.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
            
            // Password strength check
            if (registerPasswordField) {
                registerPasswordField.addEventListener('focus', function() {
                    strengthBar.style.display = 'block';
                });
                
                registerPasswordField.addEventListener('input', function() {
                    const password = this.value;
                    const progressBar = document.getElementById('progress-bar');
                    const strengthText = document.getElementById('strength-text');
                    
                    // Check password strength
                    let strength = 0;
                    if (password.length > 7) strength += 20;
                    if (password.match(/[A-Z]/)) strength += 20;
                    if (password.match(/[0-9]/)) strength += 20;
                    if (password.match(/[^A-Za-z0-9]/)) strength += 20;
                    if (password.length > 11) strength += 20;
                    
                    // Update progress bar
                    progressBar.style.width = strength + '%';
                    
                    // Change color based on strength
                    if (strength < 40) {
                        progressBar.style.backgroundColor = '#ff4d4d';
                        strengthText.textContent = 'Password debole';
                    } else if (strength < 70) {
                        progressBar.style.backgroundColor = '#ffaa00';
                        strengthText.textContent = 'Password media';
                    } else {
                        progressBar.style.backgroundColor = '#2ecc71';
                        strengthText.textContent = 'Password forte';
                    }
                });
            }
        });
        
        // Toggle between login and register forms
        function toggleForms(showRegister) {
            const loginFields = document.getElementById('login-fields');
            const registerFields = document.getElementById('register-fields');
            const formTitle = document.getElementById('form-title');
            
            if (showRegister) {
                loginFields.classList.remove('active');
                loginFields.classList.add('hidden');
                registerFields.classList.remove('hidden');
                registerFields.classList.add('active');
                formTitle.textContent = 'Registrati';
            } else {
                registerFields.classList.remove('active');
                registerFields.classList.add('hidden');
                loginFields.classList.remove('hidden');
                loginFields.classList.add('active');
                formTitle.textContent = 'Sign in';
            }
        }
    </script>
</body>
</html>