<?php
session_start();

if (!isset($_SESSION['professeur_logged']) || !isset($_SESSION['professeur_id'])) {
    header("Location: connexion.php");
    exit();
}

require_once '../bdd/db.php';

$stmt = $pdo->prepare("SELECT nom_complet FROM professeurs WHERE id = ?");
$stmt->execute([$_SESSION['professeur_id']]);
$prof = $stmt->fetch();

$nom = $prof ? $prof['nom_complet'] : 'Professeur';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Professeur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #1f1f1f;
            color: #f0f0f0;
            padding: 2rem 1rem;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
        .btn {
            font-size: 1.05rem;
            padding: 0.8rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn svg {
            width: 1.2rem;
            height: 1.2rem;
        }
        .btn-primary {
            background-color: #4a90e2;
            border: none;
        }
        .btn-primary:hover {
            background-color: #357ab7;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <h2 class="mb-4">Bonjour <?= htmlspecialchars($nom) ?></h2>

        <a href="cours.php" class="btn btn-danger w-100">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-journal-bookmark" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M6 8V1h1v6.117l.447-.276a.5.5 0 0 1 .553 0L8.5 7.118l.5-.277a.5.5 0 0 1 .553 0L10 7.117V1h1v7a.5.5 0 0 1-.757.429L9 7.618l-.743.448A.5.5 0 0 1 7.5 8l-.743-.448L6 8z"/>
              <path d="M4 0h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.757.429L10 14.117l-3.243 1.812A.5.5 0 0 1 6 15.5V1H4a1 1 0 0 0-1 1v1h1v12h1V2a1 1 0 0 1 1-1z"/>
            </svg>
            Gérer mes cours
        </a>

        <a href="generer_qr.php" class="btn btn-primary w-100">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-qr-code" viewBox="0 0 16 16">
              <path d="M2 2h2v2H2V2Zm1 1V3H3v1h1V3H3Zm3 0V2h2v2H6V3Zm1-1H7v1h1V2H7Zm3 1h1v1h-1V3Zm1-1v2H9V2h2Zm-2 1h1V2h-1v1Zm-6 6H2v2h2V9ZM2 10V9h1v1H2Zm5-1h2v2H7V9Zm1 1V9H7v1h1Zm3-1h2v2h-2V9Zm2 1v-1h-1v1h1ZM2 14v-2h2v2H2Zm1-1v1h1v-1H3Zm3 0h2v2H6v-2Zm1 1v-1H7v1h1Zm3-1h2v2h-2v-2Zm1 1v-1h-1v1h1Z"/>
              <path fill-rule="evenodd" d="M0 0h6v6H0V0Zm1 1v4h4V1H1Zm8 0h6v6H9V0Zm1 1v4h4V1h-4ZM0 9h6v6H0V9Zm1 1v4h4v-4H1Zm8 0h3v1h-2v1h2v3h-3v-2h-1v-1h1v-1H9v-1Zm4 1h1v1h-1v-1Zm-1 2h1v2h-1v-2Zm1 0h1v1h-1v-1Z"/>
            </svg>
            Générer un QR code
        </a>

        <a href="afficher_presences.php" class="btn btn-info w-100">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
              <path d="M13 7a2 2 0 1 0-1.999-2.001A2 2 0 0 0 13 7Zm0 1c-1.098 0-2.063.55-2.598 1.378a1 1 0 0 0-.161.576V11h5v-.046a1 1 0 0 0-.161-.576C15.063 8.55 14.098 8 13 8Zm-9-1a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 1c-1.098 0-2.063.55-2.598 1.378A1 1 0 0 0 1 10.954V11h5v-.046a1 1 0 0 0-.161-.576C4.063 8.55 3.098 8 2 8Zm6-1a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm-1 1c-.73 0-1.387.195-1.921.522A4.978 4.978 0 0 1 8 13a4.978 4.978 0 0 1 2.921-.478C10.387 10.195 9.73 10 9 10H7Z"/>
            </svg>
            Voir les présences
        </a>

        <a href="deconnexion.php" class="btn btn-secondary w-100 mt-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M10 15a1 1 0 0 1-1-1v-1h1v1h4V2h-4v1h-1V2a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-4Zm-5-6a.5.5 0 0 1 0-1h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3.999 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L10.793 9H5Z"/>
            </svg>
            Déconnexion
        </a>
    </div>
</body>
</html>
