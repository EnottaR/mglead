<?php
$pdo = require_once 'db.php';

function getClientIP() {
    // verifica se l'IP è passato tramite un proxy o un bilanciatore di carico
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // se ci sono più indirizzi IP, il primo è l'IP originale
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        // se non c'è il proxy, prendi direttamente l'IP remoto
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // prendi solo il primo IP in caso di "X-Forwarded-For" con più IP
    if (strpos($ip, ',') !== false) {
        $ip = explode(',', $ip)[0];
    }
    return $ip;
}

$name = $_POST['name'] ?? '';
$surname = $_POST['surname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$message = $_POST['message'] ?? '';
$ip = getClientIP();
$clients_id = $_POST['clients_id'] ?? ''; // clients_id passato dal form

// verifica se il cliente esiste in base al clients_id
$queryClient = "SELECT id, encryption_key FROM clients WHERE id = :clients_id LIMIT 1";
$stmtClient = $pdo->prepare($queryClient);
$stmtClient->bindParam(':clients_id', $clients_id);
$stmtClient->execute();
$client = $stmtClient->fetch(PDO::FETCH_ASSOC);

if ($client) {
    // cifra i dati sensibili (telefono e messaggio)
    $encryption_key = $client['encryption_key']; // la chiave di cifratura del cliente
    $iv = openssl_random_pseudo_bytes(16); // genera un iv casuale di 16 byte

    // cifriamo i dati sensibili
    $encryptedPhone = openssl_encrypt($phone, 'aes-256-cbc', $encryption_key, 0, $iv);
    $encryptedMessage = openssl_encrypt($message, 'aes-256-cbc', $encryption_key, 0, $iv);

    // verifica se la persona esiste già
    $queryPersonas = "SELECT id FROM personas WHERE email = :email LIMIT 1";
    $stmtPersonas = $pdo->prepare($queryPersonas);
    $stmtPersonas->bindParam(':email', $email);
    $stmtPersonas->execute();
    $persona = $stmtPersonas->fetch(PDO::FETCH_ASSOC);

    // se la persona esiste, usa il suo personas_id esistente
    if ($persona) {
        $personas_id = $persona['id'];
    } else {
        // se la persona non esiste, la inseriamo
        $queryInsertPersonas = "INSERT INTO personas (name, surname, email, created_at) 
                                 VALUES (:name, :surname, :email, NOW())";
        $stmtInsertPersonas = $pdo->prepare($queryInsertPersonas);
        $stmtInsertPersonas->bindParam(':name', $name);
        $stmtInsertPersonas->bindParam(':surname', $surname);
        $stmtInsertPersonas->bindParam(':email', $email);

        if ($stmtInsertPersonas->execute()) {
            $personas_id = $pdo->lastInsertId();
        } else {
            echo "errore nell'inserimento della persona.";
            exit;
        }
    }

    // estrai il dominio dal referer
    $refererUrl = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST); // estrae solo il dominio
    if ($refererUrl) {
        // rimuovi il prefisso 'www.' se presente
        $refererUrl = preg_replace('/^www\./', '', $refererUrl);
    } else {
        echo "errore: nessun referer trovato.";
        exit;
    }

    // verifica che il sito provenga dal cliente
    $queryWebsite = "SELECT id FROM websites WHERE url LIKE :url AND clients_id = :clients_id LIMIT 1";
    $stmtWebsite = $pdo->prepare($queryWebsite);

    // Usa il wildcard per LIKE nel parametro, aggiungendo i simboli % attorno al valore
    $likeUrl = '%' . $refererUrl . '%'; // Aggiungi i % prima e dopo il dominio

    $stmtWebsite->bindParam(':url', $likeUrl); // Parametro correttamente legato
    $stmtWebsite->bindParam(':clients_id', $client['id']);
    $stmtWebsite->execute();
    $website = $stmtWebsite->fetch(PDO::FETCH_ASSOC);

    if ($website) {
        // inserisci il lead nella tabella leads
        $insertLeadQuery = "INSERT INTO leads (phone, message, ip, status_id, created_at, clients_id, personas_id, websites_id, iv)
                            VALUES (:phone, :message, :ip, 1, NOW(), :clients_id, :personas_id, :websites_id, :iv)";
        $stmtLead = $pdo->prepare($insertLeadQuery);

        $stmtLead->bindParam(':phone', $encryptedPhone);
        $stmtLead->bindParam(':message', $encryptedMessage);
        $stmtLead->bindParam(':ip', $ip);
        $stmtLead->bindParam(':clients_id', $client['id']);
        $stmtLead->bindParam(':personas_id', $personas_id);
        $stmtLead->bindParam(':websites_id', $website['id']);
        $stmtLead->bindParam(':iv', $iv);  // memorizza anche l'iv

        if ($stmtLead->execute()) {
            echo "lead inserito con successo!";
        } else {
            echo "errore nell'inserimento del lead.";
        }
    } else {
        echo "il sito non appartiene al cliente.";
    }
} else {
    echo "cliente non trovato.";
}
?>