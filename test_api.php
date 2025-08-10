<?php
// Teste simples da API Pushin Pay
$token = '41909|sPaCf1Ns2mh7K7whnrSjI1Xl48T0lAkfeo2yeZEY772f30ae';
$api_url = 'https://api.pushinpay.com.br/api/pix/cashIn';

$headers = [
    "Authorization: Bearer $token",
    "Accept: application/json",
    "Content-Type: application/json"
];

$data = [
    "value" => 1000,  // R$ 10,00 em centavos
    "webhook_url" => "https://80-ikw68058pigsf3xalvb0i-508520ae.manusvm.computer/webhook.php",
    "split_rules" => []
];

echo "<h1>Teste da API Pushin Pay</h1>";
echo "<h2>Dados enviados:</h2>";
echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "<h2>Código HTTP: $http_code</h2>";

if ($curl_error) {
    echo "<h2>Erro cURL:</h2>";
    echo "<pre>$curl_error</pre>";
}

echo "<h2>Resposta da API:</h2>";
echo "<pre>$response</pre>";

if ($response) {
    $response_data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<h2>Dados decodificados:</h2>";
        echo "<pre>" . json_encode($response_data, JSON_PRETTY_PRINT) . "</pre>";
        
        if (isset($response_data['qr_code_base64'])) {
            echo "<h2>QR Code gerado:</h2>";
            echo "<img src='data:image/png;base64," . $response_data['qr_code_base64'] . "' alt='QR Code PIX' style='max-width: 300px;'>";
        }
    } else {
        echo "<h2>Erro ao decodificar JSON:</h2>";
        echo "<pre>" . json_last_error_msg() . "</pre>";
    }
}
?>

