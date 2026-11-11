<?php
// POST: inicia sesión con correo y contraseña
require_once(__DIR__ . "/Connection.php");
require_once(__DIR__ . "/core/Request.php");
require_once(__DIR__ . "/core/Auth.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Response::error("Método no permitido", -1, 405);
}

// Datos enviados
$pdo = new Connection();
$datos = Request::body();
$correo = strtolower(Request::texto($datos["correo"] ?? ""));
$clave = (string)($datos["password"] ?? "");

// Validación básica
if ($correo === "" || $clave === "") {
    Response::error("Ingresa correo y contraseña");
}

// Límite de intentos: 5 fallos por correo o 20 por IP en 15 minutos
$ip = $_SERVER["REMOTE_ADDR"] ?? "";
$st = $pdo->prepare("SELECT SUM(correo = ?) AS por_correo, COUNT(*) AS por_ip
    FROM intentos_login WHERE ip = ? AND fecha > NOW() - INTERVAL 15 MINUTE");
$st->execute([$correo, $ip]);
$intentos = $st->fetch();
if ((int)$intentos["por_correo"] >= 5 || (int)$intentos["por_ip"] >= 20) {
    Response::error("Demasiados intentos fallidos. Espera 15 minutos e inténtalo de nuevo", -1, 429);
}

// Busca el correo en cada tabla de rol (solo cuentas activas)
foreach (["admin", "profesor", "estudiante"] as $rol) {
    $tabla = Auth::tabla($rol);
    $st = $pdo->prepare("SELECT id, nombre, password FROM $tabla WHERE correo = ? AND estado = 1");
    $st->execute([$correo]);
    $u = $st->fetch();

    if ($u && password_verify($clave, $u["password"])) {
        // Entró bien: se borran sus intentos fallidos
        $pdo->prepare("DELETE FROM intentos_login WHERE correo = ? AND ip = ?")->execute([$correo, $ip]);
        Auth::iniciarSesion((int)$u["id"], $rol, $u["nombre"], !empty($datos["recordar"]));
        Response::success(["id" => (int)$u["id"], "rol" => $rol, "nombre" => $u["nombre"]], "Bienvenido");
    }
}

// Ningún rol coincidió: guarda el intento y limpia los viejos
$pdo->prepare("INSERT INTO intentos_login (correo, ip) VALUES (?, ?)")->execute([$correo, $ip]);
$pdo->exec("DELETE FROM intentos_login WHERE fecha < NOW() - INTERVAL 1 DAY");
Response::error("Correo o contraseña incorrectos", -1, 401);
