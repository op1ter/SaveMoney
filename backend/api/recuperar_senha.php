<?php
// backend/api/recuperar_senha.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Carrega o PHPMailer que o Composer instalou
require_once '../vendor/autoload.php';
require_once '../config/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email)) {
    $emailUsuario = $data->email;

    try {
        // 1. Verifica se o e-mail existe no banco de dados do Supabase
        $query = 'SELECT "ID_PF", "NOME_PF" FROM "USUARIO" WHERE "EMAIL_PF" = :email LIMIT 1';
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $emailUsuario);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $nomeUsuario = $usuario['NOME_PF'];
            $idUsuario = $usuario['ID_PF'];

            // 2. Gera o Token e salva no banco de dados para validarmos depois
            $token = bin2hex(random_bytes(16)); 
            
            $updateQuery = 'UPDATE "USUARIO" SET "TOKEN_SENHA_PF" = :token WHERE "ID_PF" = :id';
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->bindParam(':token', $token);
            $updateStmt->bindParam(':id', $idUsuario);
            $updateStmt->execute();

            // 3. Configura o PHPMailer
            $mail = new PHPMailer(true);

            // Configurações do Servidor SMTP do Google
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'joaovictorfelipe99@gmail.com'; 
            $mail->Password   = 'fhmxwdtxvispmkpg';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Remetente e Destinatário
            $mail->setFrom('joaovictorfelipe99@gmail.com', 'Sistema Save Money');
            $mail->addAddress($emailUsuario, $nomeUsuario);

            // Conteúdo do E-mail
            $mail->isHTML(true);
            $mail->Subject = 'Recuperacao de Senha - Save Money';
            
            // Link com o caminho correto do seu projeto
            $linkRecuperacao = "http://localhost/savemoney/public/nova_senha.html?token=" . $token;

            $mail->Body    = "
                <h2>Olá, {$nomeUsuario}!</h2>
                <p>Recebemos um pedido para redefinir a senha da sua conta no Save Money.</p>
                <p>Clique no link abaixo para criar uma nova senha:</p>
                <p><a href='{$linkRecuperacao}' style='background: #32c54a; color: #111; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Redefinir Minha Senha</a></p>
                <p>Se você não fez esse pedido, pode ignorar este e-mail.</p>
            ";

            $mail->send();
            
            echo json_encode(["status" => "success", "message" => "Instruções enviadas para o seu e-mail!"]);
        } else {
            // Por segurança, não dizemos se o e-mail existe ou não na base
            echo json_encode(["status" => "success", "message" => "Se o e-mail existir, as instruções foram enviadas!"]);
        }

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Erro ao enviar e-mail: {$mail->ErrorInfo}"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Erro no banco: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Por favor, informe um e-mail."]);
}
?>