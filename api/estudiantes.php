<?php
require_once(__DIR__ . "/Connection.php");
require_once(__DIR__ . "/core/Auth.php");

$pdo = new Connection();
$u = Auth::requiere($pdo, "profesor");

$st = $pdo->prepare("SELECT e.id, e.nombre, e.correo, i.fecha_creacion AS fecha_inscripcion,
        t.id AS tutoria_id, t.titulo, t.tema, m.nombre AS materia,
        t.fecha, t.hora_inicio, t.estado AS estado_tutoria
    FROM inscripciones i
    JOIN estudiantes e ON e.id = i.estudiante_id
    JOIN tutorias t ON t.id = i.tutoria_id
    JOIN materias m ON m.id = t.materia_id
    WHERE t.profesor_id = ?
    ORDER BY e.nombre, t.fecha");
$st->execute([$u["id"]]);
Response::success($st->fetchAll());
