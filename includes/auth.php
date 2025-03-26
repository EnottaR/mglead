<?php
require_once 'db.php';

// Funzione registrazione utente
function register_user($conn, $nome, $cognome, $email, $password)
{
    try {
        $nome = ucfirst(strtolower(trim($nome)));
        $cognome = ucfirst(strtolower(trim($cognome)));

        // Check per vedere se l'email è già registrata
        if (email_exists($conn, $email)) {
            return [
                "type" => "error",
                "message" => "⚠️ Attenzione, l'indirizzo email inserito è già presente nei nostri sistemi."
            ];
        }

        $username = generate_unique_username($conn, $nome, $cognome);

        // Hash bcrypt
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $encryption_key = bin2hex(random_bytes(32));

        // Inserisco i dati nel db
        $stmt = $conn->prepare("INSERT INTO clients (name, surname, username, password, email, type, encryption_key) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $type = 1; // Utente predefinito (1 - Generico)
        $stmt->bind_param("sssssis", $nome, $cognome, $username, $hashed_password, $email, $type, $encryption_key);
        $stmt->execute();

        return [
            "type" => "success",
            "message" => "✅ Registrazione completata con successo! Ora puoi accedere."
        ];
    } catch (Exception $e) {
        return [
            "type" => "error",
            "message" => "❌ Errore nella registrazione. Riprova."
        ];
    }
}

function login_user($conn, $login, $password)
{
    try {
        // Controllo se l'utente esiste (login possibile sia con email che username)
        $stmt = $conn->prepare("SELECT * FROM clients WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $login, $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verifico la password
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true); // Rigenero la sessione per sicurezza
                
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['surname'] = $user['surname'];
                $_SESSION['email'] = $user['email'];

                return true;
            }
        }
    } catch (Exception $e) {
        return false;
    }

    return false;
}


// Verifico se l'email è già presente nel db
function email_exists($conn, $email)
{
    $stmt = $conn->prepare("SELECT id FROM clients WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

// Genero username unico, nel caso di omonimia, viene aggiungo il numero 1 all'username
function generate_unique_username($conn, $nome, $cognome)
{
    $base_username = strtolower(substr($nome, 0, 1) . $cognome);
    $username = $base_username;
    $suffix = 1;

    while (true) {
        $stmt = $conn->prepare("SELECT id FROM clients WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            return $username;
        }
        $username = $base_username . $suffix++;
    }
}
?> 