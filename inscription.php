<?php
session_start();
require 'db.php';

$message = "";
$status = "";

if (isset($_POST['register'])) {
    // Récupération Parent
    $nom = strtoupper(htmlspecialchars($_POST['nom_parent']));
    $prenom = ucfirst(htmlspecialchars($_POST['prenom_parent']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $telephone = htmlspecialchars($_POST['tel_parent']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Vérifications de sécurité
    if ($password !== $password_confirm) {
        $message = "Les mots de passe ne correspondent pas.";
        $status = "error";
    } elseif (strlen($password) < 6) {
        $message = "Le mot de passe est trop court (6 caractères min).";
        $status = "error";
    } else {
        try {
            $pdo->beginTransaction();

            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            $nom_complet = $nom . " " . $prenom;

            // 1. Insertion Parent
            $stmt = $pdo->prepare("INSERT INTO parents (nom_complet, email, telephone, mot_de_passe) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nom_complet, $email, $telephone, $password_hashed]);
            $id_parent = $pdo->lastInsertId();

            // 2. Insertion Enfants
            if (!empty($_POST['enfant_nom'])) {
                $stmtEnfant = $pdo->prepare("INSERT INTO eleves (id_parent, nom_enfant, prenom_enfant, classe) VALUES (?, ?, ?, ?)");
                foreach ($_POST['enfant_nom'] as $key => $enf_nom) {
                    if (!empty($enf_nom)) {
                        $e_nom = strtoupper(htmlspecialchars($enf_nom));
                        $e_pre = ucfirst(htmlspecialchars($_POST['enfant_prenom'][$key]));
                        $e_cla = htmlspecialchars($_POST['enfant_classe'][$key]);
                        $stmtEnfant->execute([$id_parent, $e_nom, $e_pre, $e_cla]);
                    }
                }
            }

            $pdo->commit();
            $message = "Compte famille créé ! Bienvenue chez ELOHIM.";
            $status = "success";
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Erreur : " . $e->getMessage();
            $status = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Famille | ELOHIM APPUI</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --gold: #facc15; --emerald: #064e3b; --dark: #020617; }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        
        body { 
            background: radial-gradient(circle at top right, #065f46, #022c22); 
            color: white; min-height: 100vh; padding: 40px 20px;
            display: flex; justify-content: center;
        }

        .container { 
            max-width: 650px; width: 100%; 
            background: rgba(15, 23, 42, 0.75); 
            backdrop-filter: blur(20px); padding: 40px; 
            border-radius: 30px; border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }

        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { font-weight: 800; font-size: 2.2rem; letter-spacing: -1px; }
        .header h2 span { color: var(--gold); }

        .section-label { 
            color: var(--gold); font-size: 0.8rem; font-weight: 800; 
            text-transform: uppercase; margin: 30px 0 15px; 
            display: flex; align-items: center; gap: 10px;
        }

        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        
        input, .custom-select { 
            width: 100%; padding: 14px; border-radius: 12px; 
            border: 1px solid rgba(255,255,255,0.1); 
            background: rgba(255,255,255,0.05); color: white; outline: none;
            font-size: 0.95rem; transition: 0.3s;
        }
        
        input:focus, .custom-select:focus { border-color: var(--gold); background: rgba(255,255,255,0.1); }

        /* Style spécifique pour le SELECT */
        .custom-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23facc15' viewBox='0 0 24 24'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 18px;
        }

        option { background: #1e293b; color: white; }
        optgroup { background: #0f172a; color: var(--gold); font-weight: bold; }

        .enfant-box { 
            background: rgba(0,0,0,0.2); padding: 20px; 
            border-radius: 18px; border-left: 4px solid var(--gold); margin-bottom: 15px;
            animation: fadeIn 0.4s ease-out;
        }

        .btn-add { 
            background: rgba(250, 204, 21, 0.05); border: 2px dashed rgba(250, 204, 21, 0.3); 
            color: var(--gold); width: 100%; padding: 12px; 
            border-radius: 12px; cursor: pointer; font-weight: 700; margin-bottom: 25px;
        }
        .btn-add:hover { background: rgba(250, 204, 21, 0.1); border-color: var(--gold); }

        .btn-register { 
            background: var(--gold); color: #022c22; width: 100%; 
            padding: 18px; border: none; border-radius: 12px; 
            font-weight: 800; cursor: pointer; font-size: 1.1rem;
        }

        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; text-align: center; font-weight: 600; }
        .success { background: rgba(74, 222, 128, 0.2); color: #4ade80; border: 1px solid #4ade80; }
        .error { background: rgba(248, 113, 113, 0.2); color: #f87171; border: 1px solid #f87171; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>ELOHIM <span>APPUI</span></h2>
        <p style="color: #94a3b8;">Portail d'inscription des familles</p>
    </div>

    <?php if($message): ?>
        <div class="alert <?= $status ?>"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="section-label"><i class="fa-solid fa-user-shield"></i> Responsable de la Famille</div>
        <div class="grid">
            <input type="text" name="nom_parent" placeholder="NOM DU PARENT" required>
            <input type="text" name="prenom_parent" placeholder="PRÉNOM DU PARENT" required>
        </div>
        <div class="grid">
            <input type="email" name="email" placeholder="Adresse Email" required>
            <input type="tel" name="tel_parent" placeholder="Téléphone (ex: 01020304)" required>
        </div>
        <div class="grid">
            <input type="password" name="password" placeholder="Mot de passe" required>
            <input type="password" name="password_confirm" placeholder="Confirmer mot de passe" required>
        </div>

        <div class="section-label"><i class="fa-solid fa-users-items"></i> Enfants à Inscrire</div>
        <div id="enfants-list">
            <div class="enfant-box">
                <div class="grid">
                    <input type="text" name="enfant_nom[]" placeholder="NOM DE L'ENFANT" required>
                    <input type="text" name="enfant_prenom[]" placeholder="PRÉNOM" required>
                </div>
                <select name="enfant_classe[]" class="custom-select" style="margin-top: 10px;" required>
                    <option value="" disabled selected>-- Sélectionner la classe --</option>
                    <optgroup label="CYCLE COLLÈGE">
                        <option value="6eme">6ème</option>
                        <option value="5eme">5ème</option>
                        <option value="4eme">4ème</option>
                        <option value="3eme">3ème</option>
                    </optgroup>
                    <optgroup label="CYCLE LYCÉE">
                        <option value="2nde">Seconde (2nde)</option>
                        <option value="1ere">Première (1ère)</option>
                        <option value="Tle">Terminale (Tle)</option>
                    </optgroup>
                </select>
            </div>
        </div>

        <button type="button" class="btn-add" onclick="ajouterEnfant()">
            <i class="fa-solid fa-plus-circle"></i> Ajouter un autre enfant
        </button>

        <button type="submit" name="register" class="btn-register">
            VALIDER L'INSCRIPTION FAMILLE
        </button>
        
        <p style="text-align:center; margin-top:20px; font-size:0.9rem;">
            Déjà inscrit ? <a href="login.php" style="color:var(--gold); font-weight:bold; text-decoration:none;">Se connecter</a>
        </p>
    </form>
</div>

<script>
function ajouterEnfant() {
    const container = document.getElementById('enfants-list');
    const div = document.createElement('div');
    div.className = 'enfant-box';
    div.innerHTML = `
        <div class="grid">
            <input type="text" name="enfant_nom[]" placeholder="NOM DE L'ENFANT" required>
            <input type="text" name="enfant_prenom[]" placeholder="PRÉNOM" required>
        </div>
        <select name="enfant_classe[]" class="custom-select" style="margin-top: 10px;" required>
            <option value="" disabled selected>-- Sélectionner la classe --</option>
            <optgroup label="CYCLE COLLÈGE">
                <option value="6eme">6ème</option>
                <option value="5eme">5ème</option>
                <option value="4eme">4ème</option>
                <option value="3eme">3ème</option>
            </optgroup>
            <optgroup label="CYCLE LYCÉE">
                <option value="2nde">Seconde (2nde)</option>
                <option value="1ere">Première (1ère)</option>
                <option value="Tle">Terminale (Tle)</option>
            </optgroup>
        </select>
    `;
    container.appendChild(div);
}
</script>

</body>
</html>