<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../conexion.php';

    if (!$conexion) {
        echo json_encode(["success" => false, "message" => "Error en la conexión a la base de datos: " . mysqli_connect_error()]);
        exit;
    }
    
    // Leer JSON enviado desde Android
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode(["success" => false, "message" => "Error en JSON recibido."]);
        exit;
    }

    // Extraer valores del JSON
    $categoria   = $data['categoria'] ?? '';
    $objetivo    = $data['objetivo'] ?? '';
    $accion      = $data['accion'] ?? '';
    $fecha_ini   = $data['fecha_ini'] ?? '';
    $fecha_ter   = $data['fecha_ter'] ?? '';
    $seguimiento = $data['seguimiento'] ?? '';
    $usuario_id  = $data['usuario_id'] ?? '';

    // Registrar en log para depuración
    // error_log("Datos recibidos: " . json_encode($data));

    // Validar que los campos no estén vacíos
    if (empty($categoria) || empty($objetivo) || empty($accion) || empty($fecha_ini) || empty($fecha_ter) || empty($seguimiento) || empty($usuario_id)) {
        echo json_encode(["success" => false, "message" => "Por favor, complete todos los campos."]);
        exit;
    }

    // Verificar si ya existe el registro
    $stmt = $conexion->prepare("SELECT id FROM proyecto_de_vida WHERE usuario_id = ? AND categoria = ?");
    $stmt->bind_param("is", $usuario_id, $categoria);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        // Actualizar registro existente
        $stmt = $conexion->prepare("UPDATE proyecto_de_vida SET objetivo = ?, accion = ?, fecha_ini = ?, fecha_ter = ?, seguimiento = ? WHERE usuario_id = ? AND categoria = ?");
        $stmt->bind_param("sssssis", $objetivo, $accion, $fecha_ini, $fecha_ter, $seguimiento, $usuario_id, $categoria);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Registro actualizado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
        }
    } else {
        // Insertar nuevo registro
        $stmt = $conexion->prepare("INSERT INTO proyecto_de_vida (usuario_id, categoria, objetivo, accion, fecha_ini, fecha_ter, seguimiento) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $usuario_id, $categoria, $objetivo, $accion, $fecha_ini, $fecha_ter, $seguimiento);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Datos registrados correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error en la consulta: " . $stmt->error]);
        }
    }

    $stmt->close();
    mysqli_close($conexion);
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>
