<?php
// Configuración de errores y buffer
ini_set('display_errors', 0); // Evita mostrar errores en la respuesta JSON
ini_set('log_errors', 1);
// ini_set('error_log', 'error_log.txt');

error_reporting(E_ALL);
header("Content-Type: application/json");

// Verificar si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
    exit;
}

// Conectar a la base de datos
include '../conexion.php';

// Leer el JSON recibido
$inputJSON = file_get_contents("php://input");
$data = json_decode($inputJSON, true);

// Validar si el JSON es válido
if (!$data) {
    echo json_encode(["success" => false, "message" => "Datos JSON inválidos."]);
    exit;
}

// Obtener datos del JSON
$autoestima = trim($data['autoestima'] ?? '');
$autorespeto = trim($data['autorespeto'] ?? '');
$autoaceptacion = trim($data['autoaceptacion'] ?? '');
$autovaloracion = trim($data['autovaloracion'] ?? '');
$autoconcepto = trim($data['autoconcepto'] ?? '');
$autoconocimiento = trim($data['autoconocimiento'] ?? '');
$usuario_id = intval($data['usuario_id'] ?? 0);

// Validar datos requeridos
if (empty($autoestima) || empty($autorespeto) || empty($autoaceptacion) || 
    empty($autovaloracion) || empty($autoconcepto) || empty($autoconocimiento) || 
    $usuario_id <= 0) {
    
    echo json_encode(["success" => false, "message" => "Por favor, complete todos los campos."]);
    exit;
}

// Verificar si el usuario ya tiene un registro
$stmt = $conexion->prepare("SELECT id FROM autoestima WHERE usuario_id  = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
    exit;
}

$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Si existe, actualizar el registro
    $stmt->close();
    $stmt = $conexion->prepare("UPDATE autoestima SET autoestima = ?, autorespeto = ?, autoaceptacion = ?, autovaloracion = ?, autoconcepto = ?, autoconocimiento = ? WHERE usuario_id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
        exit;
    }

    $stmt->bind_param("ssssssi", $autoestima, $autorespeto, $autoaceptacion, $autovaloracion, $autoconcepto, $autoconocimiento, $usuario_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registro actualizado correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
    }
} else {
    // Si no existe, insertar un nuevo registro
    $stmt->close();
    $stmt = $conexion->prepare("INSERT INTO autoestima (autoestima, autorespeto, autoaceptacion, autovaloracion, autoconcepto, autoconocimiento, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
        exit;
    }

    $stmt->bind_param("ssssssi", $autoestima, $autorespeto, $autoaceptacion, $autovaloracion, $autoconcepto, $autoconocimiento, $usuario_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registro guardado correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar: " . $stmt->error]);
    }
}

// Cerrar conexiones
$stmt->close();
$conexion->close();
?>
