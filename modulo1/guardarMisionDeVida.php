<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_log("Error en el script PHP", 0);

header("Content-Type: application/json; charset=UTF-8"); // Forzar JSON

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Incluir archivo de conexión a la base de datos
    include '../conexion.php';
 

    // Verificar que la conexión existe
    if (!$conexion) {
        echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos"]);
        exit;
    }
    // Leer el JSON recibido
    $inputJSON = file_get_contents("php://input");
    // file_put_contents("debug_log.txt", $inputJSON . PHP_EOL, FILE_APPEND);

    $data = json_decode($inputJSON, true);

    // Validar si el JSON es válido
    if (!$data) {
        echo json_encode(["success" => false, "message" => "Datos JSON inválidos."]);
        exit;
    }


    // Obtener los datos enviados en la solicitud POST
    $a_que_se_dedica = $data['a_que_se_dedica'] ?? '';
    $que_quiere_alcanzar = $data['que_quiere_alcanzar'] ?? '';
    $breves_ideas= $data['breves_ideas'] ?? '';
    $resumen = $data['resumen'] ?? '';
    $usuario_id  = $data['usuario_id'] ?? '';

    // Verificar si los valores no están vacíos
    if (empty($a_que_se_dedica) || empty($que_quiere_alcanzar) || empty($breves_ideas) || empty($resumen)|| empty($usuario_id)) {
        echo json_encode(["success" => false, "message" => "Faltan datos en la solicitud"]);
        exit;
    }


       // Verificar si ya existe el registro
       $stmt = $conexion->prepare("SELECT id FROM mision_vida WHERE usuario_id = ?");
       $stmt->bind_param("i", $usuario_id);
       $stmt->execute();
       $stmt->store_result();


       if ($stmt->num_rows > 0) {
        $stmt->close();
        // Actualizar registro existente
        $stmt = $conexion->prepare("UPDATE mision_vida SET a_que_se_dedica = ?, que_quiere_alcanzar = ?, breves_ideas = ?, resumen_ = ? WHERE usuario_id = ?");
        $stmt->bind_param("ssssi", $a_que_se_dedica, $que_quiere_alcanzar, $breves_ideas, $resumen,  $usuario_id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Registro actualizado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
        }
    } else {
        // Insertar nuevo registro
        $stmt = $conexion->prepare("INSERT INTO mision_vida (usuario_id, a_que_se_dedica, que_quiere_alcanzar, breves_ideas, resumen_) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $usuario_id, $a_que_se_dedica, $que_quiere_alcanzar, $breves_ideas, $resumen);
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
