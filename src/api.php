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

$rutaArchivoDatosJson = __DIR__ . '/data.json';
if (!file_exists($rutaArchivoDatosJson)) {
    file_put_contents($rutaArchivoDatosJson, json_encode([]) . "\n");
}
$listaUsuarios = json_decode((string) file_get_contents($rutaArchivoDatosJson), true);
if (!is_array($listaUsuarios)) $listaUsuarios = [];

$metodoHttpRecibido = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$accionSolicitada = $_GET['action'] ?? $_POST['action'] ?? 'list';

if ($metodoHttpRecibido === 'GET' && $accionSolicitada === 'list') {
    responder_json_exito($listaUsuarios);
}

if ($metodoHttpRecibido === 'POST' && $accionSolicitada === 'create') {
    $cuerpoBruto = (string) file_get_contents('php://input');
    $datosDecodificados = $cuerpoBruto !== '' ? (json_decode($cuerpoBruto, true) ?? []) : [];
    $nombreUsuarioNuevo = trim((string) ($datosDecodificados['nombre'] ?? $_POST['nombre'] ?? ''));
    $correoUsuarioNuevo = trim((string) ($datosDecodificados['email'] ?? $_POST['email'] ?? ''));

    if ($nombreUsuarioNuevo === '' || $correoUsuarioNuevo === '') responder_json_error('Los campos "nombre" y "email" son obligatorios.', 422);
    if (!filter_var($correoUsuarioNuevo, FILTER_VALIDATE_EMAIL)) responder_json_error('El campo "email" no tiene un formato válido.', 422);

    $listaUsuarios[] = ['nombre' => $nombreUsuarioNuevo, 'email' => $correoUsuarioNuevo];
    file_put_contents($rutaArchivoDatosJson, json_encode($listaUsuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
    responder_json_exito($listaUsuarios, 201);
}

if (($metodoHttpRecibido === 'POST' || $metodoHttpRecibido === 'DELETE') && $accionSolicitada === 'delete') {
    $indiceEnQuery = $_GET['index'] ?? null;
    if ($indiceEnQuery === null) {
        $cuerpoBruto = (string) file_get_contents('php://input');
        $datosDecodificados = $cuerpoBruto !== '' ? (json_decode($cuerpoBruto, true) ?? []) : [];
        $indiceEnQuery = $datosDecodificados['index'] ?? $_POST['index'] ?? null;
    }
    if ($indiceEnQuery === null) responder_json_error('Falta el parámetro "index" para eliminar.', 422);
    $indiceUsuarioAEliminar = (int) $indiceEnQuery;
    if (!isset($listaUsuarios[$indiceUsuarioAEliminar])) responder_json_error('El índice indicado no existe.', 404);

    unset($listaUsuarios[$indiceUsuarioAEliminar]);
    $listaUsuarios = array_values($listaUsuarios);
    file_put_contents($rutaArchivoDatosJson, json_encode($listaUsuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
    responder_json_exito($listaUsuarios);
}

responder_json_error('Acción no soportada. Use list | create | delete', 400);
