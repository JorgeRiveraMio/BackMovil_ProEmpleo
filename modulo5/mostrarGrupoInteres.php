<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header("Content-Type: application/json");
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include '../conexion.php'; // Conectar a la base de datos

    // ✅ Recibir y validar el ID del usuario
    $usuario_id = intval($_GET['usuario_id'] ?? 0);

    error_log("📥 Datos recibidos en PHP: usuario_id=$usuario_id");

    if ($usuario_id <= 0) {
        echo json_encode(["success" => false, "message" => "Por favor, ingrese un usuario válido."]);
        exit;
    }

    // ✅ Preparar consulta para obtener los datos
    $stmt = $conexion->prepare("SELECT interno, externo, importante_nosotros, importante_ellos FROM grupo_interes WHERE usuario_id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
        exit;
    }

    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->store_result();

    // ✅ Verificar si hay registros
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($interno, $externo, $importante_nosotros, $importante_ellos);
        $stmt->fetch();

        echo json_encode([
            "success" => true,
            "data" => [
                "interno" => $interno,
                "externo" => $externo,
                "importante_nosotros" => $importante_nosotros,
                "importante_ellos" => $importante_ellos              
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
