<?php
session_start();
require 'db.php'; // Port 3308

// Vérification staff/admin
if(!isset($_SESSION['staff_id'])){
    header("Location: login_staff.php");
    exit();
}

// Récupération parents
$stmt = $pdo->query("SELECT id_parent, nom_complet, email FROM parents ORDER BY nom_complet ASC");
$parents = $stmt->fetchAll();
$total_parents = count($parents);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Console Admin | Appui2026</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; margin:0; }
.glass-nav { display:flex; justify-content:space-between; align-items:center; padding:15px 30px; background:#007bff; color:#fff; flex-wrap:wrap; }
.glass-nav .logo { font-weight:bold; font-size:1.3em; }
.glass-nav .links a { color:#fff; text-decoration:none; margin-left:15px; }
.badge { background:#ffc107; color:#000; padding:3px 8px; border-radius:5px; font-weight:bold; }
.main-container { max-width:1000px; margin:50px auto; padding:0 20px; }
.glass-card { background:#fff; padding:25px; border-radius:15px; box-shadow:0 8px 25px rgba(0,0,0,0.1); margin-bottom:30px; animation:fadeIn 1s ease-in-out; }
.admin-header { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; }
.stats-pill { background:#e0f0ff; color:#007bff; padding:8px 15px; border-radius:20px; font-weight:bold; margin-top:10px; }
.search-bar-mock { margin:20px 0; position:relative; }
.search-bar-mock i { position:absolute; top:50%; left:10px; transform:translateY(-50%); color:#aaa; }
.search-bar-mock input { width:100%; padding:10px 10px 10px 35px; border-radius:10px; border:1px solid #ddd; }
.parent-list { display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:20px; }
.parent-item { background:#fefefe; padding:15px; border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.05); display:flex; flex-direction:column; justify-content:space-between; }
.parent-avatar { width:50px; height:50px; border-radius:50%; background:#007bff; color:#fff; display:flex; justify-content:center; align-items:center; font-weight:bold; font-size:1.2em; margin-bottom:10px; }
.parent-info h3 { margin:0; font-size:1em; }
.parent-actions a { text-decoration:none; color:#fff; background:#007bff; padding:6px 12px; border-radius:10px; font-size:0.9em; display:inline-flex; align-items:center; gap:5px; margin-top:10px; }
.empty-state { text-align:center; padding:50px 0; color:#555; grid-column:1/-1; }
.empty-state i { font-size:3em; color:#007bff; margin-bottom:15px; display:block; }
@keyframes fadeIn { from{opacity:0;} to{opacity:1;} }
</style>
</head>
<body>

<nav class="glass-nav">
    <div class="logo">Appui<span>Admin</span></div>
    <div class="nav-status">
        <span class="badge">Mode Administration</span>
    </div>
    <div class="links">
        <a href="ajouter_note.php"><i class="fa-solid fa-pen"></i> Saisir Note</a>
        <a href="ajouter_cours.php"><i class="fa-solid fa-book"></i> Nouveau Cours</a>
        <a href="logout.php" class="logout-icon"><i class="fa-solid fa-power-off"></i></a>
    </div>
</nav>

<div class="main-container">
    <div class="glass-card wide-card">
        <div class="admin-header">
            <h2><i class="fa-solid fa-users-gear"></i> Gestion des Familles</h2>
            <div class="stats-pill"><strong><?= $total_parents ?></strong> parents inscrits</div>
        </div>

        <div class="search-bar-mock">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Rechercher une famille..." id="parentSearch">
        </div>

        <div class="parent-list" id="parentList">
            <?php if($total_parents > 0): ?>
                <?php foreach($parents as $p): ?>
                    <div class="parent-item">
                        <div class="parent-avatar"><?= strtoupper(substr($p['nom_complet'],0,1)); ?></div>
                        <div class="parent-info">
                            <h3><?= htmlspecialchars(strtoupper($p['nom_complet'])); ?></h3>
                            <p><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($p['email']); ?></p>
                        </div>
                        <div class="parent-actions">
                            <a href="voir_enfants.php?id_parent=<?= $p['id_parent']; ?>" class="btn-open">
                                Dossier <i class="fa-solid fa-folder-tree"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-user-slash"></i>
                    <p>Aucun parent n'est encore enregistré.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Recherche dynamique des parents
const searchInput = document.getElementById('parentSearch');
const parentList = document.getElementById('parentList');
searchInput.addEventListener('input', function(){
    const filter = this.value.toLowerCase();
    const items = parentList.getElementsByClassName('parent-item');
    Array.from(items).forEach(item => {
        const name = item.querySelector('h3').textContent.toLowerCase();
        const email = item.querySelector('p').textContent.toLowerCase();
        if(name.includes(filter) || email.includes(filter)){
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>

</body>
</html>
