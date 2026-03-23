<?php
// backend/api/cadastro.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Inclui a conexão com o banco
require_once '../config/database.php';

// Recebe os dados em JSON enviados pelo fetch() do JavaScript
$data = json_decode(file_get_contents("php://input"));

// Verifica se os dados essenciais chegaram
if (!empty($data->nome) && !empty($data->email) && !empty($data->senha) && !empty($data->cpf)) {
    try {
        // Criptografa a senha antes de salvar no banco
        $senhaHash = password_hash($data->senha, PASSWORD_DEFAULT);

        // A query agora mapeia exatamente as colunas da sua tabela USUARIO
        $query = 'INSERT INTO "USUARIO" (
                    "NOME_PF", 
                    "DATA_NASCIMENTO_PF", 
                    "CPF_PF", 
                    "CELULAR_PF", 
                    "ENDERECO_PF", 
                    "NUMERO_CASA_PF",
                    "CIDADE_PF",
                    "ESTADO_PF",
                    "EMAIL_PF", 
                    "SENHA_PF"
                  ) VALUES (
                    :nome, 
                    :data_nascimento, 
                    :cpf, 
                    :celular, 
                    :endereco, 
                    :numero_casa,
                    :cidade,
                    :estado,
                    :email, 
                    :senha
                  )';
        
        $stmt = $pdo->prepare($query);

        // Aqui ligamos os dados que vieram do JavaScript com as variáveis da Query acima
        $stmt->bindParam(':nome', $data->nome);
        $stmt->bindParam(':data_nascimento', $data->data_nascimento);
        $stmt->bindParam(':cpf', $data->cpf);
        $stmt->bindParam(':celular', $data->telefone); // Do JS vem como 'telefone'
        $stmt->bindParam(':endereco', $data->endereco); 
        $stmt->bindParam(':numero_casa', $data->numero); // Do JS vem como 'numero'
        $stmt->bindParam(':cidade', $data->cidade);
        $stmt->bindParam(':estado', $data->estado);
        $stmt->bindParam(':email', $data->email);
        $stmt->bindParam(':senha', $senhaHash);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Usuário cadastrado com sucesso!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Erro ao cadastrar usuário."]);
        }
    } catch (PDOException $e) {
        // Captura erro de restrição UNIQUE (CPF ou E-mail repetido)
        if ($e->getCode() == '23505') {
             echo json_encode(["status" => "error", "message" => "E-mail ou CPF já cadastrados."]);
        } else {
             echo json_encode(["status" => "error", "message" => "Erro no banco: " . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Dados incompletos. Preencha todos os campos."]);
}
?>