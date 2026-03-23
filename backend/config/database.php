<?php

$host = "db.unqfceucutbdfkurllsa.supabase.co";
$port = "5432";
$dbname = "postgres";
$user = "postgres";
$password = "Cajati2025*";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    // O PDO é a melhor forma de interagir com bancos no PHP hoje em dia
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Retorna erro em formato JSON caso a conexão falhe
    die(json_encode(["status" => "error", "message" => "Erro de conexão: " . $e->getMessage()]));
}
?>