<?php
session_start();
require 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM parents WHERE email = ?");
    $stmt->execute([$email]);
    $parent = $stmt->fetch();

    if ($parent && password_verify($pass, $parent['password'])) {
        $_SESSION['parent_id'] = $parent['id_parent'];
        $_SESSION['parent_nom'] = $parent['nom_parent'];
        header("Location: espace_parent.php");
        exit();
    } else {
        $error = "Identifiants parents incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Parent | ELOHIM</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <div class="glass-card">
        <div class="brand">
            <h2>Espace <span>Parent</span></h2>
            <p>Connectez-vous pour voir les notes</p>
        </div>
        <?php if($error) echo "<div class='alert error'>$error</div>"; ?>
        <form action="" method="POST" class="modern-form">
            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-submit">ACCÉDER À MON ESPACE</button>
        </form>
    </div>
</body>
</html>