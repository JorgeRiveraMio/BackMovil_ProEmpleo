<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
    exit;
}

include '../conexion.php';

$inputJSON = file_get_contents("php://input");
$data = json_decode($inputJSON, true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Datos JSON inválidos."]);
    exit;
}

$interno = $data['interno'] ?? '';
$externo = $data['externo'] ?? '';
$categoria = $data['categoria'] ?? '';
$usuario_id = intval($data['usuario_id'] ?? 0);

if (empty($interno) || empty($externo) || empty($categoria) || $usuario_id <= 0) {
    echo json_encode(["success" => false, "message" => "Por favor, complete todos los campos."]);
    exit;
}

// Verificar si ya existe
$stmt = $conexion->prepare("SELECT id FROM dimensiones_rse WHERE usuario_id = ? AND categoria = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
    exit;
}
$stmt->bind_param("is", $usuario_id, $categoria);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $stmt = $conexion->prepare("UPDATE dimensiones_rse SET interno = ?, externo = ? WHERE usuario_id = ? AND categoria = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
        exit;
    }

    $stmt->bind_param("ssis", $interno, $externo, $usuario_id, $categoria);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registro actualizado correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
    }
} else {
    $stmt->close();
    $stmt = $conexion->prepare("INSERT INTO dimensiones_rse (interno, externo, categoria, usuario_id) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
        exit;
    }

    $stmt->bind_param("sssi", $interno, $externo, $categoria, $usuario_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registro guardado correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar: " . $stmt->error]);
    }
}

$stmt->close();
$conexion->close();
?>
