<?php

header("Content-Type: application/json");

require_once __DIR__ . '/../config/connectdb.php';

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'obtener':
        obtenerExamenes();
        break;

    case 'agregar':
        agregarExamen();
        break;

    case 'editar':
        editarExamen();
        break;

    case 'eliminar':
        eliminarExamen();
        break;

    default:
        echo json_encode([
            "success" => false,
            "error" => "Acción no válida"
        ]);
        break;
}

function obtenerExamenes() {
    global $myPDO;

    try {
        $query = $myPDO->prepare("SELECT * FROM exams");
        $query->execute();

        $exams = $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "success" => true,
            "data" => $exams
        ]);

    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "error" => $e->getMessage()
        ]);
    }
}

function agregarExamen() {
    global $myPDO;

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['code'], $data['exam_type'], $data['exam_date'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Campos requeridos: code, exam_type, exam_date']);
        return;
    }

    $code = intval($data['code']);
    $exam_type = trim($data['exam_type']);
    $exam_date = trim($data['exam_date']);
    $status = $data['status'] ?? 'pendiente';

    if (empty($exam_type) || empty($exam_date)) {
        http_response_code(400);
        echo json_encode(['error' => 'Tipo de examen y fecha no pueden estar vacíos']);
        return;
    }

    try {
        $query = $myPDO->prepare('INSERT INTO exams (code, exam_type, exam_date, status) VALUES (?, ?, ?, ?)');
        $query->execute([$code, $exam_type, $exam_date, $status]);

        $id = $myPDO->lastInsertId();
        echo json_encode(['success' => true, 'message' => 'Examen agregado exitosamente', 'id' => $id]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Error al agregar examen: ' . $e->getMessage()]);
    }
}

function editarExamen() {
    global $myPDO;

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['idx'], $data['code'], $data['exam_type'], $data['exam_date'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Campos requeridos: idx, code, exam_type, exam_date']);
        return;
    }

    $idx = intval($data['idx']);
    $code = intval($data['code']);
    $exam_type = trim($data['exam_type']);
    $exam_date = trim($data['exam_date']);
    $status = $data['status'] ?? 'pendiente';

    if (empty($exam_type) || empty($exam_date)) {
        http_response_code(400);
        echo json_encode(['error' => 'Tipo de examen y fecha no pueden estar vacíos']);
        return;
    }

    try {
        $query = $myPDO->prepare('UPDATE exams SET code = ?, exam_type = ?, exam_date = ?, status = ? WHERE idx = ?');
        $query->execute([$code, $exam_type, $exam_date, $status, $idx]);

        echo json_encode(['success' => true, 'message' => 'Examen actualizado exitosamente']);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Error al actualizar examen: ' . $e->getMessage()]);
    }
}

function eliminarExamen() {
    global $myPDO;

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['idx'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Campo requerido: idx']);
        return;
    }

    $idx = intval($data['idx']);

    try {
        $query = $myPDO->prepare('DELETE FROM exams WHERE idx = ?');
        $query->execute([$idx]);

        echo json_encode(['success' => true, 'message' => 'Examen eliminado exitosamente']);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Error al eliminar examen: ' . $e->getMessage()]);
    }
}