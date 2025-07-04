<?php
session_start();

if (!isset($_SESSION['superadmin_logged']) || $_SESSION['superadmin_logged'] !== true) {
    header('Location: connexion.php');
    exit();
}

require_once '../bdd/db.php';

$stmt = $pdo->query("SELECT id, nom_complet, email FROM professeurs ORDER BY nom_complet ASC");
$professeurs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Superadmin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background-color: #1f1f1f;
            color: #f0f0f0;
            font-size: 0.9rem;
        }
        body {
            padding: 2rem 1rem;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 960px;
        }
        h2 {
            margin-bottom: 1.5rem;
            color: #fff;
        }
        .btn-primary {
            background-color: #4a90e2;
            border: none;
            font-size: 1rem;
            padding: 0.4rem 0.9rem;
        }
        .btn-primary:hover {
            background-color: #357ab7;
        }
        .btn-danger {
            background-color: #d9534f;
            border: none;
            font-size: 1rem;
            padding: 0.4rem 0.9rem;
        }
        .btn-danger:hover {
            background-color: #c9302c;
        }
        .btn-warning {
            background-color: #f0ad4e;
            border: none;
            font-size: 1rem;
            padding: 0.4rem 0.9rem;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #ec971f;
            color: #212529;
        }
        table {
            font-size: 0.95rem;
        }
        .table-dark {
            background-color: #333;
        }
        thead th {
            border-bottom: 2px solid #555;
        }
        tbody tr:nth-child(odd) {
            background-color: #2b2b2b;
        }
        tbody tr:nth-child(even) {
            background-color: #262626;
        }
        tbody td {
            vertical-align: middle;
            color: #ddd;
        }
        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0,0,0,0.7);
        }
        @media (max-width: 576px) {
            body {
                padding: 1.5rem 1rem;
                font-size: 1rem;
            }
            .btn-primary, .btn-danger, .btn-warning {
                font-size: 1.1rem;
                padding: 0.5rem 1.2rem;
                background-color: red;
            }
            table {
                font-size: 1rem;
            }
            .container {
                max-width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h2>Dashboard Superadmin</h2>
            <a href="deconnexion.php" class="btn btn-danger mt-2 mt-sm-0">Déconnexion</a>
        </div>

        <a href="ajouter_professeur.php" class="btn btn-primary mb-4">Ajouter un professeur</a>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th style="width: 170px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($professeurs) === 0): ?>
                        <tr>
                            <td colspan="3" class="text-center">Aucun professeur enregistré.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($professeurs as $prof): ?>
                            <tr>
                                <td><?= htmlspecialchars($prof['nom_complet']) ?></td>
                                <td><?= htmlspecialchars($prof['email']) ?></td>
                                <td>
                                    <a href="modifier_professeur.php?id=<?= $prof['id'] ?>" class="btn btn-sm btn-warning me-1 mb-1 mb-sm-0">Modifier</a>
                                    <a href="supprimer_professeur.php?id=<?= $prof['id'] ?>" onclick="return confirm('Confirmer la suppression ?');" class="btn btn-sm btn-danger">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
