<?php
session_start();
require_once '../bdd/db.php';

if (!isset($_SESSION['professeur_id'])) {
    header('Location: connexion.php');
    exit;
}

$professeur_id = $_SESSION['professeur_id'];
$message = '';
$qr_data_json = '';

$stmt = $pdo->prepare("SELECT id, nom FROM cours WHERE professeur_id = ?");
$stmt->execute([$professeur_id]);
$cours = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cours_id = (int)$_POST['cours_id'];
    $type_pointage = $_POST['type_pointage']; // debut ou fin

    if (!$cours_id || !in_array($type_pointage, ['debut', 'fin'])) {
        $message = 'Informations invalides.';
    } else {
        $secret = 'MA_CLE_SECRETE';
        $timestamp = time();

        $token = hash_hmac('sha256', $cours_id . $type_pointage . $timestamp, $secret);

        $qr_payload = [
            'cours_id' => $cours_id,
            'type_pointage' => $type_pointage,
            'timestamp' => $timestamp,
            'token' => $token
        ];

        $qr_data_json = json_encode($qr_payload);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Générer QR Code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
   
    
</head> <style>
        body {
            background-color: #1f1f1f;
            color: #f0f0f0;
            padding: 2rem 1rem;
            min-height: 100vh;
        }
        .container {
            max-width: 700px;
            margin: auto;
        }

        .my-3{
            background: white;
            height: 300px;
            width: 300px;
            padding: 20px;
            margin-left: 200px;
        }
       
    </style>
<body class="p-4">
<div class="container">
    <h2 class="mb-4">Générer un QR Code de présence</h2>

    <form method="post" class="row g-3">
        <div class="col-md-6">
            <label for="cours_id" class="form-label">Cours</label>
            <select name="cours_id" id="cours_id" class="form-select" required>
                <option value="">-- Sélectionnez un cours --</option>
                <?php foreach ($cours as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Type de pointage</label>
            <select name="type_pointage" class="form-select" required>
                <option value="debut">Début</option>
                <option value="fin">Fin</option>
            </select>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Générer QR Code</button>
            <a href="dashboard.php" class="btn btn-secondary ms-2">Retour</a>
        </div>
    </form>

    <?php if ($qr_data_json): ?>
        <div class="mt-5 text-center">
            <h4>QR Code généré</h4>
            <div id="qrcode" class="my-3"></div>
            <script>
                new QRCode(document.getElementById("qrcode"), {
                    text: <?= json_encode($qr_data_json) ?>,
                    width: 256,
                    height: 256
                });
            </script>
        </div>
    <?php elseif ($message): ?>
        <div class="alert alert-danger mt-4"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
</div>
</body>
</html>