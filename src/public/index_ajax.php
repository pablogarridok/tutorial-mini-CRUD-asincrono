<?php
require_once __DIR__ . '/../auth.php';
require_login();

// Solo admins pueden entrar
if ($_SESSION['role'] !== 'admin') {
    header("Location: sociograma.php");
    exit;
}
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Mini CRUD AJAX (fetch + JSON)</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<header class="encabezado-aplicacion">
<h1 class="encabezado-aplicacion__titulo">Mini CRUD con fetch() (sin Base de Datos)</h1>
<p class="encabezado-aplicacion__descripcion">
Esta pantalla usa JavaScript para hablar con la API PHP y actualizar la tabla sin recargar la página.
</p>
</header>
<main class="zona-principal" id="zona-principal" tabindex="-1">
<div id="msg" class="mensajes-estado" role="status" aria-live="polite" aria-atomic="true"></div>
<section class="bloque-formulario" aria-labelledby="titulo-formulario">
<h2 id="titulo-formulario">Agregar nuevo usuario</h2>
<form id="formCreate" class="formulario-alta-usuario" autocomplete="on" novalidate>
<div class="form-row">
<label for="campo-nombre" class="form-label">Nombre</label>
<input id="campo-nombre" name="nombre" class="form-input" type="text" required minlength="2" maxlength="60" placeholder="Ej.: Ana Pérez" autocomplete="name" inputmode="text">
</div>
<div class="form-row">
<label for="campo-email" class="form-label">Email</label>
<input id="campo-email" name="email" class="form-input" type="email" required maxlength="120" placeholder="ejemplo@correo.com" autocomplete="email" inputmode="email">
</div>
<div class="form-actions">
<button id="boton-agregar-usuario" type="submit" class="boton-primario">Agregar usuario</button>
<span id="indicador-cargando" class="indicador-cargando" aria-hidden="true" hidden>Cargando...</span>
</div>
</form>
</section>
<section class="bloque-listado" aria-labelledby="titulo-listado">
<h2 id="titulo-listado">Listado de usuarios</h2>
<div class="tabla-contenedor" role="region" aria-labelledby="titulo-listado">
<table class="tabla-usuarios">
<thead>
<tr>
<th scope="col">#</th>
<th scope="col">Nombre</th>
<th scope="col">Email</th>
<th scope="col">Acción</th>
</tr>
</thead>
<tbody id="tbody">
<tr id="fila-estado-vacio" class="fila-estado-vacio" hidden>
<td colspan="4"><em>No hay usuarios registrados todavía.</em></td>
</tr>
</tbody>
</table>
</div>
</section>
</main>
<footer class="pie-aplicacion">
<p><a href="/index.php">Ir a Parte 1 (clásica sin AJAX)</a></p>
</footer>
<script src="/assets/js/main.js" defer></script>
</body>
</html>
