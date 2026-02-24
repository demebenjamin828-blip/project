<?php
session_start();
require 'db.php'; 
$message = "";

if(isset($_POST['submit'])){
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // 1. Tentative de connexion PARENT
    $stmt = $pdo->prepare("SELECT id_parent, nom_complet, mot_de_passe FROM parents WHERE email = ?");
    $stmt->execute([$email]);
    $parent = $stmt->fetch();

    if($parent && password_verify($password, $parent['mot_de_passe'])){
        session_regenerate_id();
        $_SESSION['parent_id'] = $parent['id_parent'];
        $_SESSION['parent_nom'] = $parent['nom_complet'];
        $_SESSION['type'] = 'parent';
        header("Location: espace_parent.php");
        exit;
    }

    // 2. Tentative de connexion ÉLÈVE
    $stmt = $pdo->prepare("SELECT id_eleve, nom_enfant, prenom_enfant, mot_de_passe FROM eleves WHERE email_eleve = ?");
    $stmt->execute([$email]);
    $eleve = $stmt->fetch();

    if($eleve && isset($eleve['mot_de_passe']) && password_verify($password, $eleve['mot_de_passe'])){
        session_regenerate_id();
        $_SESSION['eleve_id'] = $eleve['id_eleve'];
        $_SESSION['eleve_nom'] = $eleve['prenom_enfant'];
        $_SESSION['type'] = 'eleve';
        header("Location: espace_eleve.php");
        exit;
    }

    $message = "Identifiants invalides. Veuillez réessayer.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | ELOHIM Appui</title>
    
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#022c22">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="icon-192.png">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --gold: #facc15; --emerald: #042f2e; --glass: rgba(255, 255, 255, 0.05); }
        
        body { 
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: radial-gradient(circle at top right, #065f46, #022c22);
            font-family: 'Plus Jakarta Sans', sans-serif; color: white;
        }

        .login-card {
            width: 100%; max-width: 400px; padding: 40px; border-radius: 28px;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 25px 50px rgba(0,0,0,0.4);
            text-align: center; animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .icon-box {
            width: 70px; height: 70px; background: rgba(250, 204, 21, 0.1);
            color: var(--gold); border-radius: 50%; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 20px; font-size: 1.8rem;
            border: 1px solid rgba(250, 204, 21, 0.2);
        }

        h2 { font-weight: 800; font-size: 1.8rem; margin: 0 0 10px; letter-spacing: -1px; }
        p.subtitle { color: #94a3b8; font-size: 0.9rem; margin-bottom: 30px; }

        .input-group { position: relative; margin-bottom: 20px; text-align: left; }
        .input-group i { position: absolute; left: 15px; top: 45px; color: var(--gold); opacity: 0.8; }
        label { display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--gold); margin-bottom: 8px; margin-left: 5px; }

        input {
            width: 100%; padding: 15px 15px 15px 45px; background: var(--glass);
            border: 1px solid rgba(255,255,255,0.1); border-radius: 12px;
            color: white; font-size: 1rem; box-sizing: border-box; transition: 0.3s;
        }

        input:focus { border-color: var(--gold); background: rgba(255,255,255,0.1); outline: none; }

        .btn-submit {
            width: 100%; padding: 16px; background: var(--gold); color: #042f2e;
            border: none; border-radius: 12px; font-weight: 800; font-size: 1rem;
            cursor: pointer; transition: 0.3s; margin-top: 10px; display: flex;
            align-items: center; justify-content: center; gap: 10px;
        }

        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(250, 204, 21, 0.3); }

        .alert { background: rgba(239, 68, 68, 0.15); color: #f87171; padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 0.85rem; border: 1px solid rgba(239, 68, 68, 0.3); }

        .auth-footer { margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px; }
        .auth-footer p { font-size: 0.85rem; color: #64748b; }
        .gold-link { color: var(--gold); text-decoration: none; font-weight: 700; }
        .staff-access-link { display: inline-block; margin-top: 15px; color: #94a3b8; font-size: 0.8rem; text-decoration: none; transition: 0.3s; }
        .staff-access-link:hover { color: white; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="icon-box">
        <i class="fa-solid fa-user-graduate"></i>
    </div>
    <h2>ELOHIM APPUI</h2>
    <p class="subtitle">Accédez à votre espace réussite</p>

    <?php if($message): ?>
        <div class="alert">
            <i class="fa-solid fa-circle-exclamation"></i> <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Identifiant Email</label>
            <i class="fa-solid fa-envelope"></i>
            <input type="email" name="email" placeholder="votre@email.com" required>
        </div>

        <div class="input-group">
            <label>Mot de passe</label>
            <i class="fa-solid fa-key"></i>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" name="submit" class="btn-submit">
            <i class="fa-solid fa-right-to-bracket"></i> SE CONNECTER
        </button>
    </form>

    <div class="auth-footer">
        <p>Pas encore inscrit ? <a href="inscription.php" class="gold-link">Créer un compte parent</a></p>
        <a href="login_staff.php" class="staff-access-link">Accès Collaborateur Staff</a>
    </div>
</div>

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('sw.js')
                .then(reg => console.log('Service Worker enregistré !'))
                .catch(err => console.log('Erreur SW:', err));
        });
    }
</script>

</body>
</html>