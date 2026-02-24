<?php
session_start();
require 'db.php'; // Port 3308

// Sécurité staff/admin
if(!isset($_SESSION['staff_id'])){
    header("Location: login_staff.php");
    exit();
}

// Requête : moyenne et nombre de notes par élève
$sql = "SELECT e.id_eleve, e.nom_enfant, e.prenom_enfant, 
               AVG(ev.note) as moyenne, 
               COUNT(ev.id_eval) as nb_notes
        FROM eleves e
        LEFT JOIN evaluations ev ON e.id_eleve = ev.id_eleve
        GROUP BY e.id_eleve
        ORDER BY moyenne DESC";

$stmt = $pdo->query($sql);
$eleves = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Suivi des Performances | Appui2026</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; margin:0; }
.glass-nav { display:flex; justify-content:space-between; align-items:center; padding:15px 30px; background:#007bff; color:#fff; flex-wrap:wrap; }
.glass-nav .logo { font-weight:bold; font-size:1.3em; }
.glass-nav .links a { color:#fff; text-decoration:none; margin-left:15px; }
.main-container { max-width:1000px; margin:50px auto; padding:0 20px; }
.glass-card { background:#fff; padding:25px; border-radius:15px; box-shadow:0 8px 25px rgba(0,0,0,0.1); margin-bottom:30px; animation:fadeIn 1s ease-in-out; }
table.modern-table { width:100%; border-collapse:collapse; margin-top:20px; }
table.modern-table th, table.modern-table td { padding:12px; text-align:left; border-bottom:1px solid #eee; }
table.modern-table th { background:#f0f0f0; }
.student-profile { display:flex; align-items:center; gap:10px; }
.count-badge { background:#e0e0e0; padding:3px 8px; border-radius:10px; font-size:0.85em; }
.moyenne-pill { padding:4px 8px; border-radius:8px; font-weight:bold; color:#fff; font-size:0.85em; }
.moyenne-pill.excellent { background:#28a745; }
.moyenne-pill.good { background:#007bff; }
.moyenne-pill.warning { background:#dc3545; }
.status-txt { font-weight:bold; font-size:0.85em; }
.status-txt.green { color:#28a745; }
.status-txt.red { color:#dc3545; }
.btn-add-note { text-decoration:none; background:#007bff; color:#fff; padding:5px 12px; border-radius:8px; font-size:0.85em; display:inline-flex; align-items:center; gap:5px; }
.admin-footer { margin-top:20px; }
.admin-footer a { text-decoration:none; color:#007bff; font-weight:bold; }
@keyframes fadeIn { from{opacity:0;} to{opacity:1;} }
</style>
</head>
<body class="admin-body">

<nav class="glass-nav">
    <div class="logo">Appui<span>Stats</span></div>
    <div class="links">
        <a href="gestion_globale.php"><i class="fa-solid fa-users"></i> Familles</a>
        <a href="ajouter_note.php"><i class="fa-solid fa-pen"></i> Saisir Note</a>
        <a href="logout.php" class="logout-icon"><i class="fa-solid fa-power-off"></i></a>
    </div>
</nav>

<div class="main-container">
    <div class="glass-card wide-card animated-fade-in">
        <div class="header-section">
            <h2><i class="fa-solid fa-chart-line"></i> Suivi Global des Élèves</h2>
            <p>Vue d'ensemble des moyennes générales et activités.</p>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Élève</th>
                        <th>Notes saisies</th>
                        <th>Moyenne Générale</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eleves as $eleve): 
                        $moyenne = $eleve['moyenne'];
                        $colorClass = 'neutral';
                        if ($moyenne !== null) {
                            if ($moyenne >= 14) $colorClass = 'excellent';
                            elseif ($moyenne >= 10) $colorClass = 'good';
                            else $colorClass = 'warning';
                        }
                    ?>
                    <tr>
                        <td>
                            <div class="student-profile">
                                <i class="fa-solid fa-circle-user"></i>
                                <strong><?= htmlspecialchars(strtoupper($eleve['nom_enfant']) . " " . $eleve['prenom_enfant']); ?></strong>
                            </div>
                        </td>
                        <td><span class="count-badge"><?= $eleve['nb_notes']; ?> note(s)</span></td>
                        <td><span class="moyenne-pill <?= $colorClass ?>"><?= $moyenne !== null ? number_format($moyenne,2)." / 20" : "Pas de note" ?></span></td>
                        <td>
                            <?php if($moyenne === null): ?>
                                <span class="status-txt">En attente</span>
                            <?php elseif($moyenne >= 10): ?>
                                <span class="status-txt green">Admis</span>
                            <?php else: ?>
                                <span class="status-txt red">Soutien requis</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="ajouter_note.php?id_eleve=<?= $eleve['id_eleve']; ?>" class="btn-add-note">
                                <i class="fa-solid fa-plus"></i> Noter
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-footer">
            <a href="index.php"><i class="fa-solid fa-chevron-left"></i> Retour au Portail</a>
        </div>
    </div>
</div>

</body>
</html>
