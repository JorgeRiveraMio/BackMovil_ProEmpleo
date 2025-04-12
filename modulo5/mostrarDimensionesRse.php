<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header("Content-Type: application/json"); // Asegura que PHP devuelva JSON
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include '../conexion.php'; // Conectar a la base de datos

    // ✅ Recibir datos por GET (desde la URL)
    $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';  
    $usuario_id = isset($_GET['usuario_id']) ? $_GET['usuario_id'] : '';

    error_log("📥 Datos recibidos en PHP: categoria=$categoria, id_usuario=$usuario_id");

    // Validar que no haya datos vacíos
    if (!$categoria || !$usuario_id) {
        echo json_encode(["success" => false, "message" => "Por favor, complete todos los campos."]);
        exit;
    }

    // ✅ Verificar si ya existe un registro con el mismo id_usuario y categoria
    $stmt = $conexion->prepare("SELECT interno, externo FROM dimensiones_rse WHERE usuario_id = ? AND categoria = ?");
    $stmt->bind_param("is", $usuario_id, $categoria);
    $stmt->execute();
    $stmt->store_result();

    // Si existe el registro, obtener los datos
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($interno, $externo);
        $stmt->fetch();
        echo json_encode(["success" => true,
        "data" => [
         "interno" => $interno, "externo" => $externo]]

        );
    } else {
        echo json_encode(["success" => false, "message" => "No se encontró ningún registro con esos datos."]);
    }

    $stmt->close(); // Cerrar statement
    mysqli_close($conexion); // Cerrar conexión
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>
