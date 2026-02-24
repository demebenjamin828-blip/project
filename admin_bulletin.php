<?php
session_start();
require 'db.php';

if (!isset($_SESSION['parent_id'])) {
    header("Location: login.php");
    exit();
}

$parent_id = $_SESSION['parent_id'];
$id_eleve = isset($_GET['id']) ? $_GET['id'] : 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM eleves WHERE id_eleve = ? AND id_parent = ?");
    $stmt->execute([$id_eleve, $parent_id]);
    $enfant = $stmt->fetch();

    if (!$enfant) {
        header("Location: espace_parent.php?erreur=acces_refuse");
        exit();
    }

    // Récupération des notes incluant la colonne document_joint
    $stmtNotes = $pdo->prepare("SELECT * FROM notes WHERE id_eleve = ? ORDER BY trimestre ASC, id_note DESC");
    $stmtNotes->execute([$id_eleve]);
    $notes = $stmtNotes->fetchAll();

    $matieres_uniques = [];
    $appreciation_globale = ""; 

    foreach ($notes as $n) {
        $matieres_uniques[] = $n['matiere'];
        if (!empty($n['observation']) && $appreciation_globale == "") {
            $appreciation_globale = $n['observation'];
        }
    }
    $nombre_matieres = count(array_unique($matieres_uniques));

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin | ELOHIM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --gold: #facc15; --dark: #022c22; }
        body { background: var(--dark); color: white; font-family: 'Plus Jakarta Sans', sans-serif; padding: 20px; }
        
        .bulletin-card { 
            max-width: 850px; margin: auto; 
            background: rgba(255,255,255,0.03); 
            padding: 40px; border-radius: 30px; 
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }

        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .stats-badge { background: var(--gold); color: var(--dark); padding: 8px 15px; border-radius: 12px; font-weight: 800; font-size: 0.8rem; }
        h1 { color: var(--gold); font-size: 1.8rem; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: rgba(0,0,0,0.2); border-radius: 15px; overflow: hidden; }
        th { background: rgba(250, 204, 21, 0.1); color: var(--gold); text-align: left; padding: 15px; text-transform: uppercase; font-size: 0.75rem; }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #e2e8f0; vertical-align: middle; }

        .note-pill { background: rgba(250, 204, 21, 0.15); color: var(--gold); padding: 6px 12px; border-radius: 8px; font-weight: 800; border: 1px solid rgba(250, 204, 21, 0.3); }

        .appreciation-box {
            margin-top: 35px;
            background: linear-gradient(145deg, rgba(250, 204, 21, 0.08), rgba(0, 0, 0, 0.2));
            border-left: 4px solid var(--gold);
            padding: 30px;
            border-radius: 0 20px 20px 0;
            position: relative;
        }

        .appreciation-title {
            color: var(--gold);
            font-size: 0.9rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 15px;
            display: flex; align-items: center; gap: 10px;
        }

        .comment-text {
            line-height: 1.8; color: #f1f5f9; font-size: 1.05rem;
            white-space: pre-wrap; word-wrap: break-word; text-align: justify;
        }

        .btn-doc {
            color: var(--gold);
            text-decoration: none;
            font-size: 1.2rem;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-doc:hover { transform: scale(1.1); opacity: 0.8; }
        
        .quote-icon { position: absolute; right: 20px; bottom: 10px; font-size: 3rem; opacity: 0.05; color: white; }
    </style>
</head>
<body>

<div class="bulletin-card">
    <div class="header-flex">
        <a href="espace_parent.php" style="color:#94a3b8; text-decoration:none;"><i class="fa-solid fa-chevron-left"></i> Retour</a>
        <div class="stats-badge"><i class="fa-solid fa-book"></i> <?= $nombre_matieres ?> MATIÈRES</div>
    </div>
    
    <h1>Bulletin de <?= htmlspecialchars($enfant['prenom_enfant'] . ' ' . $enfant['nom_enfant']) ?></h1>

    <table>
        <thead>
            <tr>
                <th>Matière</th>
                <th>Période</th>
                <th>Note / 20</th>
                <th style="text-align: center;">Doc</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($notes): ?>
                <?php foreach ($notes as $n): ?>
                <tr>
                    <td style="font-weight: 600;"><?= htmlspecialchars($n['matiere']) ?></td>
                    <td><?= $n['trimestre'] ?>° Trimestre</td>
                    <td><span class="note-pill"><?= number_format($n['note_valeur'], 2) ?></span></td>
                    
                    <td style="text-align: center;">
                        <?php if (!empty($n['document_joint'])): ?>
                            <a href="uploads/<?= $n['document_joint'] ?>" target="_blank" class="btn-doc" title="Voir le document">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                        <?php else: ?>
                            <span style="opacity: 0.2;">-</span>
                        <?php endif; ?>
                    </td>

                    <td style="font-size: 0.85rem; opacity: 0.6;"><?= date('d/m/Y', strtotime($n['date_saisie'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; padding:30px; opacity:0.5;">Aucune note disponible.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (!empty($appreciation_globale)): ?>
    <div class="appreciation-box">
        <div class="appreciation-title">
            <i class="fa-solid fa-feather-pointed"></i> Bilan de l'ensemble du travail
        </div>
        <div class="comment-text">
            <?php 
                $clean_text = htmlspecialchars_decode($appreciation_globale, ENT_QUOTES);
                echo nl2br(htmlspecialchars($clean_text)); 
            ?>
        </div>
        <i class="fa-solid fa-quote-right quote-icon"></i>
    </div>
    <?php endif; ?>
</div>

</body>
</html>