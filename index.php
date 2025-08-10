<?php
session_start();

// Token de autenticação da PushinPay
$token = '41909|sPaCf1Ns2mh7K7whnrSjI1Xl48T0lAkfeo2yeZEY772f30ae';
$api_url = 'https://api.pushinpay.com.br/api/pix/cashIn';

// Cabeçalhos para autenticação com o token
$headers = [
    "Authorization: Bearer $token",
    "Accept: application/json",
    "Content-Type: application/json"
];

// Função para gerar um token CSRF
function generateCSRFToken() {
    return bin2hex(random_bytes(32));
}

// Função para validar o valor inserido
function validarValor($valor) {
    return $valor >= 0.50;
}

// Gerar token CSRF se não existir
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generateCSRFToken();
}

// Definir valor padrão
$value = 6000;  // Valor em centavos (R$ 60,00)
$response_data = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar o token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Erro de segurança: CSRF inválido");
    }

    // Pega o valor inserido pelo usuário e converte para centavos
    $value_in_reais = floatval($_POST['value']);
    if (!validarValor($value_in_reais)) {
        die("Erro: O valor inserido deve ser maior que R$0,50.");
    }
    
    $value = $value_in_reais * 100;  // Convertendo para centavos
    
    // Dados para criar o PIX
    $data = [
        "value" => $value,  // Valor em centavos
        "webhook_url" => "https://80-ikw68058pigsf3xalvb0i-508520ae.manusvm.computer/webhook.php",
        "split_rules" => []
    ];

    // Inicializar a requisição cURL
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Enviar a requisição e obter a resposta
    $response = curl_exec($ch);
    curl_close($ch);

    // Verificar se a resposta foi recebida corretamente
    if ($response !== false) {
        $response_data = json_decode($response, true);
        
        // Comentado: Salvamento no banco de dados (será implementado posteriormente)
        /*
        if (isset($response_data['qr_code_base64'])) {
            $pdo = new PDO('mysql:host=localhost;dbname=seu_banco', 'usuario', 'senha');
            $stmt = $pdo->prepare("INSERT INTO transacoes (id_transacao, valor, status) VALUES (:id_transacao, :valor, :status)");
            $stmt->bindParam(':id_transacao', $response_data['id']);
            $stmt->bindParam(':valor', $value);
            $stmt->bindParam(':status', $response_data['status']);
            $stmt->execute();
        }
        */
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pushin Pay - Pagamento PIX</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Menu Lateral -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3>pushin <span>pay</span></h3>
        </div>
        <ul class="list-unstyled components">
            <li class="active"><a href="#" onclick="showSection('home')">🏠 Home</a></li>
            <li><a href="#" onclick="showSection('extrato')">📊 Extrato</a></li>
            <li><a href="#" onclick="showSection('transacoes')">💳 Transações</a></li>
            <li><a href="#" onclick="showSection('transferencias')">💸 Transferências</a></li>
            <li><a href="#" onclick="showSection('produtos')">📦 Produtos</a></li>
            <li><a href="#" onclick="showSection('checkout')">🛒 Checkout</a></li>
            <li><a href="#" onclick="showSection('boleto')">📄 Criar Boleto</a></li>
            <li><a href="#" onclick="showSection('plugins')">🔌 Plugins</a></li>
            <li><a href="#" onclick="showSection('relatorios')">📈 Relatórios Gerais</a></li>
            <li><a href="#" onclick="showSection('mensagens')">💬 Mensagens</a></li>
            <li><a href="#" onclick="showSection('configuracoes')">⚙️ Configurações</a></li>
            <li><a href="#" onclick="showSection('aplicativo')">📱 Aplicativo</a></li>
            <li><a href="#" onclick="showSection('suporte')">🆘 Suporte</a></li>
        </ul>
    </nav>

    <!-- Toggle do Menu para Mobile -->
    <button id="sidebarToggle" class="sidebar-toggle">☰</button>

    <!-- Conteúdo Principal -->
    <div id="content">
        <!-- Seção Home (PIX) -->
        <div id="home" class="content-section active">
            <?php if (!isset($response_data['qr_code_base64'])): ?>
            <!-- Tela inicial para inserir valor -->
            <div class="container">
                <div class="payment-form">
                    <h1>💰 Pagamento via PIX</h1>
                    <form method="POST" id="paymentForm">
                        <div class="form-group">
                            <label for="value">Valor do pagamento</label>
                            <input type="number" name="value" id="value" placeholder="Digite o valor em reais" 
                                   required step="0.01" min="0.50" value="<?= number_format($value / 100, 2, '.', '') ?>">
                        </div>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit" class="generate-pix-btn">Gerar PIX</button>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <!-- Tela de passos para pagar -->
            <div class="container">
                <div class="payment-steps">
                    <h1>passos para pagar:</h1>
                    
                    <!-- Passo 1: Copiar código PIX -->
                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Copie o código PIX:</h3>
                            <div class="pix-code-container">
                                <input type="text" id="pixCodeInput" value="<?= $response_data['qr_code'] ?>" readonly>
                            </div>
                            <button onclick="copyPixCode()" class="copy-btn">Copiar código PIX</button>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="qr-code-section">
                        <img src="<?= $response_data['qr_code_base64'] ?>" alt="QR Code PIX" class="qr-code">
                    </div>

                    <!-- Passos 2-5 -->
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Abra o aplicativo do seu banco favorito</h3>
                        </div>
                    </div>

                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Na seção de PIX, selecione a opção pix copia e cola</h3>
                        </div>
                    </div>

                    <div class="step">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Cole o código</h3>
                        </div>
                    </div>

                    <div class="step">
                        <div class="step-number">5</div>
                        <div class="step-content">
                            <h3>Confirme o pagamento</h3>
                        </div>
                    </div>

                    <!-- Botão final -->
                    <button class="payment-done-btn" onclick="window.location.reload()">Já fiz o pagamento</button>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Outras seções do menu -->
        <div id="extrato" class="content-section">
            <div class="container">
                <h1>📊 Extrato</h1>
                <p>Visualize seu extrato de transações aqui.</p>
                <div class="placeholder-content">
                    <div class="card">
                        <h3>Saldo Atual</h3>
                        <p class="balance">R$ 1.234,56</p>
                    </div>
                    <div class="card">
                        <h3>Últimas Transações</h3>
                        <p>Nenhuma transação encontrada.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="transacoes" class="content-section">
            <div class="container">
                <h1>💳 Transações</h1>
                <p>Gerencie suas transações PIX aqui.</p>
                <div class="placeholder-content">
                    <div class="card">
                        <h3>Transações Recentes</h3>
                        <p>Carregando transações...</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="transferencias" class="content-section">
            <div class="container">
                <h1>💸 Transferências</h1>
                <p>Realize transferências PIX aqui.</p>
                <div class="placeholder-content">
                    <div class="card">
                        <h3>Nova Transferência</h3>
                        <p>Funcionalidade em desenvolvimento.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="produtos" class="content-section">
            <div class="container">
                <h1>📦 Produtos</h1>
                <p>Gerencie seus produtos aqui.</p>
                <div class="placeholder-content">
                    <div class="card">
                        <h3>Catálogo de Produtos</h3>
                        <p>Nenhum produto cadastrado.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="checkout" class="content-section">
            <div class="container">
                <h1>🛒 Checkout</h1>
                <p>Configure seu checkout aqui.</p>
                <div class="placeholder-content">
                    <div class="card">
                        <h3>Configurações de Checkout</h3>
                        <p>Personalize sua página de checkout.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="boleto" class="content-section">
            <div class="container">
                <h1>📄 Criar Boleto</h1>
                <p>Gere boletos bancários aqui.</p>
                <div class="placeholder-content">
                    <div class="card">
                        <h3>Novo Boleto</h3>
                        <p>Funcionalidade em desenvolvimento.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="plugins" class="content-section">
            <div class="container">
                <h1>🔌 Plugins</h1>
                <p>Gerencie plugins e integrações.</p>
                <div class="placeholder-content">
                    <div class="card">
                        <h3>Plugins Disponíveis</h3>
                        <p>Nenhum plugin instalado.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="relatorios" class="content-section">
            <div class="container">
                <h1>📈 Relatórios Gerais</h1>
                <p>Visualize relatórios detalhados.</p>
                <div class="placeholder-content">
                    <div class="card">
                        <h3>Relatório Mensal</h3>
                        <p>Dados não disponíveis.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="mensagens" class="content-section">
            <div class="container">
                <h1>💬 Mensagens</h1>
                <p>Central de mensagens e notificações.</p>
                <div class="placeholder-content">
                    <div class="card">
                        <h3>Caixa de Entrada</h3>
                        <p>Nenhuma mensagem nova.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="configuracoes" class="content-section">
            <div class="container">
                <h1>⚙️ Configurações</h1>
                <p>Configure sua conta e preferências.</p>
                <div class="placeholder-content">
                    <div class="card">
                        <h3>Configurações da Conta</h3>
                        <p>Gerencie suas configurações.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="aplicativo" class="content-section">
            <div class="container">
                <h1>📱 Aplicativo</h1>
                <p>Baixe nosso aplicativo móvel.</p>
                <div class="placeholder-content">
                    <div class="card">
                        <h3>Download do App</h3>
                        <p>Disponível na App Store e Google Play.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="suporte" class="content-section">
            <div class="container">
                <h1>🆘 Suporte</h1>
                <p>Central de ajuda e suporte técnico.</p>
                <div class="placeholder-content">
                    <div class="card">
                        <h3>Como podemos ajudar?</h3>
                        <p>Entre em contato conosco.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>

