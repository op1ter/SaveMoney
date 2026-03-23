<?php
// backend/api/cadastro.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Inclui a conexão com o banco
require_once '../config/database.php';

// Recebe os dados do frontend (como estamos enviando via fetch, pegamos o JSON)
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->nome) && !empty($data->email) && !empty($data->senha)) {
    try {
        // Criptografa a senha antes de salvar (MUITO IMPORTANTE)
        $senhaHash = password_hash($data->senha, PASSWORD_DEFAULT);

        // Prepara a query de inserção (ajuste os nomes das colunas conforme sua tabela no Supabase)
        $query = "INSERT INTO usuarios (nome, data_nascimento, cpf, telefone, cep, endereco, numero, cidade, estado, email, senha) 
                  VALUES (:nome, :data_nascimento, :cpf, :telefone, :cep, :endereco, :numero, :cidade, :estado, :email, :senha)";
        
        $stmt = $pdo->prepare($query);

        // Bind dos parâmetros para evitar SQL Injection
        $stmt->bindParam(':nome', $data->nome);
        $stmt->bindParam(':data_nascimento', $data->data_nascimento);
        $stmt->bindParam(':cpf', $data->cpf);
        $stmt->bindParam(':telefone', $data->telefone);
        $stmt->bindParam(':cep', $data->cep);
        $stmt->bindParam(':endereco', $data->endereco);
        $stmt->bindParam(':numero', $data->numero);
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
        // Trata erro de e-mail ou CPF duplicado (se configurado como UNIQUE no banco)
        if ($e->getCode() == '23505') {
             echo json_encode(["status" => "error", "message" => "E-mail ou CPF já cadastrados."]);
        } else {
             echo json_encode(["status" => "error", "message" => "Erro no banco: " . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Dados incompletos."]);
}
?>