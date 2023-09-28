<?php

require_once "db_config.php";



// Datenbank-Verbindungsdaten
$host = $db_host;
$dbname = $db_name;
$username = $db_username;
$password = $db_password;

// DSN (Data Source Name) enthält die erforderlichen Informationen für PDO
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

// Optionen für die PDO-Verbindung
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    // Versuchen, eine Verbindung zur Datenbank herzustellen
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Fehlerbehandlung, falls die Verbindung fehlschlägt
    die('Verbindungsfehler: ' . $e->getMessage());
}
?>
