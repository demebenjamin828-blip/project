<?php
require 'db.php';
session_start();

// Protection : Seul le staff peut uploader des bulletins
if (!isset($_SESSION['staff_id'])) {
    die("Accès refusé."); // Empêche tout accès non autorisé
}

// Vérifie que le formulaire a été soumis et qu'un fichier est présent
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['mon_bulletin'])) {
    $id_eleve = $_POST['id_eleve'];
    $commentaire = htmlspecialchars($_POST['commentaire']);
    
    // 1. Configuration et création du dossier uploads/bulletins
    $uploadDir = 'uploads/bulletins/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Crée le dossier si inexistant
    }

    $file = $_FILES['mon_bulletin'];
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    // 2. Vérifications de sécurité
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['pdf']; // On n'autorise QUE les PDF

    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize <= 5000000) { // Limite à 5 Mo
                
                // 3. Génération d'un nom unique sécurisé
                $newFileName = "bulletin_" . $id_eleve . "_" . uniqid('', true) . "." . $fileExt;
                $destination = $uploadDir . $newFileName;

                // 4. Déplacement et enregistrement dans la base de données
                if (move_uploaded_file($fileTmp, $destination)) {
                    try {
                        $req = $pdo->prepare("
                            INSERT INTO evaluations 
                            (id_eleve, commentaire, bulletin_pdf, date_upload) 
                            VALUES (?, ?, ?, NOW())
                        ");
                        $req->execute([$id_eleve, $commentaire, $newFileName]);

                        header("Location: gestion_globale.php?upload=success");
                        exit();
                    } catch (PDOException $e) {
                        // Supprime le fichier si l'insertion SQL échoue
                        unlink($destination);
                        die("Erreur base de données : " . $e->getMessage());
                    }
                } else {
                    die("Erreur lors du déplacement du fichier.");
                }
            } else {
                die("Le fichier est trop lourd (Max 5Mo).");
            }
        } else {
            die("Erreur lors du transfert.");
        }
    } else {
        die("Seuls les fichiers PDF sont autorisés.");
    }
}
?>
