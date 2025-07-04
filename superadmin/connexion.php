<?php
session_start();
require_once '../bdd/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = trim($_POST['nom_utilisateur']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM superadmins WHERE nom_utilisateur = ?");
    $stmt->execute([$nom_utilisateur]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($mot_de_passe, $admin['mot_de_passe'])) {
        $_SESSION['superadmin_logged'] = true;
        $_SESSION['superadmin_nom'] = $admin['nom_utilisateur'];
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Identifiants incorrects.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Important pour responsive -->
    <title>Connexion Superadmin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background-color: #1f1f1f;
            color: #f0f0f0;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1rem;
            padding: 1rem;
        }
        .login-card {
            background-color: #2b2b2b;
            border-radius: 1rem;
            padding: 2.5rem 3rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.6);
            width: 100%;
            max-width: 420px;
        }
        .form-control {
            background-color: #3a3a3a;
            color: #fff;
            border: none;
        }
        .form-control:focus {
            background-color: #444;
            color: #fff;
            box-shadow: none;
        }
        .btn-primary {
            background-color: #4a90e2;
            border: none;
            font-size: 1.2rem;
            padding: 0.75rem;
        }
        .btn-primary:hover {
            background-color: #357ab7;
        }
        .alert {
            margin-bottom: 1rem;
        }

        @media (max-width: 576px) {
            body {
                font-size: 1.2rem;
                padding: 2rem 1rem;
            }
            .login-card {
                max-width: 90vw;
                padding: 2rem 1.5rem;
                border-radius: 1rem;
            }
            .btn-primary {
                font-size: 1.3rem;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center mb-4">Connexion Superadmin</h3>

        <?php if ($message): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="mb-3">
                <label for="nom_utilisateur" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="nom_utilisateur" name="nom_utilisateur" required autofocus>
            </div>
            <div class="mb-4">
                <label for="mot_de_passe" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>
    </div>
</body>
</html>
