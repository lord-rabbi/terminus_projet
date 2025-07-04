<?php
session_start();

if (!isset($_SESSION['superadmin_logged']) || $_SESSION['superadmin_logged'] !== true) {
    header('Location: connexion.php');
    exit();
}

require_once '../bdd/db.php';

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_complet = trim($_POST['nom_complet'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $mot_de_passe_confirm = $_POST['mot_de_passe_confirm'] ?? '';

    if (empty($nom_complet)) {
        $errors[] = "Le nom complet est requis.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Un email valide est requis.";
    }
    if (empty($mot_de_passe)) {
        $errors[] = "Le mot de passe est requis.";
    }
    if ($mot_de_passe !== $mot_de_passe_confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM professeurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Cet email est déjà utilisé.";
        } else {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO professeurs (nom_complet, email, mot_de_passe) VALUES (?, ?, ?)");
            $stmt->execute([$nom_complet, $email, $hash]);
            $message = "✅ Professeur ajouté avec succès.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ajouter Professeur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #1f1f1f;
            color: #f0f0f0;
            padding: 2rem 1rem;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 480px;
        }
        .btn-primary {
            background-color: #4a90e2;
            border: none;
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }
        .btn-primary:hover {
            background-color: #357ab7;
        }
        label {
            color: #ddd;
        }
        .form-control {
            background-color: #3a3a3a;
            border: none;
            color: #fff;
        }
        .form-control:focus {
            background-color: #444;
            color: #fff;
            box-shadow: none;
        }
        .alert {
            margin-bottom: 1rem;
        }
    </style>
    <script>
        function validateForm() {
            const pwd = document.getElementById('mot_de_passe').value;
            const pwdConfirm = document.getElementById('mot_de_passe_confirm').value;
            if (pwd !== pwdConfirm) {
                alert('Les mots de passe ne correspondent pas.');
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Ajouter un professeur</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" novalidate onsubmit="return validateForm()">
            <div class="mb-3">
                <label for="nom_complet">Nom complet</label>
                <input type="text" id="nom_complet" name="nom_complet" class="form-control" value="<?= htmlspecialchars($_POST['nom_complet'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
            </div>
            <div class="mb-4">
                <label for="mot_de_passe_confirm">Confirmer le mot de passe</label>
                <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ajouter</button>
            <a href="dashboard.php" class="btn btn-secondary mt-3 w-100 text-center">Accueil</a>
        </form>
    </div>
</body>
</html>
