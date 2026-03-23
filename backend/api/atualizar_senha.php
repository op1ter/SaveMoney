<?php
// backend/api/atualizar_senha.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/database.php';

// Recebe o JSON com o token e a nova senha
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->token) && !empty($data->nova_senha)) {
    try {
        // 1. Procura se existe algum usuário com esse token no banco
        $query = 'SELECT "ID_PF" FROM "USUARIO" WHERE "TOKEN_SENHA_PF" = :token LIMIT 1';
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':token', $data->token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $idUsuario = $usuario['ID_PF'];

            // 2. Criptografa a nova senha
            $senhaHash = password_hash($data->nova_senha, PASSWORD_DEFAULT);

            // 3. Atualiza a senha no banco e apaga o token (para invalidar o link)
            $updateQuery = 'UPDATE "USUARIO" SET "SENHA_PF" = :senha, "TOKEN_SENHA_PF" = NULL WHERE "ID_PF" = :id';
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->bindParam(':senha', $senhaHash);
            $updateStmt->bindParam(':id', $idUsuario);
            
            if ($updateStmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Senha atualizada com sucesso!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Erro ao atualizar a senha."]);
            }
        } else {
            // Se não encontrou o token, é porque é inválido ou já foi usado
            echo json_encode(["status" => "error", "message" => "Link inválido ou expirado."]);
        }

    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Erro no banco de dados: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Dados incompletos."]);
}
?>