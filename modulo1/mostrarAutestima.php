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
    $stmt = $conexion->prepare("SELECT autoestima, autorespeto, autoaceptacion, autovaloracion, autoconcepto, autoconocimiento FROM autoestima WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->store_result();

    // ✅ Verificar si hay registros
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($autoestima, $autorespeto, $autoaceptacion, $autovaloracion, $autoconcepto, $autoconocimiento);
        $stmt->fetch();

        echo json_encode([
            "success" => true,
            "data" => [
                "autoestima" => $autoestima,
                "autorespeto" => $autorespeto,
                "autoaceptacion" => $autoaceptacion,
                "autovaloracion" => $autovaloracion,
                "autoconcepto" => $autoconcepto,
                "autoconocimiento" => $autoconocimiento
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
