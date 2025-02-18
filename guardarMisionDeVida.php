<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json; charset=UTF-8"); // Forzar JSON

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Incluir archivo de conexión a la base de datos
    include 'conexion.php'; 

    // Verificar que la conexión existe
    if (!$conexion) {
        echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos"]);
        exit;
    }

    // Obtener los datos enviados en la solicitud POST
    $a_que_se_dedica = $_POST['a_que_se_dedica'] ?? '';
    $que_quiere_alcanzar = $_POST['que_quiere_alcanzar'] ?? '';
    $breves_ideas= $_POST['breves_ideas'] ?? '';
    $resumen = $_POST['resumen'] ?? '';

    // Verificar si los valores no están vacíos
    if (empty($a_que_se_dedica) || empty($que_quiere_alcanzar) || empty($breves_ideas) || empty($resumen)) {
        echo json_encode(["success" => false, "message" => "Faltan datos en la solicitud"]);
        exit;
    }

    // Verificar si se proporciona un ID en la solicitud (para actualización)
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $query = "UPDATE mision_vida SET a_que_se_dedica=?, que_quiere_alcanzar=?, breves_ideas=?, resumen=? WHERE id=?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ssssi", $a_que_se_dedica, $que_quiere_alcanzar, $breves_ideas, $resumen, $id);
    } else {
        $query = "INSERT INTO mision_vida (a_que_se_dedica, que_quiere_alcanzar, breves_ideas, resumen) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ssss", $a_que_se_dedica, $que_quiere_alcanzar, $breves_ideas, $resumen);
    }

    // Ejecutar la consulta
    if ($stmt->execute()) {
        $response = ["success" => true, "message" => "Datos guardados correctamente"];
    } else {
        $response = ["success" => false, "message" => "Error en la consulta: " . $stmt->error];
    }

    // Cerrar la conexión
    $stmt->close();
    $conexion->close();

    // Enviar respuesta JSON
    echo json_encode($response);
}
?>
