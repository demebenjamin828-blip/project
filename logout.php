<?php
session_start();

// 1. Vider toutes les variables de session
$_SESSION = array();

// 2. Détruire le cookie de session si nécessaire
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Détruire la session côté serveur
session_destroy();

// 4. Rediriger vers l'accueil avec un petit message
header("Location: index.php?logout=success");
exit();
?>
