<?php

session_start(); // Démarre la session PHP

// Vérifie si le parent est connecté
if (!isset($_SESSION['user_id'])) { // 'user_id' doit être le même nom que dans connexion.php
    header("Location: connexion.php"); // Si pas connecté, renvoie à la page de connexion
    exit(); // Arrête le script
}
?>

<?php
session_start();
require 'db.php';

// Sécurité : on vérifie que le parent est connecté
if (!isset($_SESSION['parent_id'])) {
    header("Location: login.php");
    exit();
}

$id_eleve = $_GET['id'] ?? 0;

// On récupère les infos de l'enfant pour afficher son nom
$stmt = $pdo->prepare("SELECT prenom_enfant, nom_enfant, classe FROM eleves WHERE id_eleve = ? AND id_parent = ?");
$stmt->execute([$id_eleve, $_SESSION['parent_id']]);
$enfant = $stmt->fetch();

if (!$enfant) {
    die("Accès non autorisé ou enfant introuvable.");
}

// On récupère les notes de l'enfant
$stmtNotes = $pdo->prepare("SELECT * FROM evaluations WHERE id_eleve = ? ORDER BY date_eval DESC");
$stmtNotes->execute([$id_eleve]);
$notes = $stmtNotes->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Évaluations de <?= $enfant['prenom_enfant'] ?> | ELOHIM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --gold: #facc15; --emerald: #042f2e; }
        body { background: var(--emerald); color: white; font-family: 'Plus Jakarta Sans', sans-serif; padding: 30px; }
        
        .container { max-width: 900px; margin: 0 auto; }
        .back-link { color: var(--gold); text-decoration: none; font-size: 0.9rem; font-weight: 700; display: inline-block; margin-bottom: 20px; }
        
        .header-card { 
            background: rgba(255,255,255,0.05); padding: 30px; border-radius: 20px; 
            border: 1px solid rgba(255,255,255,0.1); margin-bottom: 30px; display: flex; align-items: center; gap: 20px;
        }
        .avatar-circle { width: 60px; height: 60px; background: var(--gold); color: #042f2e; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; }
        
        h2 { margin: 0; font-size: 1.4rem; }
        .classe-badge { background: rgba(250, 204, 21, 0.1); color: var(--gold); padding: 4px 12px; border-radius: 8px; font-size: 0.8rem; }

        table { width: 100%; border-collapse: collapse; background: rgba(0,0,0,0.2); border-radius: 15px; overflow: hidden; }
        th { background: rgba(255,255,255,0.05); color: var(--gold); text-align: left; padding: 15px; font-size: 0.8rem; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.95rem; }
        
        .note-circle { 
            display: inline-block; width: 40px; height: 40px; line-height: 40px; 
            text-align: center; border-radius: 10px; font-weight: 800;
            background: rgba(250, 204, 21, 0.1); color: var(--gold);
        }
        .empty { text-align: center; padding: 50px; opacity: 0.5; }
    </style>
</head>
<body>

<div class="container">
    <a href="espace_parent.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Retour au tableau de bord</a>

    <div class="header-card">
        <div class="avatar-circle"><?= substr($enfant['prenom_enfant'], 0, 1) ?></div>
        <div>
            <h2><?= $enfant['prenom_enfant'] ?> <?= $enfant['nom_enfant'] ?></h2>
            <span class="classe-badge">Classe : <?= $enfant['classe'] ?></span>
        </div>
    </div>

    <h3>Détails des Évaluations</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Matière</th>
                <th>Type</th>
                <th>Note / 20</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($notes) > 0): ?>
                <?php foreach($notes as $n): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($n['date_eval'])) ?></td>
                    <td style="font-weight: 700;"><?= $n['matiere'] ?></td>
                    <td><?= $n['type_eval'] ?></td>
                    <td><span class="note-circle"><?= $n['note'] ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="empty">Aucune note enregistrée pour le moment.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>