<?php

$host = "db.unqfceucutbdfkurllsa.supabase.co";
$port = "5432";
$dbname = "postgres";
$user = "postgres";
$password = "Cajati2025*";

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}