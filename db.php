<?php
$host = 'localhost';
$db   = 'bd_cours_appui'; 
$user = 'root';
$pass = '';
$port = '3306'; // Vérifie que MySQL écoute bien sur 3306
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Débogage activé pour le développement
$debug = true;

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    if ($debug) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    } else {
        die("Connexion au serveur impossible. Veuillez réessayer plus tard.");
    }
}
?>
