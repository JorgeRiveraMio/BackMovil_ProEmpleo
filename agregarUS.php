<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    include 'conexion.php'; // Asegúrate de tener un archivo conexion.php con los detalles de la conexión a tu base de datos

    $username = $_POST['username'];
    $password = $_POST['password'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];

    // Validar entradas
    if (!empty($username) && !empty($password) && !empty($nombre) && !empty($apellido)) {
        // Preparar consulta para evitar inyecciones SQL
        $stmt = $conexion->prepare("INSERT INTO usuario (username, password,nombre,apellido) VALUES (?, ?,?,?)");
        $stmt->bind_param("ssss", $username, $password,  $nombre, $apellido  );

        // Ejecutar consulta
        if ($stmt->execute()) {
            echo "Usuario registrado correctamente";
        } else {
            echo "Error al registrar el usuario: " . $stmt->error;
        }

        // Cerrar declaración y conexión
        $stmt->close();
    } else {
        echo "Por favor, complete todos los campos.";
    }

    mysqli_close($conexion);
}
?>