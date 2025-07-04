<?php
session_start();
require_once '../bdd/db.php';

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';

    if (!$email || !$mdp) {
        $erreur = "Tous les champs sont obligatoires.";
    } else {
        $stmt = $pdo->prepare("SELECT id, nom_complet, mot_de_passe FROM etudiants WHERE email = ?");
        $stmt->execute([$email]);
        $etudiant = $stmt->fetch();

        if (!$etudiant || !password_verify($mdp, $etudiant['mot_de_passe'])) {
            $erreur = "Email ou mot de passe incorrect.";
        } else {
            $_SESSION['etudiant_logged'] = true;
            $_SESSION['etudiant_id'] = $etudiant['id'];
            $_SESSION['etudiant_nom'] = $etudiant['nom_complet'];
            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Étudiant</title>
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
        input:focus {
            background-color: #444;
            color: #fff;
            box-shadow: none;
        }
        .btn-primary {
            background-color: #4a90e2;
        }
        .btn-primary:hover {
            background-color: #357ab7;
        }
        .text-decoration-none {
            color: orange;
        }
        .alert-danger {
            background-color: #842029;
            border: none;
            color: #f8d7da;
        }
        .alert-success {
            background-color: #2e7031;
            border: none;
            color: #d4edda;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2 class="mb-4 text-center">Connexion Étudiant</h2>

    <?php if (isset($_GET['inscription']) && $_GET['inscription'] === 'ok'): ?>
        <div class="alert alert-success">Inscription réussie, vous pouvez vous connecter.</div>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
    </form>

    <p class="mt-3 text-center">
        Pas encore de compte ? <a href="inscription.php" class="text-decoration-none">S’inscrire</a>
    </p>
</div>
</body>
</html>
