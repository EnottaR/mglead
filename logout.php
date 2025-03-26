<?php
session_start();

$_SESSION = array();

// Cancello i cookie di sessione, se presenti
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Spacca sessione
session_destroy();

sleep(5);

header("Location: login.php");
exit;
?>
