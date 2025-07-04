<?php
session_start();

require_once '../bdd/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (empty($email) || empty($mot_de_passe)) {
        $errors[] = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM professeurs WHERE email = ?");
        $stmt->execute([$email]);
        $prof = $stmt->fetch();

        if ($prof && password_verify($mot_de_passe, $prof['mot_de_passe'])) {
            $_SESSION['professeur_logged'] = true;
            $_SESSION['professeur_id'] = $prof['id'];
            header('Location: dashboard.php');
            exit();
        } else {
            $errors[] = "Identifiants incorrects.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Professeur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1f1f1f;
            color: #f0f0f0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            background-color: #2c2c2c;
            padding: 2rem;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
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
        }
        .btn-primary:hover {
            background-color: #357ab7;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h3 class="mb-4 text-center">Connexion Professeur</h3>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="email">Adresse email</label>
                <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>
    </div>
</body>
</html>
