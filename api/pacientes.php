<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once "../config/connectdb.php";

$method = $_SERVER['REQUEST_METHOD'];
$request = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($method) {
        case 'GET':
            if ($request === 'obtener') {
                obtenerPacientes();
            } else if ($request === 'obtener_uno') {
                obtenerPaciente();
            } else {
                obtenerPacientes();
            }
            break;

        case 'POST':
            agregarPaciente();
            break;

        case 'PUT':
            editarPaciente();
            break;

        case 'DELETE':
            eliminarPaciente();
            break;

        default:
            echo json_encode(['error' => 'Método no permitido']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// Obtener todos los pacientes
function obtenerPacientes() {
    global $myPDO;
    $query = $myPDO->prepare('SELECT * FROM users ORDER BY code DESC');
    $query->execute();
    $pacientes = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $pacientes]);
}

// Obtener un paciente específico
function obtenerPaciente() {
    global $myPDO;
    
    if (!isset($_GET['id'])) {
        echo json_encode(['error' => 'ID no proporcionado']);
        return;
    }
    
    $id = intval($_GET['id']);
    $query = $myPDO->prepare('SELECT * FROM users WHERE code = ?');
    $query->execute([$id]);
    $paciente = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($paciente) {
        echo json_encode(['success' => true, 'data' => $paciente]);
    } else {
        echo json_encode(['error' => 'Paciente no encontrado']);
    }
}

// Agregar paciente
function agregarPaciente() {
    global $myPDO;
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['first_name'], $data['last_name'], $data['strat'], $data['date'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Campos requeridos: first_name, last_name, strat, date']);
        return;
    }
    
    $first_name = trim($data['first_name']);
    $last_name = trim($data['last_name']);
    $strat = intval($data['strat']);
    $date = trim($data['date']);
    
    if (empty($first_name) || empty($last_name) || empty($date)) {
        http_response_code(400);
        echo json_encode(['error' => 'El nombre, apellido y fecha no pueden estar vacíos']);
        return;
    }
    
    if ($strat < 1 || $strat > 7) {
        http_response_code(400);
        echo json_encode(['error' => 'El estrato debe estar entre 1 y 7']);
        return;
    }
    
    try {
        $query = $myPDO->prepare('INSERT INTO users (first_name, last_name, strat, date) VALUES (?, ?, ?, ?)');
        $query->execute([$first_name, $last_name, $strat, $date]);
        
        $id = $myPDO->lastInsertId();
        echo json_encode(['success' => true, 'message' => 'Paciente agregado exitosamente', 'id' => $id]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Error al agregar paciente: ' . $e->getMessage()]);
    }
}

// Editar paciente
function editarPaciente() {
    global $myPDO;
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['code'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID (code) no proporcionado']);
        return;
    }
    
    $code = intval($data['code']);
    $first_name = isset($data['first_name']) ? trim($data['first_name']) : null;
    $last_name = isset($data['last_name']) ? trim($data['last_name']) : null;
    $strat = isset($data['strat']) ? intval($data['strat']) : null;
    $date = isset($data['date']) ? trim($data['date']) : null;
    
    // Obtener datos actuales
    $query = $myPDO->prepare('SELECT * FROM users WHERE code = ?');
    $query->execute([$code]);
    $paciente = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$paciente) {
        http_response_code(404);
        echo json_encode(['error' => 'Paciente no encontrado']);
        return;
    }
    
    // Usar datos actuales si no se proporcionan nuevos
    $first_name = $first_name ?? $paciente['first_name'];
    $last_name = $last_name ?? $paciente['last_name'];
    $strat = $strat ?? $paciente['strat'];
    $date = $date ?? $paciente['date'];
    
    try {
        $query = $myPDO->prepare('UPDATE users SET first_name = ?, last_name = ?, strat = ?, date = ? WHERE code = ?');
        $query->execute([$first_name, $last_name, $strat, $date, $code]);
        
        echo json_encode(['success' => true, 'message' => 'Paciente actualizado exitosamente']);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Error al actualizar paciente: ' . $e->getMessage()]);
    }
}

// Eliminar paciente
function eliminarPaciente() {
    global $myPDO;
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['code'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID (code) no proporcionado']);
        return;
    }
    
    $code = intval($data['code']);
    
    try {
        $query = $myPDO->prepare('DELETE FROM users WHERE code = ?');
        $query->execute([$code]);
        
        if ($query->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Paciente eliminado exitosamente']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Paciente no encontrado']);
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Error al eliminar paciente: ' . $e->getMessage()]);
    }
}
?>
