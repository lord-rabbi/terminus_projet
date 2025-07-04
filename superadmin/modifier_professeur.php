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

$message = '';
$errors = [];

// Récupérer le professeur
$stmt = $pdo->prepare("SELECT * FROM professeurs WHERE id = ?");
$stmt->execute([$id]);
$prof = $stmt->fetch();

if (!$prof) {
    $errors[] = "Professeur introuvable.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $prof) {
    $nom_complet = trim($_POST['nom_complet'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (empty($nom_complet)) {
        $errors[] = "Le nom complet est requis.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Un email valide est requis.";
    }

    // Vérifier doublon email (sauf soi-même)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM professeurs WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Cet email est déjà utilisé par un autre professeur.";
    }

    if (empty($errors)) {
        // Si mot de passe vide → ne pas le changer
        if (!empty($mot_de_passe)) {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE professeurs SET nom_complet = ?, email = ?, mot_de_passe = ? WHERE id = ?");
            $stmt->execute([$nom_complet, $email, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE professeurs SET nom_complet = ?, email = ? WHERE id = ?");
            $stmt->execute([$nom_complet, $email, $id]);
        }

        $message = "✅ Professeur modifié avec succès.";
        // Rafraîchir les infos
        $stmt = $pdo->prepare("SELECT * FROM professeurs WHERE id = ?");
        $stmt->execute([$id]);
        $prof = $stmt->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Modifier Professeur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #1f1f1f;
            color: #f0f0f0;
            padding: 2rem 1rem;
        }
        .container {
            max-width: 480px;
        }
        .btn-primary {
            background-color: #4a90e2;
            border: none;
        }
        .btn-primary:hover {
            background-color: #357ab7;
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
        label {
            color: #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Modifier un professeur</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message ?></div>
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

        <?php if ($prof): ?>
            <form method="post" novalidate>
                <div class="mb-3">
                    <label for="nom_complet">Nom complet</label>
                    <input type="text" name="nom_complet" id="nom_complet" class="form-control"
                           value="<?= htmlspecialchars($prof['nom_complet']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email">Adresse email</label>
                    <input type="email" name="email" id="email" class="form-control"
                           value="<?= htmlspecialchars($prof['email']) ?>" required>
                </div>
                <div class="mb-4">
                    <label for="mot_de_passe">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                    <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
                <a href="dashboard.php" class="btn btn-secondary mt-3 w-100 text-center">Accueil</a>
                
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
