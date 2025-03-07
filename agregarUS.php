<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    include 'conexion.php'; // Archivo con la conexión a la base de datos

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);

    // Validar que los campos no estén vacíos
    if (!empty($username) && !empty($password) && !empty($nombre) && !empty($apellido)) {
        
        // Verificar si el username ya existe
        $stmt = $conexion->prepare("SELECT id FROM usuario WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // El usuario ya existe
            echo "El nombre de usuario ya está registrado.";
        } else {
            // Insertar nuevo usuario
            $stmt->close();
            $stmt = $conexion->prepare("INSERT INTO usuario (username, password, nombre, apellido) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $password, $nombre, $apellido);

            if ($stmt->execute()) {
                echo "Usuario registrado correctamente.";
            } else {
                echo "Error al registrar el usuario: " . $stmt->error;
            }
        }

        // Cerrar declaración y conexión
        $stmt->close();
    } else {
        echo "Por favor, complete todos los campos.";
    }

    mysqli_close($conexion);
}
?>
