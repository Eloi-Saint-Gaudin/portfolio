<?php
$envFile = __DIR__ . "/../.env";


if (!file_exists($envFile)) {
    die(".env n'existe pas veuiller le creer a l aracine de l'app");
}

$env = parse_ini_file($envFile);

$dbHost = $env["DB_HOST"];
$dbName = $env["DB_NAME"];
$dbUser = $env["DB_USER"];
$dbPassword = $env["DB_PASSWORD"];


try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
} catch (PDOException $e) {
    die("Erreur de connection a la db : " . $e->getMessage());
}
?>