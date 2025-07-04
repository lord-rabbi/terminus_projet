<?php
session_start();

if (!isset($_SESSION['superadmin_logged']) || $_SESSION['superadmin_logged'] !== true) {
    header('Location: connexion.php');
    exit();
}

require_once '../bdd/db.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Location: dashboard.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM professeurs WHERE id = ?");
$stmt->execute([$id]);
$prof = $stmt->fetch();

if (!$prof) {
    header('Location: dashboard.php');
    exit();
}

$stmt = $pdo->prepare("DELETE FROM professeurs WHERE id = ?");
$stmt->execute([$id]);

header('Location: dashboard.php?msg=suppression_reussie');
exit();
?>
