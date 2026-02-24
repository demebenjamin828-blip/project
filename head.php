<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Appui2026 | Excellence Scolaire</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="style.css?v=<?= time(); ?>">

<style>
/* Animation d'entrée de page */
body { animation: fadeInPage 0.5s ease-in-out; }
@keyframes fadeInPage { from { opacity:0; transform:translateY(-10px);} to{ opacity:1; transform:translateY(0);} }

/* Popup de bienvenue */
#welcomePopup {
    position: fixed;
    top:0; left:0; right:0; bottom:0;
    background: rgba(0,0,0,0.6);
    display:flex;
    justify-content:center;
    align-items:center;
    z-index:10000;
}
#welcomePopup .popup-content {
    background:#fff;
    padding:30px 40px;
    border-radius:15px;
    text-align:center;
    max-width:400px;
    animation:fadeInPopup 0.5s ease-in-out;
}
#welcomePopup h2 { margin-bottom:15px; color:#007bff; }
#welcomePopup button {
    background:#007bff; color:#fff; border:none; padding:10px 20px;
    border-radius:10px; cursor:pointer; font-weight:bold;
    transition:0.3s;
}
#welcomePopup button:hover { background:#0056b3; }
@keyframes fadeInPopup { from{opacity:0; transform:scale(0.9);} to{opacity:1; transform:scale(1);} }
</style>
</head>
<body>

<!-- Popup de bienvenue au premier chargement -->
<div id="welcomePopup">
    <div class="popup-content">
        <h2>Bienvenue sur Appui2026 !</h2>
        <p>Votre plateforme de suivi scolaire et de partage de cours à domicile.</p>
        <button id="continueBtn">Appuyer pour continuer</button>
    </div>
</div>

<nav class="glass-nav">
    <div class="nav-content">
        <a href="index.php" class="logo">✨ Appui<span>2026</span></a>
        <div class="nav-links">
            <?php if(isset($_SESSION['parent_id'])): ?>
                <a href="espace_parent.php"><i class="fa-solid fa-chart-pie"></i> Notes</a>
                <a href="logout.php"><i class="fa-solid fa-power-off"></i></a>
            <?php elseif(isset($_SESSION['staff_id'])): ?>
                <a href="gestion_globale.php"><i class="fa-solid fa-gears"></i> Admin</a>
                <a href="logout.php"><i class="fa-solid fa-power-off"></i></a>
            <?php else: ?>
                <a href="login_choix.php">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="nav-spacer" style="height: 80px;"></div>

<script>
// Gestion du popup de bienvenue
document.getElementById('continueBtn').addEventListener('click', function(){
    document.getElementById('welcomePopup').style.display = 'none';
});
</script>
