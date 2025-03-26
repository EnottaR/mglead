<?php
require 'includes/auth-checker.php';
$pageTitle = "Leads | LeadAI";
include 'includes/parts/header.php';
require 'includes/db.php';
require_once 'includes/functions/decrypt.php';

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

// Recuperiamo la chiave di crittografia dell'utente loggato
$stmt = $conn->prepare("SELECT encryption_key, type FROM clients WHERE id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$stmt->bind_result($encryption_key, $client_type);
$stmt->fetch();
$stmt->close();

// Recuperiamo tutti gli status disponibili per il client_type
$status_stmt = $conn->prepare("SELECT leads_status_id, label FROM status_labels WHERE clients_type = ?");
$status_stmt->bind_param("i", $client_type);
$status_stmt->execute();
$result = $status_stmt->get_result();
$status_options = [];
while ($row = $result->fetch_assoc()) {
    $status_options[$row['leads_status_id']] = $row['label'];
}
$status_stmt->close();

// Query per ottenere i lead associati all'utente loggato
$stmt = $conn->prepare("
    SELECT l.id, p.name, p.surname, p.email, l.phone, l.message, l.status_id, l.created_at, HEX(l.iv) as iv
    FROM leads l
    JOIN personas p ON l.personas_id = p.id
    WHERE l.clients_id = ?
    ORDER BY l.created_at DESC
");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
$leads = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

setlocale(LC_TIME, 'it_IT.UTF-8');
?>

<body>
    <div class="app-container">
        <?php include 'includes/parts/head.php'; ?>
        <div class="app-content">
            <?php include 'includes/parts/sidebar.php'; ?>
            <div class="projects-section" style="overflow: auto;">
                <div class="projects-section-header">
                    <p>I tuoi lead</p>
                    <button id="download-csv" class="btn-primary"><i class="fa-regular fa-file-excel"></i> Esporta in csv</button>
                </div>
                <div class="table-container">
                    <table class="leads-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Cognome</th>
                                <th>Email</th>
                                <th>Telefono</th>
                                <th>Status</th>
                                <th>Creato il</th>
                                <th>Messaggio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($leads) > 0): ?>
                                <?php foreach ($leads as $lead): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($lead['name']); ?></td>
                                        <td><?= htmlspecialchars($lead['surname']); ?></td>
                                        <td><?= htmlspecialchars($lead['email']); ?></td>
                                        <td><?= decryptData($lead['phone'], $lead['iv'], $encryption_key) ?></td>
                                        <td>
                                            <select class="status-select <?= 'status-' . $lead['status_id']; ?>" data-lead-id="<?= $lead['id']; ?>">
                                                <?php foreach ($status_options as $id => $label): ?>
                                                    <option value="<?= $id; ?>" <?= ($id == $lead['status_id']) ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($label); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="orario-creazione"><?= strftime("%d %b %Y • %H:%M", strtotime($lead['created_at'])); ?></td>
                                        <td class="lead-messaggio"><?= decryptData($lead['message'], $lead['iv'], $encryption_key) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center;">Nessun lead disponibile.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    <div id="notification-container"></div>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/leads.js"></script>
</body>