<?php
session_start();
require 'db.php';

// 1. Vérification de la connexion
if (!isset($_SESSION['parent_id'])) {
    header("Location: login.php");
    exit();
}

$parent_id = $_SESSION['parent_id'];
$parent_nom = $_SESSION['parent_nom'] ?? "Parent";

try {
    // 2. Récupérer les infos du parent
    $stmt = $pdo->prepare("SELECT email, telephone FROM parents WHERE id_parent = ?");
    $stmt->execute([$parent_id]);
    $parent_info = $stmt->fetch();

    // 3. Récupérer les enfants
    $stmtEnfants = $pdo->prepare("SELECT * FROM eleves WHERE id_parent = ?");
    $stmtEnfants->execute([$parent_id]);
    $enfants = $stmtEnfants->fetchAll();
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Parent | ELOHIM APPUI</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --gold: #facc15; --emerald: #064e3b; }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background: #022c22; background-image: radial-gradient(circle at top right, #065f46, #022c22); color: white; min-height: 100vh; padding: 20px; }
        
        .navbar { max-width: 1100px; margin: 0 auto 30px; display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.05); padding: 15px 30px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); }
        .logo { font-weight: 800; font-size: 1.4rem; color: var(--gold); text-decoration: none; }
        .logout-btn { color: #f87171; text-decoration: none; font-weight: 700; font-size: 0.9rem; }

        .main-container { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 300px 1fr; gap: 25px; }

        .profile-card { background: rgba(15, 23, 42, 0.7); padding: 30px; border-radius: 25px; border: 1px solid rgba(255, 255, 255, 0.1); text-align: center; height: fit-content; }
        .avatar { width: 80px; height: 80px; background: var(--gold); color: #022c22; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 2rem; font-weight: 800; }
        .info-item { text-align: left; background: rgba(255,255,255,0.03); padding: 12px; border-radius: 12px; margin-bottom: 10px; font-size: 0.85rem; }
        .info-item span { color: var(--gold); display: block; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; margin-bottom: 4px; }

        .welcome-banner { background: linear-gradient(135deg, #facc15, #eab308); color: #022c22; padding: 30px; border-radius: 25px; margin-bottom: 25px; position: relative; }
        
        .children-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .child-card { background: rgba(255, 255, 255, 0.05); padding: 25px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.1); position: relative; transition: 0.3s; }
        .child-card:hover { border-color: var(--gold); transform: translateY(-5px); }
        .child-class { position: absolute; top: 15px; right: 15px; background: var(--gold); color: #022c22; padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; }

        /* LE STYLE DU BOUTON */
        .action-btn { display: block; width: 100%; padding: 12px; text-align: center; background: rgba(250, 204, 21, 0.1); color: var(--gold); text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 0.85rem; margin-top: 15px; transition: 0.3s; border: 1px solid var(--gold); }
        .action-btn:hover { background: var(--gold); color: #022c22; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="#" class="logo">ELOHIM <span>APPUI</span></a>
        <a href="logout.php" class="logout-btn"><i class="fa-solid fa-power-off"></i> DÉCONNEXION</a>
    </nav>

    <div class="main-container">
        <div class="profile-card">
            <div class="avatar"><?= strtoupper(substr($parent_nom, 0, 1)) ?></div>
            <h3 style="margin-bottom: 20px;"><?= htmlspecialchars($parent_nom) ?></h3>
            <div class="info-item"><span>Email</span><?= htmlspecialchars($parent_info['email']) ?></div>
            <div class="info-item"><span>Téléphone</span><?= htmlspecialchars($parent_info['telephone'] ?? 'Non renseigné') ?></div>
        </div>

        <div class="content-area">
            <div class="welcome-banner">
                <h2>Tableau de bord</h2>
                <p>Suivez les résultats de vos enfants.</p>
            </div>

            <h3 style="color: var(--gold); margin-bottom: 20px;">MES ENFANTS</h3>

            <div class="children-grid">
                <?php if (!empty($enfants)): ?>
                    <?php foreach ($enfants as $enfant): ?>
                        <div class="child-card">
                            <span class="child-class"><?= htmlspecialchars($enfant['classe']) ?></span>
                            <h4><i class="fa-solid fa-child"></i> <?= htmlspecialchars($enfant['prenom_enfant']) ?></h4>
                            
                            <a href="admin_bulletin.php?id=<?= $enfant['id_eleve'] ?>" class="action-btn">
                                <i class="fa-solid fa-file-invoice"></i> VOIR LES NOTES
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="opacity: 0.5;">Aucun enfant trouvé.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>