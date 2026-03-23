<?php
// backend/api/login.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/database.php';

// Recebe os dados
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->senha)) {
    try {
        // Busca o usuário pelo email na tabela (trazendo apenas o ID, NOME e SENHA)
        $query = 'SELECT "ID_PF", "NOME_PF", "SENHA_PF" FROM "USUARIO" WHERE "EMAIL_PF" = :email LIMIT 1';
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $data->email);
        $stmt->execute();

        // Verifica se encontrou algum e-mail
        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica se a senha digitada bate com a senha criptografada do banco
            if (password_verify($data->senha, $usuario['SENHA_PF'])) {
                // Sucesso! Retorna os dados do usuário
                echo json_encode([
                    "status" => "success", 
                    "message" => "Login realizado com sucesso",
                    "user" => [
                        "id" => $usuario['ID_PF'],
                        "nome" => $usuario['NOME_PF']
                    ]
                ]);
            } else {
                // Senha incorreta
                echo json_encode(["status" => "error", "message" => "Senha incorreta."]);
            }
        } else {
            // Email não encontrado
            echo json_encode(["status" => "error", "message" => "E-mail não encontrado."]);
        }

    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Erro no banco: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Dados incompletos."]);
}
?>