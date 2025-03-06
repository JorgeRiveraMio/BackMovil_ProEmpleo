<?php
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
    header("Content-Type: application/json");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(["success" => false, "message" => "Método no permitido."]);
        exit;
    }

    include '../conexion.php'; // Conexión a la base de datos

    // Leer el JSON recibido
    $inputJSON = file_get_contents("php://input");
    $data = json_decode($inputJSON, true);

    // Obtener el ID enviado
    $id = intval($data['id'] ?? 0);

    // Validar que el ID sea válido
    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "ID inválido o no proporcionado."]);
        exit;
    }

    // Verificar si el ID existe en la base de datos
    $stmt = $conexion->prepare("SELECT id FROM agenda WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "El ID no existe."]);
        $stmt->close();
        exit;
    }

    $stmt->close();

    // Eliminar el registro
    $stmt = $conexion->prepare("DELETE FROM agenda WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
        exit;
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registro eliminado correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al eliminar: " . $stmt->error]);
    }

    // Cerrar conexiones
    $stmt->close();
    $conexion->close();
?>
