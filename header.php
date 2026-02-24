<header class="main-header">
    <div class="nav-container">
        <div class="logo">
            <i class="fa-solid fa-cross"></i> ELOHIM <span>Appui</span>
        </div>

        <nav class="nav-menu">
            <a href="index.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-house"></i> Accueil
            </a>

            <?php if(isset($_SESSION['parent_id'])): ?>
                <a href="espace_parent.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'espace_parent.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-user-graduate"></i> Mon Espace
                </a>
                <a href="logout.php" class="nav-link logout-link">
                    <i class="fa-solid fa-power-off"></i> Déconnexion
                </a>

            <?php elseif(isset($_SESSION['staff_id'])): ?>
                <a href="gestion_globale.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'gestion_globale.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-lock-open"></i> Administration
                </a>
                <a href="logout.php" class="nav-link logout-link">
                    <i class="fa-solid fa-power-off"></i> Déconnexion
                </a>

            <?php else: ?>
                <a href="login_choix.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'login_choix.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-right-to-bracket"></i> Connexion
                </a>
            <?php endif; ?>
        </nav>

        <?php if(isset($_SESSION['nom_complet'])): ?>
            <div class="user-info">
                <i class="fa-solid fa-user-circle"></i> 
                <span><?= htmlspecialchars($_SESSION['nom_complet']); ?></span>
            </div>
        <?php endif; ?>
    </div>
</header>

<div class="header-spacer" style="height:80px;"></div>
