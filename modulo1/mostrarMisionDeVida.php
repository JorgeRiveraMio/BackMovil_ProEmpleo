<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header("Content-Type: application/json");
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include '../conexion.php'; // Conectar a la base de datos

    // ✅ Recibir datos por GET
    $usuario_id = isset($_GET['usuario_id']) ? $_GET['usuario_id'] : '';

    error_log("📥 Datos recibidos en PHP: id_usuario=$usuario_id");

    // ✅ Validar que no haya datos vacíos
    if (!$usuario_id) {
        echo json_encode(["success" => false, "message" => "Por favor, ingrese un usuario válido."]);
        exit;
    }

    // ✅ Preparar consulta para obtener los datos
    $stmt = $conexion->prepare("SELECT a_que_se_dedica, a_que_se_dedica, breves_ideas, resumen_  FROM mision_vida WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->store_result();

    // ✅ Verificar si hay registros
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($a_que_se_dedica, $que_quiere_alcanzar, $breves_ideas, $resumen_);
        $stmt->fetch();

        echo json_encode([
            "success" => true,
            "data" => [
                "a_que_se_dedica" => $a_que_se_dedica,
                "que_quiere_alcanzar" => $que_quiere_alcanzar,
                "breves_ideas" => $breves_ideas,
                "resumen_" => $resumen_
               
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "No se encontró ningún registro para el usuario proporcionado."]);
    }

    // ✅ Cerrar conexiones
    $stmt->close();
    $conexion->close();
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>
