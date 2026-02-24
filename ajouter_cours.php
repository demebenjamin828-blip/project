<?php
session_start();
require 'db.php';

// Vérifier si l'utilisateur est connecté (ici professeur ou admin)
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['professeur', 'admin'])) {
    header('Location: login.php');
    exit();
}

$message = "";
$status = "";

if(isset($_POST['submit'])){
    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);

    $chemin = null;

    if(isset($_FILES['fichier']) && $_FILES['fichier']['error'] == 0){
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if(!in_array($_FILES['fichier']['type'], $allowedTypes)){
            $message = "❌ Format de fichier non autorisé. Seuls PDF et Word sont acceptés.";
            $status = "error";
        } else {
            // Création sécurisée du dossier uploads
            if (!file_exists('uploads')) { mkdir('uploads', 0777, true); }
            $nomFichier = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $_FILES['fichier']['name']);
            $chemin = "uploads/" . $nomFichier;
            move_uploaded_file($_FILES['fichier']['tmp_name'], $chemin);
        }
    }

    if($chemin !== null || $message === ""){
        try {
            $stmt = $pdo->prepare("INSERT INTO cours (titre, description, fichier) VALUES (?, ?, ?)");
            if($stmt->execute([$titre, $description, $chemin])){
                $message = "✨ Le cours a été publié avec succès !";
                $status = "success";
            }
        } catch (Exception $e) {
            $message = "❌ Erreur : " . $e->getMessage();
            $status = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Cours | Appui2026</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; }
        .main-container { display: flex; justify-content: center; margin: 50px 0; }
        .glass-card { background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); width: 100%; max-width: 700px; animation: fadeIn 1s ease-in-out; }
        .subtitle { color: #555; margin-bottom: 20px; }
        .input-group { margin-top: 15px; }
        input[type="text"], textarea, input[type="file"] { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
        .btn-submit { margin-top: 20px; padding: 10px 20px; border: none; border-radius: 5px; background: #007bff; color: #fff; cursor: pointer; }
        .btn-submit:hover { background: #0056b3; }
        .alert { margin-top: 15px; padding: 10px; border-radius: 5px; }
        .alert.success { background: #d4edda; color: #155724; }
        .alert.error { background: #f8d7da; color: #721c24; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .footer-link { margin-top: 20px; }
    </style>
</head>
<body>

<nav class="glass-nav">
    <div class="logo">Appui<span>2026</span></div>
    <div class="links">
        <a href="index.php">Accueil</a>
        <a href="liste_cours.php">Cours</a>
        <a href="ajouter_cours.php" class="active">Ajouter</a>
        <a href="login.php" class="btn-login-nav">Connexion</a>
    </div>
</nav>

<div class="main-container">
    <div class="glass-card">
        <h2>📚 Nouveau Contenu</h2>
        <p class="subtitle">Partagez vos connaissances avec vos élèves</p>

        <?php if($message): ?>
            <div class="alert <?= $status ?>"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" action=
