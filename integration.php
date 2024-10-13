<?php
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/
// integration.php

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Si no está autenticado, devolver un error
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit();
}

header('Content-Type: application/json');

// Obtener los datos de la solicitud
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['action'])) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó ninguna acción.']);
    exit;
}

$action = $input['action'];

// Mapeo de acciones a webhook IDs
$actionMap = [
    'salir' => '-YOUR_WEBHOOK_ID', 
    'llegar' => '-YOUR_WEBHOOK_ID',
    'abrir-entrada' => '-YOUR_WEBHOOK_ID',
    'abrir-garaje' => '-YOUR_WEBHOOK_ID',
    'he-llegado' => '-YOUR_WEBHOOK_ID'
];

// Verificar si la acción existe en el mapeo
if (!array_key_exists($action, $actionMap)) {
    echo json_encode(['success' => false, 'message' => 'Acción no reconocida.']);
    exit;
}

// Construir la URL del webhook
$haUrl = 'http://YOUR_HOME_ASSSITANT_SERVER_HOST_OR_IP:8123/api/webhook/' . $actionMap[$action];

// Iniciar cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $haUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true); // Para obtener el encabezado de la respuesta
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

// Ejecutar la solicitud
$response = curl_exec($ch);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    echo json_encode(['success' => false, 'message' => 'Error cURL: ' . $error]);
    exit;
}

// Obtener información de la respuesta
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(['success' => false, 'message' => 'Error HTTP ' . $httpCode, 'response' => $body]);
    exit;
}

// Asumimos que la respuesta es exitosa
echo json_encode(['success' => true, 'response' => $body]);
?>
