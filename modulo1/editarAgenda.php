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

    // Obtener los datos enviados
    $id = intval($data['id'] ?? 0);
    $dia = $data['dia'] ?? '';
    $hora = $data['hora'] ?? '';
    $actividad = $data['actividad'] ?? '';

    // Validar que todos los datos estén presentes
    if ($id <= 0 || empty($dia) || empty($hora) || empty($actividad)) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
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

    // Cerrar la primera consulta
    $stmt->close();

    // Realizar la actualización
    $stmt = $conexion->prepare("UPDATE agenda SET dia = ?, hora = ?, actividad = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
        exit;
    }

    $stmt->bind_param("sssi", $dia, $hora, $actividad, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registro actualizado correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
    }

    // Cerrar conexiones
    $stmt->close();
    $conexion->close();
?>
