<?php
header("Content-Type: application/json");

// Intentar incluir la conexión a la base de datos
$conexion_path = "../conexion.php"; 

if (!file_exists($conexion_path)) {
    die(json_encode(["error" => "No se encontró el archivo de conexión"]));
}

include $conexion_path;

// Verifica si $conexion está definida
if (!isset($conexion)) {
    die(json_encode(["error" => "No se pudo establecer la conexión a la base de datos"]));
}

if (isset($_GET['usuario_id'])) {
    $usuario_id = intval($_GET['usuario_id']); // Asegurar que sea un número

    // Consulta para obtener las actividades del usuario
    $stmt = $conexion->prepare("SELECT id, dia, hora, actividad FROM agenda WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $actividades = array();
    while ($row = $result->fetch_assoc()) {
        $actividades[] = $row;
    }

    echo json_encode($actividades);
} else {
    echo json_encode(["error" => "No se proporcionó usuario_id"]);
}

$stmt->close();
$conexion->close();
?>
