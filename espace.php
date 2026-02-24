<?php 
session_start();
require 'db.php'; // Port 3308

// Vérification de connexion
if(!isset($_SESSION['parent_id']) && !isset($_SESSION['user_id'])){
    header("Location: connexion.php");
    exit();
}

// Détermination de l'utilisateur
$user_id = $_SESSION['parent_id'] ?? $_SESSION['user_id'];
$nom_utilisateur = $_SESSION['nom_complet'] ?? $_SESSION['nom'] ?? 'Utilisateur';
$type = $_SESSION['type'] ?? (isset($_SESSION['parent_id']) ? 'parent' : 'eleve');
?>

<?php include 'header.php'; ?>

<div class="main-container">
    <div class="glass-card wide-card animated-fade-in">
        <div class="welcome-header">
            <h2>✨ Bonjour, <?= htmlspecialchars($nom_utilisateur) ?></h2>
            <p class="role-badge"><?= ucfirst($type) ?> | Session sécurisée</p>
        </div>

        <div class="content-section">

            <?php if($type == 'eleve'): ?>
                <h3 class="section-title"><i class="fa-solid fa-book-open"></i> Vos ressources pédagogiques</h3>
                <div class="grid-container">
                    <?php 
                    $cours = $pdo->query("SELECT * FROM cours ORDER BY id DESC")->fetchAll();
                    foreach($cours as $c): ?>
                        <div class="item-card">
                            <h4><?= htmlspecialchars($c['titre']) ?></h4>
                            <p class="desc"><?= htmlspecialchars($c['description']) ?></p>
                            <?php if($c['fichier']): ?>
                                <a href="<?= $c['fichier'] ?>" target="_blank" class="btn-action">
                                    <i class="fa-solid fa-download"></i> Voir le support
