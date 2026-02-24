<?php
session_start();
require 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Infos Parent
    $nom_p = strtoupper(htmlspecialchars($_POST['nom_parent']));
    $email_p = htmlspecialchars($_POST['email_parent']);
    $pass_p = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    // Infos Enfant
    $nom_e = strtoupper(htmlspecialchars($_POST['nom_enfant']));
    $prenom_e = ucfirst(htmlspecialchars($_POST['prenom_enfant']));
    $classe_e = $_POST['classe'];

    try {
        $pdo->beginTransaction(); // Sécurité : tout ou rien

        // 1. Création du compte parent
        $stmtP = $pdo->prepare("INSERT INTO parents (nom_parent, email, password) VALUES (?, ?, ?)");
        $stmtP->execute([$nom_p, $email_p, $pass_p]);
        
        $last_parent_id = $pdo->lastInsertId(); // On récupère l'ID du parent tout juste créé

        // 2. Création de l'élève rattaché
        $stmtE = $pdo->prepare("INSERT INTO eleves (nom_enfant, prenom_enfant, classe, parent_id, statut) VALUES (?, ?, ?, ?, 'Inscrit')");
        $stmtE->execute([$nom_e, $prenom_e, $classe_e, $last_parent_id]);

        $pdo->commit(); // On valide tout
        $message = "<div class='alert success'>Inscription réussie ! Votre enfant est enregistré. <a href='login_parent.php'>Se connecter</a></div>";
        
    } catch (Exception $e) {
        $pdo->rollBack(); // En cas d'erreur, on annule tout pour éviter les comptes vides
        $message = "<div class='alert error'>Erreur lors de l'inscription. L'email est peut-être déjà utilisé.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Parent | ELOHIM</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">
    <div class="glass-card animated-fade-in" style="max-width: 500px;">
        <div class="brand">
            <i class="fa-solid fa-family logo-icon" style="color:#facc15; font-size: 2rem;"></i>
            <h2>Espace <span>Parent</span></h2>
            <p>Créez votre accès et inscrivez votre enfant</p>
        </div>

        <?php echo $message; ?>

        <form action="" method="POST" class="modern-form">
            <h3 style="color:#facc15; font-size:0.9rem; text-transform:uppercase; margin-bottom:15px;">1. Vos informations (Parent)</h3>
            <div class="input-group">
                <label>Nom complet</label>
                <input type="text" name="nom_parent" placeholder="Ex: M. Traoré Benjamin" required>
            </div>
            <div class="input-group">
                <label>Email (Identifiant)</label>
                <input type="email" name="email_parent" required>
            </div>
            <div class="input-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>

            <h3 style="color:#facc15; font-size:0.9rem; text-transform:uppercase; margin:20px 0 15px;">2. Votre Enfant</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="input-group">
                    <label>Nom</label>
                    <input type="text" name="nom_enfant" placeholder="Nom de l'enfant" required>
                </div>
                <div class="input-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom_enfant" placeholder="Prénom" required>
                </div>
            </div>
            <div class="input-group">
                <label>Classe</label>
                <select name="classe" required>
                    <option value="6ème">6ème</option>
                    <option value="5ème">5ème</option>
                    <option value="4ème">4ème</option>
                    <option value="3ème">3ème</option>
                    <option value="2nde">2nde</option>
                    <option value="1ère">1ère</option>
                    <option value="Terminale">Terminale</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">VALIDER L'INSCRIPTION</button>
        </form>
    </div>
</body>
</html>