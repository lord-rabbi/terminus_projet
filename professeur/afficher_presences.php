<?php
session_start();
if (!isset($_SESSION['professeur_logged']) || !isset($_SESSION['professeur_id'])) {
    header("Location: connexion.php");
    exit();
}

require_once '../bdd/db.php';
$prof_id = $_SESSION['professeur_id'];
$date_filtre = $_GET['date'] ?? null;

// Requête avec filtre facultatif
$sql = "
SELECT c.id as cours_id, c.nom as cours_nom,
       e.id as etudiant_id, e.nom_complet as etudiant,
       DATE(p.date_heure) as jour,
       TIME(p.date_heure) as heure,
       p.type_pointage
FROM cours c
JOIN presences p ON c.id = p.cours_id
JOIN etudiants e ON p.etudiant_id = e.id
WHERE c.professeur_id = ?
";

$params = [$prof_id];

if ($date_filtre) {
    $sql .= " AND DATE(p.date_heure) = ?";
    $params[] = $date_filtre;
}

$sql .= " ORDER BY c.nom, e.nom_complet, p.date_heure";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$donnees = [];

foreach ($rows as $row) {
    $cours_id = $row['cours_id'];
    $cours_nom = $row['cours_nom'];
    $etudiant = $row['etudiant'];
    $jour = $row['jour'];
    $heure = $row['heure'];
    $type = $row['type_pointage'];

    if (!isset($donnees[$cours_id])) {
        $donnees[$cours_id] = [
            'nom' => $cours_nom,
            'presences' => []
        ];
    }

    $cle = $etudiant . '|' . $jour;
    if (!isset($donnees[$cours_id]['presences'][$cle])) {
        $donnees[$cours_id]['presences'][$cle] = [
            'etudiant' => $etudiant,
            'jour' => $jour,
            'debut' => null,
            'fin' => null
        ];
    }

    if ($type === 'debut') {
        $donnees[$cours_id]['presences'][$cle]['debut'] = $heure;
    } elseif ($type === 'fin') {
        $donnees[$cours_id]['presences'][$cle]['fin'] = $heure;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Présences par date</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #1f1f1f;
            color: #f0f0f0;
            padding: 2rem 1rem;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        table {
            background-color: #2a2a2a;
            margin-bottom: 3rem;
        }
        th, td {
            vertical-align: middle !important;
        }
        .type-debut {
            color: #4caf50;
        }
        .type-fin {
            color: #e91e63;
        }
        h3.cours-title {
            margin-bottom: 1rem;
            border-bottom: 1px solid #555;
            padding-bottom: 0.3rem;
        }
        .form-date {
            max-width: 300px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Filtrer les présences par date</h2>

    <form method="get" class="form-date">
        <label for="date" class="form-label">Choisir une date :</label>
        <input type="date" id="date" name="date" class="form-control mb-2" value="<?= htmlspecialchars($date_filtre) ?>">
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <a href="?" class="btn btn-outline-light ms-2">Réinitialiser</a>
    </form>

    <?php if (empty($donnees)): ?>
        <div class="alert alert-info">Aucune présence pour la date sélectionnée.</div>
    <?php else: ?>
        <?php foreach ($donnees as $cours): ?>
            <h3 class="cours-title"><?= htmlspecialchars($cours['nom']) ?></h3>

            <table class="table table-bordered table-hover text-white">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Étudiant</th>
                        <th>Date</th>
                        <th>Heure début</th>
                        <th>Heure fin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($cours['presences'] as $presence): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($presence['etudiant']) ?></td>
                            <td><?= date('d/m/Y', strtotime($presence['jour'])) ?></td>
                            <td class="type-debut"><?= $presence['debut'] ?? '—' ?></td>
                            <td class="type-fin"><?= $presence['fin'] ?? '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Accueil</a>
</div>
</body>
</html>
