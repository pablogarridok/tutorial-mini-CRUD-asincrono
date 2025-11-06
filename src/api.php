<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

function responder_json_exito(mixed $contenidoDatos = [], int $codigoHttp = 200): void {
    http_response_code($codigoHttp);
    echo json_encode(['ok' => true, 'data' => $contenidoDatos], JSON_UNESCAPED_UNICODE);
    exit;
}

function responder_json_error(string $mensajeError, int $codigoHttp = 400): void {
    http_response_code($codigoHttp);
    echo json_encode(['ok' => false, 'error' => $mensajeError], JSON_UNESCAPED_UNICODE);
    exit;
}

$ruta = __DIR__ . '/data.json';
if (!file_exists($ruta)) {
    file_put_contents($ruta, json_encode([]) . "\n");
}

$lista = json_decode((string) file_get_contents($ruta), true);
if (!is_array($lista)) $lista = [];

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

if ($method === 'GET' && $action === 'list') {
    responder_json_exito($lista);
}

if ($method === 'POST' && $action === 'create') {
    $raw = (string) file_get_contents('php://input');
    $data = $raw !== '' ? (json_decode($raw, true) ?? []) : [];
    $nombre = trim((string) ($data['nombre'] ?? $_POST['nombre'] ?? ''));
    $email = trim((string) ($data['email'] ?? $_POST['email'] ?? ''));
    if ($nombre === '' || $email === '') responder_json_error('Los campos "nombre" y "email" son obligatorios.', 422);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) responder_json_error('El email no es válido.', 422);
    $lista[] = ['nombre' => $nombre, 'email' => $email];
    file_put_contents($ruta, json_encode($lista, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
    responder_json_exito($lista, 201);
}

if (($method === 'POST' || $method === 'DELETE') && $action === 'delete') {
    $raw = (string) file_get_contents('php://input');
    $data = $raw !== '' ? (json_decode($raw, true) ?? []) : [];
    $index = $data['index'] ?? $_POST['index'] ?? $_GET['index'] ?? null;
    if ($index === null) responder_json_error('Falta "index".', 422);
    $i = (int)$index;
    if (!isset($lista[$i])) responder_json_error('Índice no existe.', 404);
    unset($lista[$i]);
    $lista = array_values($lista);
    file_put_contents($ruta, json_encode($lista, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
    responder_json_exito($lista);
}

responder_json_error('Acción no soportada.');
?>
