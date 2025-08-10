<?php
// Webhook para receber notificações da Pushin Pay
// Este arquivo será usado para processar notificações de pagamento

// Configurações de log
$log_file = 'webhook_log.txt';

// Função para registrar logs
function log_message($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    log_message("Método não permitido: " . $_SERVER['REQUEST_METHOD']);
    exit('Método não permitido');
}

// Obter o corpo da requisição
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log da requisição recebida
log_message("Webhook recebido: " . $input);

// Verificar se os dados foram decodificados corretamente
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    log_message("Erro ao decodificar JSON: " . json_last_error_msg());
    exit('Dados inválidos');
}

// Verificar se contém os campos necessários
if (!isset($data['id']) || !isset($data['status'])) {
    http_response_code(400);
    log_message("Campos obrigatórios ausentes");
    exit('Campos obrigatórios ausentes');
}

// Processar a notificação
$transaction_id = $data['id'];
$status = $data['status'];
$value = isset($data['value']) ? $data['value'] : 0;

log_message("Processando transação: ID=$transaction_id, Status=$status, Valor=$value");

// Aqui você pode adicionar a lógica para atualizar o banco de dados
// Exemplo (comentado para uso futuro):
/*
try {
    $pdo = new PDO('mysql:host=localhost;dbname=seu_banco', 'usuario', 'senha');
    $stmt = $pdo->prepare("UPDATE transacoes SET status = :status, updated_at = NOW() WHERE id_transacao = :id_transacao");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id_transacao', $transaction_id);
    $result = $stmt->execute();
    
    if ($result) {
        log_message("Transação atualizada com sucesso: $transaction_id");
    } else {
        log_message("Erro ao atualizar transação: $transaction_id");
    }
} catch (PDOException $e) {
    log_message("Erro de banco de dados: " . $e->getMessage());
}
*/

// Responder com sucesso
http_response_code(200);
log_message("Webhook processado com sucesso");
echo 'OK';
?>

