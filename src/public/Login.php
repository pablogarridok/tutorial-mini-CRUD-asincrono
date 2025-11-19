<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') header("Location: index_ajax.php");
    else header("Location: sociograma.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../auth.php';
    $email = $_POST['email'] ?? '';
    $pass  = $_POST['password'] ?? '';
    if (login($email, $pass)) {
        if ($_SESSION['role']==='admin') header("Location: index_ajax.php");
        else header("Location: sociograma.php");
        exit;
    } else {
        $error = 'Credenciales inválidas';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<main>
<h1>Login</h1>
<?php if($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post">
<label>Email</label>
<input type="email" name="email" required>
<label>Contraseña</label>
<input type="password" name="password" required>
<button type="submit">Entrar</button>
</form>
</main>
</body>
</html>
