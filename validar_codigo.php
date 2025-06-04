<?php
 ini_set('display_errors', 0); // Evita mostrar errores en la respuesta JSON
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
    header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'conexion.php';

    $email = $_POST['email'] ?? '';
    $codigoIngresado = $_POST['codigo'] ?? '';

    if (empty($email) || empty($codigoIngresado)) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos']);
        exit;
    }

    // Preparar consulta para buscar datos temporales
    $stmt = $conexion->prepare("SELECT nombre, apellido, password, codigo FROM registro_temp WHERE email = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Error en consulta SQL: ' . $conexion->error]);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Aquí es importante almacenar todos los resultados para evitar error "Commands out of sync"
    $stmt->store_result();

    $stmt->bind_result($nombre, $apellido, $passwordHashed, $codigoGuardado);

    if ($stmt->fetch()) {
        // No cerramos $stmt aún, porque puede usarse más abajo

        if ($codigoGuardado == $codigoIngresado) {

            // Insertar datos en tabla usuario
            $insertStmt = $conexion->prepare("INSERT INTO usuario (username, password, nombre, apellido) VALUES (?, ?, ?, ?)");
            if (!$insertStmt) {
                echo json_encode(['success' => false, 'message' => 'Error en consulta de inserción: ' . $conexion->error]);
                $stmt->close();
                $conexion->close();
                exit;
            }

            $insertStmt->bind_param("ssss", $email, $passwordHashed, $nombre, $apellido);
            $execInsert = $insertStmt->execute();

            if ($execInsert) {
                $insertStmt->close();

                // Eliminar el registro temporal
                $delStmt = $conexion->prepare("DELETE FROM registro_temp WHERE email = ?");
                if ($delStmt) {
                    $delStmt->bind_param("s", $email);
                    $delStmt->execute();
                    $delStmt->close();
                }

                echo json_encode(['success' => true, 'message' => 'Usuario registrado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al insertar usuario: ' . $insertStmt->error]);
                $insertStmt->close();
            }

        } else {
            echo json_encode(['success' => false, 'message' => 'Código incorrecto']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontró código para este email']);
    }

    // Cerramos el statement principal una sola vez al final
    $stmt->close();

    $conexion->close();
}
?>
