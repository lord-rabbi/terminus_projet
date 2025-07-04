<?php
session_start();

if (isset($_SESSION['superadmin_logged']) && $_SESSION['superadmin_logged'] === true) {
    header("Location: superadmin/dashboard.php");
    exit();
}

if (isset($_SESSION['professeur_logged']) && $_SESSION['professeur_logged'] === true) {
    header("Location: professeur/dashboard.php");
    exit();
}

if (isset($_SESSION['etudiant_logged']) && $_SESSION['etudiant_logged'] === true) {
    header("Location: etudiant/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue - Gestion Présence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1f1f1f;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            max-width: 400px;
            background-color: #2a2a2a;
            padding: 2rem;
            border-radius: 8px;
        }
        .btn {
            width: 100%;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="container text-center">
    <h2 class="mb-4">Connexion à l’espace</h2>
    <a href="superadmin/connexion.php" class="btn btn-outline-light">Superadmin</a>
    <a href="professeur/connexion.php" class="btn btn-outline-light">Professeur</a>
    <a href="etudiant/connexion.php" class="btn btn-outline-light">Étudiant</a>
</div>
</body>
</html>
