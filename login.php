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
$conn->close();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeadAI - Accedi</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="assets/js/script.js"></script>

</head>

<body>
    <div class="main">
        <div class="login">
            <h2 id="form-title">Login</h2>
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
                <form method="POST" action="login.php">
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
                <form method="POST" action="login.php">
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
                    <button type="submit" style="margin-bottom: 30px;">Crea Account</button>
                </form>
                <span class="accedi-link" id="toggle-button" onclick="toggleForms(false)">← Ho già un account</span>

            </div>
        </div>
    </div>

</body>

</html>