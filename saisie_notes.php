<?php
session_start();
require 'db.php';

// 1. Sécurité : Vérifier si le staff est connecté
if (!isset($_SESSION['staff_id'])) {
    header("Location: login_staff.php");
    exit();
}

$message = "";
$id_eleve = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Récupérer les infos de l'élève
$stmt = $pdo->prepare("SELECT * FROM eleves WHERE id_eleve = ?");
$stmt->execute([$id_eleve]);
$eleve = $stmt->fetch();

if (!$eleve) {
    die("Erreur : Élève introuvable. <a href='liste_eleves.php'>Retour</a>");
}

// 3. Traitement de la sauvegarde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matiere = htmlspecialchars($_POST['matiere']);
    $note = (float)$_POST['note']; 
    $trimestre = (int)$_POST['trimestre'];
    $observation = isset($_POST['observation']) ? htmlspecialchars($_POST['observation'], ENT_QUOTES) : "";
    
    $nom_fichier = null;

    // --- GESTION DU FICHIER JOINT ---
    if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
        $extensions_valides = ['pdf', 'jpg', 'jpeg', 'png'];
        $extension_upload = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));

        if (in_array($extension_upload, $extensions_valides)) {
            // Création d'un nom unique pour éviter d'écraser d'autres fichiers
            $nom_fichier = "doc_" . time() . "_" . rand(1000, 9999) . "." . $extension_upload;
            
            // Créer le dossier uploads s'il n'existe pas
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if (!move_uploaded_file($_FILES['document']['tmp_name'], "uploads/" . $nom_fichier)) {
                $message = "<div class='alert error'>❌ Erreur lors de l'enregistrement du fichier.</div>";
            }
        } else {
            $message = "<div class='alert error'>❌ Format de fichier invalide (PDF, JPG, PNG uniquement).</div>";
        }
    }

    // --- ENREGISTREMENT SQL ---
    if (empty($message)) {
        try {
            // Si un nouveau fichier est envoyé, on le met à jour. Sinon on garde l'ancien document_joint.
            $sql = "INSERT INTO notes (id_eleve, matiere, note_valeur, trimestre, observation, document_joint) 
                    VALUES (?, ?, ?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE 
                    note_valeur = VALUES(note_valeur), 
                    observation = VALUES(observation),
                    document_joint = IFNULL(VALUES(document_joint), document_joint)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_eleve, $matiere, $note, $trimestre, $observation, $nom_fichier]);
            
            $message = "<div class='alert success'>✅ Note et documents mis à jour avec succès !</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert error'>❌ Erreur système : " . $e->getMessage() . "</div>";
        }
    }
}

// 4. Compter les notes déjà saisies
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM notes WHERE id_eleve = ?");
$stmtCount->execute([$id_eleve]);
$nb_notes = $stmtCount->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Saisie des Notes | ELOHIM</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --gold: #facc15; --emerald: #064e3b; }
        select option { background-color: #ffffff !important; color: #000000 !important; padding: 10px; }
        input:focus, select:focus, textarea:focus { outline: 2px solid var(--gold) !important; background: rgba(255,255,255,0.15) !important; color: white !important; }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: bold; }
        .success { background: rgba(74, 222, 128, 0.2); color: #4ade80; border: 1px solid #4ade80; }
        .error { background: rgba(248, 113, 113, 0.2); color: #f87171; border: 1px solid #f87171; }
        .badge-info { background: var(--gold); color: #022c22; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 800; }
        textarea { width: 100%; background: rgba(255, 255, 255, 0.05); color: white; border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; padding: 12px; font-family: inherit; resize: none; margin-top: 5px; transition: 0.3s; }
        .file-input { background: rgba(255,255,255,0.05); padding: 10px; border-radius: 8px; border: 1px dashed rgba(255,255,255,0.3); color: white; width: 100%; cursor: pointer; }
    </style>
</head>
<body class="auth-body">

    <div class="glass-card" style="max-width: 500px;">
        <div class="brand">
            <i class="fa-solid fa-pen-to-square logo-icon" style="color: var(--gold);"></i>
            <h2>Saisie <span>des Notes</span></h2>
            <p>Élève : <strong><?= htmlspecialchars($eleve['nom_enfant'] . " " . $eleve['prenom_enfant']) ?></strong></p>
            <span class="badge-info"><?= $nb_notes ?> note(s) déjà saisie(s)</span>
        </div>

        <?= $message ?>

        <form action="" method="POST" enctype="multipart/form-data" class="modern-form">
            
            <div class="input-group">
                <label>Matière</label>
                <select name="matiere" required>
                    <option value="Mathématiques">Mathématiques</option>
                    <option value="Français">Français</option>
                    <option value="Anglais">Anglais</option>
                    <option value="SVT">SVT</option>
                    <option value="Physique-Chimie">Physique-Chimie</option>
                    <option value="Histoire-Géo">Histoire-Géo</option>
                    <option value="Philosophie">Philosophie</option>
                    <option value="EPS">EPS</option>
                    <option value="Education Civique">Education Civique</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="input-group">
                    <label><i class="fa-solid fa-star"></i> Note / 20</label>
                    <input type="number" name="note" step="0.01" min="0" max="20" placeholder="Ex: 11.37" required>
                </div>
                
                <div class="input-group">
                    <label><i class="fa-solid fa-clock"></i> Période</label>
                    <select name="trimestre" required>
                        <option value="1">1er Trimestre</option>
                        <option value="2">2ème Trimestre</option>
                        <option value="3">3ème Trimestre</option>
                    </select>
                </div>
            </div>

            <div class="input-group" style="margin-top: 15px;">
                <label><i class="fa-solid fa-comment-dots"></i> Observation / Commentaire Global</label>
                <textarea name="observation" rows="3" placeholder="Tapez ici le bilan de l'ensemble du travail de l'élève..."></textarea>
            </div>

            <div class="input-group" style="margin-top: 15px;">
                <label><i class="fa-solid fa-paperclip"></i> Joindre un justificatif (PDF, Image)</label>
                <input type="file" name="document" class="file-input">
            </div>

            <button type="submit" class="btn-submit" style="margin-top: 20px;">ENREGISTRER / METTRE À JOUR</button>
        </form>

        <div class="auth-footer" style="margin-top: 20px;">
            <a href="liste_eleves.php"><i class="fa-solid fa-arrow-left"></i> Retour à la liste</a>
        </div>
    </div>

</body>
</html>