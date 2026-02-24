<?php
session_start();
require 'db.php';

// Plus tard, on vérifiera ici que le parent est connecté
// Pour le test, on imagine que le parent regarde son enfant qui a l'ID passé en paramètre
$id_eleve = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_eleve > 0) {
    // 1. Récupérer les infos de l'élève
    $stmtE = $pdo->prepare("SELECT * FROM eleves WHERE id_eleve = ?");
    $stmtE->execute([$id_eleve]);
    $eleve = $stmtE->fetch();

    // 2. Récupérer toutes les notes de cet élève
    $stmtN = $pdo->prepare("SELECT * FROM notes WHERE id_eleve = ? ORDER BY trimestre ASC, matiere ASC");
    $stmtN->execute([$id_eleve]);
    $notes = $stmtN->fetchAll();
}

if (!$eleve) {
    die("Erreur : Élève introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin de Notes | ELOHIM</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="home-body">
    <div class="glass-card" style="max-width: 800px;">
        <div class="brand">
            <i class="fa-solid fa-file-invoice logo-icon" style="color:#facc15;"></i>
            <h2>Bulletin de <span>Notes</span></h2>
            <p>Élève : <strong><?php echo htmlspecialchars($eleve['nom_enfant'] . ' ' . $eleve['prenom_enfant']); ?></strong></p>
            <p>Classe : <?php echo htmlspecialchars($eleve['classe']); ?></p>
        </div>

        <div style="margin-top: 20px;">
            <?php if (count($notes) > 0): ?>
                <table style="width: 100%; border-collapse: collapse; color: white; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid #facc15; color: #facc15;">
                            <th style="padding: 10px;">Matière</th>
                            <th>Note / 20</th>
                            <th>Trimestre</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($notes as $n): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <td style="padding: 10px;"><?php echo htmlspecialchars($n['matiere']); ?></td>
                            <td style="font-weight: bold; color: <?php echo ($n['note_valeur'] >= 10) ? '#4ade80' : '#f87171'; ?>;">
                                <?php echo number_format($n['note_valeur'], 2); ?>
                            </td>
                            <td><?php echo $n['trimestre']; ?>e Trimestre</td>
                            <td style="font-size: 0.8rem; opacity: 0.6;">
                                <?php echo date('d/m/Y', strtotime($n['date_saisie'])); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #94a3b8;">
                    <i class="fa-solid fa- hourglass-start" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p>Aucune note n'a encore été publiée pour cet élève.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="auth-footer" style="margin-top: 30px;">
            <a href="espace_parent.php" class="btn-secondary">RETOUR MON COMPTE</a>
        </div>
    </div>
</body>
</html>