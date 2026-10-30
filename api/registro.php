<?php
require_once(__DIR__ . "/Connection.php");
require_once(__DIR__ . "/core/Usuarios.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Response::error("Método no permitido", -1, 405);
}

$pdo = new Connection();
Usuarios::crear($pdo, "estudiantes", Request::body());

Response::success(null, "Cuenta creada, ya puedes iniciar sesión", 201);
