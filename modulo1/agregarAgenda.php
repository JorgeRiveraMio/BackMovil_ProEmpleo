<?php
    ini_set('display_errors', 0); // Evita mostrar errores en la respuesta JSON
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
    header("Content-Type: application/json");
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        echo json_encode(["success" => false, "message" => "Método no permitido."]);
        exit;
}
    // Incluir archivo de conexión a la base de datos
    include '../conexion.php';  // Asegúrate de tener un archivo 'conexion.php' que se conecte a la base de datos

    // Leer el JSON recibido
    $inputJSON = file_get_contents("php://input");
    $data = json_decode($inputJSON, true);
    // Obtener los datos enviados en la solicitud POST
    $dia = $data['dia'];
    $hora = $data['hora'];
    $actividad = $data['actividad'];
    $usuario_id = intval($data['usuario_id'] ?? 0);
    // Validar datos requeridos
        if (empty($dia) || empty($hora) || empty($actividad) || 
            $usuario_id <= 0) {

        echo json_encode(["success" => false, "message" => "Por favor, complete todos los campos."]);
        exit;
    }   
    // Verificar si el usuario ya tiene un registro
        $stmt = $conexion->prepare("SELECT id FROM agenda WHERE usuario_id  = ? AND dia=?");
        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
            exit;
        }

        $stmt->bind_param("is", $usuario_id, $dia);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Si existe, actualizar el registro
            $stmt->close();
            $stmt = $conexion->prepare("UPDATE agenda SET dia = ?, hora = ?, actividad = ? WHERE usuario_id = ?");
            if (!$stmt) {
                echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
                exit;
            }
        
            $stmt->bind_param("sssi", $dia, $hora, $actividad, $usuario_id);
        
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Registro actualizado correctamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
            }
        } else {
            // Si no existe, insertar un nuevo registro
            $stmt->close();
            $stmt = $conexion->prepare("INSERT INTO agenda (dia, hora, actividad, usuario_id) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
                exit;
            }
        
            $stmt->bind_param("sssi", $dia, $hora, $actividad,  $usuario_id);
        
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
