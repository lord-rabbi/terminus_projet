<?php
session_start();
if (!isset($_SESSION['professeur_logged']) || !isset($_SESSION['professeur_id'])) {
    header("Location: connexion.php");
    exit();
}

require_once '../bdd/db.php';
$prof_id = $_SESSION['professeur_id'];
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    if (empty($nom)) {
        $errors[] = "Le nom du cours est requis.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO cours (nom, professeur_id) VALUES (?, ?)");
        $stmt->execute([$nom, $prof_id]);
        $success = "Cours ajouté avec succès.";
    }
}

if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $idCours = (int)$_GET['supprimer'];
    $stmt = $pdo->prepare("DELETE FROM cours WHERE id = ? AND professeur_id = ?");
    $stmt->execute([$idCours, $prof_id]);
    $success = "Cours supprimé.";
}

$stmt = $pdo->prepare("SELECT * FROM cours WHERE professeur_id = ? ORDER BY id DESC");
$stmt->execute([$prof_id]);
$cours = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes cours</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1f1f1f;
            color: #f0f0f0;
            padding: 2rem 1rem;
        }
        .container {
            max-width: 700px;
        }
        .form-control {
            background-color: #3a3a3a;
            color: #fff;
            border: none;
        }
        .form-control:focus {
            background-color: #444;
            box-shadow: none;
            color: #fff;
        }
        .btn-primary {
            background-color: #4a90e2;
            border: none;
        }
        .btn-primary:hover {
            background-color: #357ab7;
        }
        .btn-danger {
            font-size: 0.9rem;
            padding: 0.35rem 0.6rem;
        }
        .btn svg {
            vertical-align: middle;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-journals" viewBox="0 0 16 16">
                <path d="M1 2.828c.885-.37 2.154-.713 3.5-.828V14c-1.346.115-2.615.457-3.5.828V2.828z"/>
                <path d="M14 2.828v12c-.885-.37-2.154-.713-3.5-.828V2c1.346.115 2.615.457 3.5.828z"/>
                <path d="M7.5 1.018A13.134 13.134 0 0 0 4.5 1v14c1.066 0 2.112.122 3 .335 0-1.905.138-4.463.5-6.335.362-1.872.5-4.43.5-6.335a13.134 13.134 0 0 0-.5-.982z"/>
            </svg>
            Mes cours
        </h2>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="mb-4 d-flex gap-2">
            <input type="text" name="nom" class="form-control" placeholder="Nom du cours" required>
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                    <path d="M8 1a7 7 0 1 1 0 14A7 7 0 0 1 8 1zm0 1a6 6 0 1 0 0 12A6 6 0 0 0 8 2z"/>
                    <path d="M8 4a.5.5 0 0 1 .5.5V7h2.5a.5.5 0 0 1 0 1H8.5v2.5a.5.5 0 0 1-1 0V8H5a.5.5 0 0 1 0-1h2.5V4.5A.5.5 0 0 1 8 4z"/>
                </svg>
                Ajouter
            </button>
        </form>

        <table class="table table-dark table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom du cours</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cours)): ?>
                    <tr><td colspan="3" class="text-center">Aucun cours pour l’instant.</td></tr>
                <?php else: ?>
                    <?php foreach ($cours as $index => $c): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($c['nom']) ?></td>
                            <td>
                                <a href="?supprimer=<?= $c['id'] ?>" class="btn btn-danger" title="Supprimer" onclick="return confirm('Supprimer ce cours ?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                        <path d="M6.5 1V.5a.5.5 0 0 1 1 0V1h2V.5a.5.5 0 0 1 1 0V1h2.5A.5.5 0 0 1 13 1.5v.5H3v-.5A.5.5 0 0 1 3.5 1H6.5zm6 2V13a2 2 0 0 1-2 2H5.5a2 2 0 0 1-2-2V3h9zm-6.5 1a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0V4zm3 0a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0V4zm3 0a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0V4z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary mt-3">Accueil</a>
    </div>
</body>
</html>
