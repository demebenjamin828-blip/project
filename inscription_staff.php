<?php
session_start();
require 'db.php'; 

$message = "";
$CODE_INVITATION_ECOLE = "BENJAMIN28"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);
    $code_saisi = $_POST['code_invitation'];
    $pass = $_POST['password'];
    $pass_confirm = $_POST['password_confirm'];

    if ($code_saisi !== $CODE_INVITATION_ECOLE) {
        $message = "<div class='alert error'>Code d'ecole incorrect.</div>";
    } elseif ($pass !== $pass_confirm) {
        $message = "<div class='alert error'>Les mots de passe ne correspondent pas.</div>";
    } else {
        try {
            $check = $pdo->prepare("SELECT id_staff FROM staff WHERE email = ?");
            $check->execute([$email]);

            if ($check->rowCount() > 0) {
                $message = "<div class='alert error'>Email deja utilise.</div>";
            } else {
                $nom_complet = strtoupper($nom) . " " . ucfirst($prenom);
                $hash = password_hash($pass, PASSWORD_BCRYPT);

                $sql = "INSERT INTO staff (nom, email, password, role) VALUES (:nom, :email, :pass, :role)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nom'   => $nom_complet,
                    ':email' => $email,
                    ':pass'  => $hash,
                    ':role'  => $role
                ]);
                $message = "<div class='alert success'>Compte cree ! <a href='login_staff.php'>Se connecter</a></div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert error'>Erreur SQL : " . $e->getMessage() . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Staff | ELOHIM</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">
    <div class="glass-card animated-fade-in">
        <div class="throbber-container" id="mainLoader"><div class="throbber-line"></div></div>
        <div class="brand">
            <i class="fa-solid fa-user-shield logo-icon"></i>
            <h2>Inscription <span>Administration</span></h2>
        </div>

        <?php echo $message; ?>

        <form action="" method="POST" class="modern-form">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="input-group"><label>Nom</label><input type="text" name="nom" required></div>
                <div class="input-group"><label>Prenom</label><input type="text" name="prenom" required></div>
            </div>
            <div class="input-group"><label>Email Professionnel</label><input type="email" name="email" required></div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="input-group">
                    <label>Fonction</label>
                    <select name="role" required>
                        <option value="Administrateur">Administrateur</option>
                        <option value="Professeur">Professeur</option>
                    </select>
                </div>
                <div class="input-group"><label style="color:#facc15">Code Ecole</label><input type="password" name="code_invitation" required></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="input-group"><label>Mot de passe</label><input type="password" name="password" required></div>
                <div class="input-group"><label>Confirmation</label><input type="password" name="password_confirm" required></div>
            </div>
            <button type="submit" class="btn-submit" onclick="showLoader()">CREER MON COMPTE</button>
        </form>
    </div>
    <script>function showLoader(){ if(document.querySelector('form').checkValidity()) document.getElementById('mainLoader').classList.add('show-loader'); }</script>
</body>
</html>