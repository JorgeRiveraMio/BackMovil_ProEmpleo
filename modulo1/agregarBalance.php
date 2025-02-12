<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header("Content-Type: application/json"); // Asegura que PHP devuelva JSON
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../conexion.php'; // Conectar a la base de datos

    // ✅ Leer el JSON enviado desde Android
    $data = json_decode(file_get_contents("php://input"), true);

    // ✅ Verificar si los datos se recibieron
    $categoria = isset($data['categoria']) ? $data['categoria'] : '';
    $cambio = isset($data['cambio']) ? $data['cambio'] : '';
    $explicacion = isset($data['explicacion']) ? $data['explicacion'] : '';
    $usuario_id = isset($data['usuario_id']) ? $data['usuario_id'] : '';

    error_log("📥 Datos recibidos en PHP: categoria=$categoria, cambio=$cambio, explicacion=$explicacion, id_usuario=$usuario_id");

    // Validar que no haya datos vacíos
    if (!$categoria || !$cambio || !$explicacion || !$usuario_id) {
        echo json_encode(["success" => false, "message" => "Por favor, complete todos los campos."]);
        exit;
    }

    // Preparar consulta
    $stmt = $conexion->prepare("INSERT INTO balance (categoria, cambio, explicacion, id_usuario) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssi", $categoria, $cambio, $explicacion, $usuario_id);

        // Ejecutar consulta
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Datos registrados correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al registrar: " . $stmt->error]);
        }

        $stmt->close(); // Cerrar statement
    } else {
        echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta."]);
    }

    mysqli_close($conexion); // Cerrar conexión
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>
