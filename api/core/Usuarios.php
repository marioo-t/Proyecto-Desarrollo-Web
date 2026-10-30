<?php
require_once(__DIR__ . "/Request.php");
require_once(__DIR__ . "/Response.php");

class Usuarios
{
    public static function crear(PDO $pdo, string $tabla, array $datos): int
    {
        $nombre = Request::texto($datos["nombre"] ?? "");
        $correo = strtolower(Request::texto($datos["correo"] ?? ""));
        $clave = (string)($datos["password"] ?? "");

        if (mb_strlen($nombre) < 3) {
            Response::error("El nombre debe tener al menos 3 caracteres");
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            Response::error("Correo inválido");
        }
        if (strlen($clave) < 6) {
            Response::error("La contraseña debe tener al menos 6 caracteres");
        }

        $st = $pdo->prepare("SELECT 1 FROM administradores WHERE correo = ?
            UNION SELECT 1 FROM profesores WHERE correo = ?
            UNION SELECT 1 FROM estudiantes WHERE correo = ?");
        $st->execute([$correo, $correo, $correo]);
        if ($st->fetch()) {
            Response::error("Ese correo ya está registrado", -1, 409);
        }

        $st = $pdo->prepare("INSERT INTO $tabla (nombre, correo, password) VALUES (?, ?, ?)");
        $st->execute([$nombre, $correo, password_hash($clave, PASSWORD_DEFAULT)]);
        return (int)$pdo->lastInsertId();
    }
}
