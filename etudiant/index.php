<?php
session_start();
if (!isset($_SESSION['etudiant_id']) || !isset($_SESSION['etudiant_nom'])) {
    header('Location: connexion.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Scanner de présence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white text-center p-4">
    <h2 class="mb-4">Bonjour <?= htmlspecialchars($_SESSION['etudiant_nom']) ?></h2>
    <p>Scanne le QR Code affiché par ton enseignant.</p>

    <div id="reader" style="width:300px;margin:auto;"></div>
    <div id="resultat" class="mt-3"></div>

    <a href="deconnexion.php" class="btn btn-danger mt-4">Déconnexion</a>

    <script>
        function onScanSuccess(decodedText) {
            html5QrcodeScanner.clear();
            document.getElementById('resultat').innerHTML = "Vérification en cours...";

            fetch('../valider_presence.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    etudiant_id: <?= json_encode($_SESSION['etudiant_id']) ?>,
                    qr_data: decodedText
                })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('resultat').innerHTML = data.success
                    ? `<div class='alert alert-success'>${data.message}</div>`
                    : `<div class='alert alert-danger'>${data.message}</div>`;
                setTimeout(() => location.reload(), 4000);
            })
            .catch(() => {
                document.getElementById('resultat').innerHTML = "<div class='alert alert-danger'>Erreur lors de l'envoi.</div>";
            });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>
