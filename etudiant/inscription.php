<?php
session_start();
require_once '../bdd/db.php';

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom_complet'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';
    $mdp_confirm = $_POST['mot_de_passe_confirm'] ?? '';

    if (!$nom || !$email || !$mdp || !$mdp_confirm) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Email invalide.";
    } elseif ($mdp !== $mdp_confirm) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $erreur = "Un compte avec cet email existe déjà.";
        } else {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO etudiants (nom_complet, email, mot_de_passe) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $email, $hash]);
            header("Location: connexion.php?inscription=ok");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Étudiant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1f1f1f;
            color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #2a2a2a;
            padding: 2rem;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
        }
        input, button {
            background-color: #3a3a3a;
            color: #f0f0f0;
            border: none;
        }
        .text-decoration-none {
            color: orange;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2 class="mb-4 text-center">Inscription Étudiant</h2>

    <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="mb-3">
            <label for="nom_complet" class="form-label">Nom complet</label>
            <input type="text" id="nom_complet" name="nom_complet" class="form-control" required value="<?= htmlspecialchars($_POST['nom_complet'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="mot_de_passe_confirm" class="form-label">Confirmer mot de passe</label>
            <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-secondary w-100">S’inscrire</button>
    </form>

    <p class="mt-3 text-center">
        Déjà un compte ? <a href="connexion.php" class="text-decoration-none">Se connecter</a>
    </p>
</div>
</body>
</html>
