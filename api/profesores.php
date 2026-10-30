<?php
require_once(__DIR__ . "/Connection.php");
require_once(__DIR__ . "/core/Auth.php");
require_once(__DIR__ . "/core/Usuarios.php");

$pdo = new Connection();
Auth::requiere($pdo, "admin");
$metodo = $_SERVER["REQUEST_METHOD"];

if ($metodo === "GET") {
    Response::success($pdo->query("SELECT p.id, p.nombre, p.correo, p.estado, p.fecha_creacion,
            (SELECT COUNT(*) FROM tutorias t WHERE t.profesor_id = p.id) AS tutorias
        FROM profesores p ORDER BY p.nombre")->fetchAll());
}

if ($metodo === "POST") {
    $id = Usuarios::crear($pdo, "profesores", Request::body());
    Response::success(["id" => $id], "Profesor creado", 201);
}

Response::error("Método no permitido", -1, 405);
