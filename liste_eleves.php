<?php
session_start();
require 'db.php';

// Sécurité Staff
if (!isset($_SESSION['staff_id'])) { header("Location: login_staff.php"); exit(); }

// Récupération de tous les élèves inscrits par les parents
$stmt = $pdo->query("SELECT * FROM eleves ORDER BY classe ASC, nom_enfant ASC");
$eleves = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Répertoire des Élèves | ELOHIM STAFF</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="home-body">

    <div class="glass-card" style="max-width: 1000px;">
        <div class="brand">
            <i class="fa-solid fa-address-book logo-icon" style="font-size: 2rem;"></i>
            <h2>Élèves <span>Inscrits</span></h2>
            <p>Liste des élèves enregistrés par les parents</p>
        </div>

        <div style="overflow-x: auto; margin-top: 20px;">
            <table style="width: 100%; border-collapse: collapse; color: white; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #facc15; color: #facc15; text-transform: uppercase; font-size: 0.8rem;">
                        <th style="padding: 15px;">Élève</th>
                        <th>Classe</th>
                        <th>Statut</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($eleves) > 0): ?>
                        <?php foreach($eleves as $e): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 15px;">
                                <strong><?php echo $e['nom_enfant']; ?></strong> <?php echo $e['prenom_enfant']; ?>
                            </td>
                            <td>
                                <span style="background: rgba(250, 204, 21, 0.2); color: #facc15; padding: 4px 10px; border-radius: 8px; font-size: 0.85rem; font-weight: bold;">
                                    <?php echo $e['classe']; ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 0.8rem; color: #94a3b8;">
                                    <i class="fa-solid fa-circle-check" style="color: #4ade80;"></i> <?php echo $e['statut']; ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="saisie_notes.php?id=<?php echo $e['id_eleve']; ?>" title="Ajouter une note" style="color: #facc15; text-decoration: none; margin-right: 15px;">
                                    <i class="fa-solid fa-pen-to-square"></i> Noter
                                </a>
                                <a href="profil_eleve.php?id=<?php echo $e['id_eleve']; ?>" title="Voir profil" style="color: white; opacity: 0.6;">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="padding: 40px; text-align: center; color: #94a3b8;">
                                Aucun élève inscrit pour le moment.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="auth-footer" style="margin-top: 30px;">
            <a href="espace_staff.php" class="btn-secondary">
                <i class="fa-solid fa-chevron-left"></i> RETOUR DASHBOARD
            </a>
        </div>
    </div>

</body>
</html>