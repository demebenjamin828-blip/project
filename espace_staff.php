<?php
session_start();

// Sécurité : si le staff n'est pas connecté, on le renvoie au login
if (!isset($_SESSION['staff_id'])) {
    header("Location: login_staff.php");
    exit();
}

$nom_staff = $_SESSION['staff_nom'];
$role_staff = $_SESSION['staff_role'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord | ELOHIM STAFF</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="home-body">

    <div class="glass-card" style="max-width: 900px;">
        <div class="brand">
            <i class="fa-solid fa- chalkboard-user logo-icon"></i>
            <h1>ESPACE <span>STAFF</span></h1>
            <p>Bienvenue, <strong><?php echo htmlspecialchars($nom_staff); ?></strong> (<?php echo htmlspecialchars($role_staff); ?>)</p>
        </div>

        <div class="selection-grid">
            <a href="liste_eleves.php" class="portal-card">
                <i class="fa-solid fa-user-graduate" style="font-size: 2rem; color: #facc15; margin-bottom: 15px; display: block;"></i>
                <h3>Gestion Élèves</h3>
                <p>Inscrire, modifier ou voir la liste des élèves.</p>
            </a>

            <a href="saisie_notes.php" class="portal-card">
                <i class="fa-solid fa-pen-to-square" style="font-size: 2rem; color: #facc15; margin-bottom: 15px; display: block;"></i>
                <h3>Saisie des Notes</h3>
                <p>Enregistrer les résultats des évaluations.</p>
            </a>

            <a href="bulletins.php" class="portal-card">
                <i class="fa-solid fa-file-invoice" style="font-size: 2rem; color: #facc15; margin-bottom: 15px; display: block;"></i>
                <h3>Bulletins</h3>
                <p>Générer et imprimer les rapports scolaires.</p>
            </a>
        </div>

        <div class="home-footer">
            <a href="logout.php" class="btn-secondary" style="border-color: #f87171; color: #f87171;">
                <i class="fa-solid fa-power-off"></i> DÉCONNEXION
            </a>
        </div>
    </div>

</body>
</html>