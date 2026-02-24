<?php
session_start(); // Important pour la cohérence avec le header
require 'db.php';

// On récupère les cours (assure-toi que la table 'cours' existe avec ces colonnes)
$cours = $pdo->query("SELECT * FROM cours ORDER BY id DESC")->fetchAll();
?>

<?php include 'head.php'; ?>
<?php include 'header.php'; ?>

<main class="main-container">
    <div class="glass-card wide-card animated-fade-in">
        <div class="header-section">
            <h2><i class="fa-solid fa-book-bookmark"></i> Bibliothèque de Cours</h2>
            <p>Accédez à tous les supports de cours et exercices partagés par vos professeurs.</p>
        </div>

        <?php if(count($cours) > 0): ?>
            <div class="courses-grid">
                <?php foreach($cours as $c): ?>
                    <div class="course-card">
                        <div class="course-icon">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <div class="course-info">
                            <h3><?= htmlspecialchars($c['titre']); ?></h3>
                            <p><?= htmlspecialchars($c['description']); ?></p>
                            
                            <?php if(!empty($c['fichier'])): ?>
                                <a href="<?= htmlspecialchars($c['fichier']); ?>" class="btn-download-course" target="_blank">
                                    <i class="fa-solid fa-circle-down"></i> Télécharger le support
                                </a>
                            <?php else: ?>
                                <span class="no-file-tag">Support en ligne uniquement</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-ghost"></i>
                <p>Aucun cours n'est disponible pour le moment. Revenez bientôt !</p>
            </div>
        <?php endif; ?>

        <div class="admin-footer">
            <a href="espace.php"><i class="fa-solid fa-arrow-left"></i> Retour à mon espace</a>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
