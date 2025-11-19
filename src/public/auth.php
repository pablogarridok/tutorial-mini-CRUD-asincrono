<?php
require_once __DIR__ . '/../auth.php';

$accion = $_GET['action'] ?? $_POST['action'] ?? '';

header('Content-Type: application/json; charset=utf-8');

if ($accion === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $pass  = trim($data['password'] ?? '');

    if (login($email, $pass)) {
        echo json_encode(['ok'=>true,'role'=>$_SESSION['role']]);
    } else {
        http_response_code(401);
        echo json_encode(['ok'=>false,'error'=>'Credenciales inválidas']);
    }
    exit;
}

if ($accion === 'logout') {
    logout();
    echo json_encode(['ok'=>true]);
    exit;
}

// Me (info del usuario)
if ($accion === 'me') {
    $info = me();
    if ($info) echo json_encode(['ok'=>true,'user'=>$info]);
    else { http_response_code(401); echo json_encode(['ok'=>false]); }
    exit;
}

http_response_code(400);
echo json_encode(['ok'=>false,'error'=>'Acción no soportada']);
