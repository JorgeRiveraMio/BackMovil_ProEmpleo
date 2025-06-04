<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include("conexion.php");

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['user']) || !isset($data['contraseña'])) {
        echo json_encode(array("mensaje" => "Los parámetros 'user' y 'contraseña' son requeridos"));
        exit;
    }

    $username = $data['user'];
    $password = $data['contraseña'];

    // Solo seleccionamos el hash de la contraseña
    $query = "SELECT id, username, password FROM usuario WHERE username = ?";
    if ($stmt = $conexion->prepare($query)) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verificamos la contraseña usando password_verify
            if (password_verify($password, $row['password'])) {
                $usuario = array(
                    "id" => $row['id'],
                    "username" => $row['username']
                );
                echo json_encode(array("mensaje" => "Inicio de sesión exitoso", "usuario" => $usuario));
            } else {
                echo json_encode(array("mensaje" => "Usuario o contraseña incorrectos"));
            }
        } else {
            echo json_encode(array("mensaje" => "Usuario o contraseña incorrectos"));
        }
    } else {
        echo json_encode(array("mensaje" => "Error en la preparación de la consulta de usuario"));
    }

    $conexion->close();
}
?>
