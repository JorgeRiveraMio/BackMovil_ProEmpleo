<?php
    ini_set('display_errors', 0); // Evita mostrar errores en la respuesta JSON
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
    header("Content-Type: application/json");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(["success" => false, "message" => "Método no permitido."]);
        exit;
    }

    // Incluir archivo de conexión a la base de datos
    include '../conexion.php';  // Asegúrate de que este archivo exista y tenga conexión a la BD

    // Leer el JSON recibido
    $inputJSON = file_get_contents("php://input");
    $data = json_decode($inputJSON, true);

    // Validar que el JSON es válido
    if (!$data) {
        echo json_encode(["success" => false, "message" => "Datos JSON inválidos."]);
        exit;
    }

    // Obtener los datos enviados en la solicitud POST
    $interno = $data['interno'] ?? '';
    $externo = $data['externo'] ?? '';
    $importante_nosotros = $data['importante_nosotros'] ?? ''; // Corrección de nombre
    $importante_ellos = $data['importante_ellos'] ?? ''; // Corrección de nombre
    $usuario_id = intval($data['usuario_id'] ?? 0);

    // Validar datos requeridos
    if (empty($interno) || empty($externo) || empty($importante_nosotros) || empty($importante_ellos) || $usuario_id <= 0) {
        echo json_encode(["success" => false, "message" => "Por favor, complete todos los campos."]);
        exit;
    }

    // Verificar si el usuario ya tiene un registro
    $stmt = $conexion->prepare("SELECT id FROM grupo_interes WHERE usuario_id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
        exit;
    }

    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Si existe, actualizar el registro
        $stmt->close();
        $stmt = $conexion->prepare("UPDATE grupo_interes SET interno = ?, externo = ?, importante_nosotros = ?, importante_ellos = ? WHERE usuario_id = ?");
        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
            exit;
        }

        $stmt->bind_param("ssssi", $interno, $externo, $importante_nosotros, $importante_ellos, $usuario_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Registro actualizado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
        }
    } else {
        // Si no existe, insertar un nuevo registro
        $stmt->close();
        $stmt = $conexion->prepare("INSERT INTO grupo_interes (interno, externo, importante_nosotros, importante_ellos, usuario_id) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conexion->error]);
            exit;
        }

        $stmt->bind_param("ssssi", $interno, $externo, $importante_nosotros, $importante_ellos, $usuario_id);

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
