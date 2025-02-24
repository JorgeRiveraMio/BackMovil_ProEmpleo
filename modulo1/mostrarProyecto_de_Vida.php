<?php 
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 header('Content-Type: application/json');
 error_reporting(E_ALL);

 if($_SERVER['REQUEST_METHOD'] == 'GET') {
    include '../conexion.php'; // Conectar a la base de datos

    $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
    $usuario_id = isset($_GET['usuario_id']) ? $_GET['usuario_id'] : '';

    error_log("categoria: $categoria, usuario_id: $usuario_id");

    if (!$categoria ||!$usuario_id) {
        echo json_encode(["success" => false, "message" => "Por favor, ingrese una categoría y un usuario válidos."]);
        exit;
    }

    $stmt =$conexion -> prepare ("SELECT objetivo,accion,fecha_ini ,fecha_ter,seguimiento FROM proyecto_de_vida WHERE usuario_id = ? AND categoria = ?");
    $stmt -> bind_param("is", $usuario_id, $categoria);
    $stmt -> execute();
    $stmt -> store_result();

    if ($stmt->num_rows > 0) {

        $stmt -> bind_result($objetivo, $accion, $fecha_ini, $fecha_ter, $seguimiento);
        $stmt -> fetch();

        echo json_encode(["success" => true, "objetivo" => $objetivo, "accion" => $accion, "fecha_ini" => $fecha_ini, "fecha_ter" => $fecha_ter, "seguimiento" => $seguimiento]);
      

    }else{
        echo json_encode(["success" => false, "message" => "No se encontró ningún registro con esos datos."]);
        
    }
    $stmt->close();

    $conexion->close();

    }else{
        echo json_encode(["success" => false, "message" => "Método no permitido."]);
        exit;
    }
?>