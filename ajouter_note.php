<?php
session_start();
require 'db.php';

$message = "";
$status = "";

// Traitement de l'enregistrement de la note
if (isset($_POST['submit_note'])) {
    $id_eleve = $_POST['id_eleve'];
    $matiere = htmlspecialchars($_POST['matiere']);
    $note = $_POST['note'];
    $type_eval = $_POST['type_eval'];
    $date_eval = $_POST['date_eval'];

    if (!empty($id_eleve) && !empty($matiere) && isset($note)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO evaluations (id_eleve, matiere, note, type_eval, date_eval) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id_eleve, $matiere, $note, $type_eval, $date_eval]);
            $message = "La note a été enregistrée avec succès !";
            $status = "success";
        } catch (Exception $e) {
            $message = "Erreur lors de l'enregistrement : " . $e->getMessage();
            $status = "error";
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
        $status = "error";
    }
}

// Récupération de la liste des élèves pour le menu déroulant
$eleves = $pdo->query("SELECT id_eleve, nom_enfant, prenom_enfant, classe FROM eleves ORDER BY classe, nom_enfant")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie des Notes | ELOHIM APPUI</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --gold: #facc15; --emerald: #042f2e; }
        body { 
            background: radial-gradient(circle at top right, #065f46, #022c22);
            color: white; font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0;
        }
        .admin-container {
            width: 100%; max-width: 500px; padding: 30px;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(15px);
            border-radius: 25px; border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { color: var(--gold); font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .header p { color: #94a3b8; font-size: 0.9rem; }

        .form-group { margin-bottom: 20px; }
        label { display: block; color: var(--gold); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin-bottom: 8px; margin-left: 5px; }
        
        select, input {
            width: 100%; padding: 14px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05); color: white; font-size: 1rem; outline: none; transition: 0.3s;
        }
        select:focus, input:focus { border-color: var(--gold); background: rgba(255, 255, 255, 0.1); }
        option { background: #022c22; color: white; }

        .btn-submit {
            width: 100%; padding: 16px; background: var(--gold); color: #042f2e;
            border: none; border-radius: 12px; font-weight: 800; cursor: pointer;
            transition: 0.3s; font-size: 1rem; margin-top: 10px;
        }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(250, 204, 21, 0.3); }

        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; text-align: center; font-size: 0.9rem; font-weight: 600; }
        .success { background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid #22c55e; }
        .error { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #ef4444; }
        
        .nav-links { margin-top: 25px; text-align: center; }
        .nav-links a { color: #94a3b8; text-decoration: none; font-size: 0.85rem; }
        .nav-links a:hover { color: var(--gold); }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="header">
        <h2>Saisie de Note</h2>
        <p>Espace Collaborateur Staff</p>
    </div>

    <?php if($message): ?>
        <div class="alert <?= $status ?>"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Choisir l'élève</label>
            <select name="id_eleve" required>
                <option value="" disabled selected>-- Liste des élèves --</option>
                <?php foreach($eleves as $e): ?>
                    <option value="<?= $e['id_eleve'] ?>">
                        <?= strtoupper($e['nom_enfant']) ?> <?= $e['prenom_enfant'] ?> (<?= $e['classe'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Matière</label>
            <input type="text" name="matiere" placeholder="Ex: Mathématiques" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Note / 20</label>
                <input type="number" step="0.25" min="0" max="20" name="note" placeholder="00.00" required>
            </div>
            <div class="form-group">
                <label>Type</label>
                <select name="type_eval">
                    <option value="Interrogation">Interrogation</option>
                    <option value="Devoir">Devoir</option>
                    <option value="Composition">Composition</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Date de l'évaluation</label>
            <input type="date" name="date_eval" value="<?= date('Y-m-d') ?>" required>
        </div>

        <button type="submit" name="submit_note" class="btn-submit">
            <i class="fa-solid fa-cloud-arrow-up"></i> ENREGISTRER LA NOTE
        </button>
    </form>

    <div class="nav-links">
        <a href="login.php"><i class="fa-solid fa-house"></i> Retour à l'accueil</a>
    </div>
</div>

</body>
</html>