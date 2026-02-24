<?php
session_start();
require 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $pass = $_POST['password'];

    try {
        // On cherche l'utilisateur par son email
        $stmt = $pdo->prepare("SELECT * FROM staff WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // On vérifie si l'utilisateur existe ET si le mot de passe est correct
        if ($user && password_verify($pass, $user['password'])) {
            // SUCCESS : On crée la session
            $_SESSION['staff_id'] = $user['id_staff'];
            $_SESSION['staff_nom'] = $user['nom'];
            $_SESSION['staff_role'] = $user['role'];
            
            header("Location: espace_staff.php");
            exit();
        } else {
            // ERREUR : Soit l'email n'existe pas, soit le mot de passe est faux
            $error = "Email ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        $error = "Erreur de connexion : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Staff | ELOHIM</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <div class="glass-card">
        <div class="throbber-container" id="mainLoader"><div class="throbber-line"></div></div>
        <div class="brand">
            <h1>Connexion <span>Staff</span></h1>
            <p>Accès sécurisé à l'administration</p>
        </div>

        <?php if($error): ?> 
            <div class="alert error"><?php echo $error; ?></div> 
        <?php endif; ?>

        <form action="" method="POST" class="modern-form">
            <div class="input-group">
                <label>Email Professionnel</label>
                <input type="email" name="email" placeholder="votre@email.com" required>
            </div>
            <div class="input-group">
                <label>Mot de passe</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-submit" onclick="showLoader()">SE CONNECTER</button>
        </form>
        
        <div class="auth-footer">
            <p>Pas encore de compte ? <a href="inscription_staff.php">S'inscrire ici</a></p>
        </div>
    </div>
    <script>
        function showLoader(){ 
            document.getElementById('mainLoader').classList.add('show-loader'); 
        }
    </script>
</body>
</html>