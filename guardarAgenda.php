<?php
// Verificar si la solicitud es POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // Incluir archivo de conexión a la base de datos
    include 'conexion.php';  // Asegúrate de tener un archivo 'conexion.php' que se conecte a la base de datos

    // Obtener los datos enviados en la solicitud POST
    $dia = $_POST['dia'];
    $hora = $_POST['hora'];
    $actividad = $_POST['actividad'];

    // Verificar si se proporciona un ID en la solicitud (para actualización)
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);  // Convertir el ID a entero para evitar inyecciones SQL
        // Realizar una actualización en lugar de una inserción
        $query = "UPDATE agenda SET dia='$dia', hora='$hora', actividad='$actividad' WHERE id=$id";
    } else {
        // Si no se proporciona un ID, realizar una inserción normal
        $query = "INSERT INTO agenda (dia, hora, actividad) VALUES ('$dia', '$hora', '$actividad')";
    }

    // Ejecutar la consulta
    $result = $conexion->query($query);

    // Verificar si la consulta fue exitosa
    if ($result == TRUE) {
        echo "Datos guardados correctamente";  // Mensaje de éxito
    } else {
        echo "Error al guardar los datos: " . $conexion->error;  // Mensaje de error
    }

    // Cerrar la conexión a la base de datos
    $conexion->close();
}
?>
