<?php
header('Content-Type: application/json');
require_once 'bdd/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['etudiant_id'], $data['qr_data'])) {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
    exit;
}

$etudiant_id = (int)$data['etudiant_id'];
$qr = json_decode($data['qr_data'], true);

if (!$qr || !isset($qr['cours_id'], $qr['type_pointage'], $qr['timestamp'], $qr['token'])) {
    echo json_encode(['success' => false, 'message' => 'QR Code invalide ou corrompu.']);
    exit;
}

$secret = 'MA_CLE_SECRETE';
$expected = hash_hmac('sha256', $qr['cours_id'] . $qr['type_pointage'] . $qr['timestamp'], $secret);

if (!hash_equals($expected, $qr['token'])) {
    echo json_encode(['success' => false, 'message' => 'QR Code falsifié ou expiré.']);
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM presences WHERE etudiant_id = ? AND cours_id = ? AND type_pointage = ?");
$stmt->execute([$etudiant_id, $qr['cours_id'], $qr['type_pointage']]);

if ($stmt->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'message' => 'Vous avez déjà pointé ce type de présence.']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO presences (etudiant_id, cours_id, date_heure, type_pointage) VALUES (?, ?, NOW(), ?)");
$stmt->execute([$etudiant_id, $qr['cours_id'], $qr['type_pointage']]);

echo json_encode(['success' => true, 'message' => 'Présence enregistrée avec succès !']);
