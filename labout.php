<?php 
session_start();
include 'head.php'; // Balises <head>
include 'header.php'; 
?>

<main class="main-container">
    <div class="glass-card wide-card animated-fade-in">
        <section class="about-hero">
            <div class="icon-circle gold-bg">
                <i class="fa-solid fa-award"></i>
            </div>
            <h2>À propos de Cours à domicile ELOHIM</h2>
            <p class="subtitle">L'excellence pédagogique au service de la réussite.</p>
        </section>

        <div class="about-content">
            <p>
                Depuis notre création, <strong>ELOHIM</strong> s'engage à offrir un accompagnement 
                scolaire de haute qualité. Notre plateforme numérique est le pont entre 
                les enseignants, les élèves et les parents pour un suivi en temps réel.
            </p>

            <div class="features-grid">
                <div class="feature-item">
                    <i class="fa-solid fa-book-open-reader"></i>
                    <h4>Cours & Ressources</h4>
                    <p>Accès illimité aux supports de cours et exercices en format numérique.</p>
                </div>

                <div class="feature-item">
                    <i class="fa-solid fa-chart-line"></i>
                    <h4>Suivi de Progression</h4>
                    <p>Visualisation immédiate des notes et des moyennes générales par les parents.</p>
                </div>

                <div class="feature-item">
                    <i class="fa-solid fa-file-pdf"></i>
                    <h4>Bulletins Numériques</h4>
                    <p>Génération et téléchargement sécurisé des rapports d'évaluation mensuels.</p>
                </div>
            </div>
        </div>

        <div class="about-footer-action">
            <p>Prêt à booster les résultats scolaires ?</p>
            <a href="index.php" class="btn-submit">Retour au portail</a>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
