<?php
// 1. Démarrage de la session en toute première ligne
session_start();

// 2. Connexion à la base de données
require 'db.php';

$erreur = "";

if (isset($_POST['submit'])) {
    // Nettoyage des données saisies
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse email invalide.";
    } else {
        try {
            // Recherche du parent dans la base
            $stmt = $pdo->prepare("SELECT * FROM parents WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Vérification du mot de passe haché
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                
                // On enregistre les infos en session
                $_SESSION['parent_id'] = $user['id_parent'];
                $_SESSION['parent_nom'] = $user['nom_complet'];
                $_SESSION['user_role'] = 'parent';

                // Redirection vers l'espace parent
                header("Location: espace_parent.php");
                exit();
            } else {
                $erreur = "Identifiants incorrects. Veuillez réessayer.";
            }
        } catch (Exception $e) {
            $erreur = "Erreur de connexion : " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Parent | ELOHIM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #064e3b;
            --accent-gold: #facc15;
            --glass-bg: rgba(15, 23, 42, 0.7);
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background: radial-gradient(circle at top right, #065f46, #022c22);
            /* Correction : Padding pour permettre de scroller en bas */
            padding: 40px 20px; 
            overflow-y: auto; 
        }

        .glass-card { 
            background: var(--glass-bg); 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 40px; 
            border-radius: 28px; 
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); 
            width: 100%; 
            max-width: 420px; 
            margin: auto; /* Permet le défilement fluide */
            animation: slideUp 0.6s ease-out; 
        }

        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .auth-header { text-align: center; margin-bottom: 30px; }
        
        .icon-circle { 
            background: rgba(250, 204, 21, 0.1); 
            color: var(--accent-gold); 
            width: 60px; height: 60px; 
            display: flex; justify-content: center; align-items: center; 
            border-radius: 18px; margin: 0 auto 15px;
            font-size: 1.5rem;
        }

        .auth-header h2 { color: #fff; font-size: 1.7rem; font-weight: 800; }
        .auth-header p { color: #94a3b8; font-size: 0.9rem; }

        .input-group { position: relative; margin-bottom: 18px; }
        
        .input-group input { 
            width: 100%; 
            padding: 14px 15px 14px 45px; 
            border-radius: 12px; 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 0.95rem;
            transition: 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--accent-gold);
            background: rgba(255, 255, 255, 0.08);
        }

        .input-icon { 
            position: absolute; left: 15px; top: 50%; 
            transform: translateY(-50%); 
            color: var(--accent-gold); 
            opacity: 0.7;
        }

        .btn-submit { 
            width: 100%; 
            padding: 15px; 
            background: linear-gradient(135deg, #facc15, #eab308); 
            color: #022c22; 
            border: none; 
            border-radius: 12px; 
            cursor: pointer; 
            font-weight: 800; 
            font-size: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }

        .btn-submit:hover { transform: translateY(-2px); }

        .alert.error { 
            background: rgba(239, 68, 68, 0.15); 
            color: #f87171; 
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 10px; border-radius: 10px; 
            margin-bottom: 20px; text-align: center;
            font-size: 0.85rem;
        }

        .auth-footer { text-align: center; margin-top: 25px; color: #94a3b8; font-size: 0.9rem; }
        .auth-footer a { color: var(--accent-gold); text-decoration: none; font-weight: 700; }
        
        .divider { height: 1px; background: rgba(255,255,255,0.1); margin: 20px 0; }
        .back-link { color: white; text-decoration: none; opacity: 0.6; font-size: 0.85rem; }
        .back-link:hover { opacity: 1; color: var(--accent-gold); }
    </style>
</head>
<body>

<div class="glass-card">
    <div class="auth-header">
        <div class="icon-circle">
            <i class="fa-solid fa-fingerprint"></i>
        </div>
        <h2>Espace Parent</h2>
        <p>Identifiez-vous pour continuer</p>
    </div>

    <?php if($erreur): ?>
        <div class="alert error">
            <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($erreur) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <i class="fa-solid fa-envelope input-icon"></i>
            <input type="email" name="email" required placeholder="votre@email.com">
        </div>

        <div class="input-group">
            <i class="fa-solid fa-key input-icon"></i>
            <input type="password" name="password" required placeholder="Mot de passe">
        </div>

        <button type="submit" name="submit" class="btn-submit">
            <span>SE CONNECTER</span> <i class="fa-solid fa-arrow-right-to-bracket"></i>
        </button>
    </form>

    <div class="auth-footer">
        <p>Nouveau ici ? <a href="inscription.php">Créer un compte</a></p>
        <div class="divider"></div>
        <a href="index.php" class="back-link"><i class="fa-solid fa-house"></i> Retour à l'accueil</a>
    </div>
</div>

</body>
</html>