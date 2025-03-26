<?php
require '../includes/db.php';
session_start();

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "⚠️ Utente non loggato."]);
    exit;
}

$client_id = $_SESSION['user_id'];

setlocale(LC_TIME, 'it_IT.UTF-8');

$stmt = $conn->prepare("
    SELECT p.name, p.surname, l.created_at 
    FROM leads l
    JOIN personas p ON l.personas_id = p.id
    WHERE l.clients_id = ? AND l.status_id = 1
    ORDER BY l.created_at DESC
");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

$leads = [];
while ($row = $result->fetch_assoc()) {
    $dataFormattata = strtotime($row['created_at']) * 1000; // il db è formattato in Y:m:d, la notifica non viene riconosciuta. Convertito in millisecondi
    $leads[] = [
        "name" => $row['name'],
        "surname" => $row['surname'],
        "created_at" => $dataFormattata // Passo il timestamp Unix in millisec
    ];    
}

echo json_encode([
    "status" => "success",
    "new_leads" => count($leads),
    "leads" => $leads
]);

$stmt->close();
$conn->close();
?>
