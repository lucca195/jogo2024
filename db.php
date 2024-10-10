<?php
$host = 'us-cluster-east-01.k8s.cleardb.net';
$username = 'bf4f36ce29443b';
$password = '6b0486f7';
$dbname = 'heroku_c37d1ea8733062b';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
