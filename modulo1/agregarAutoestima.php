<?php 
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 header("Content-Type: application/json");
 error_reporting(E_ALL);

 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     include '../conexion.php'; // Conectar a la base de datos

     $data = json_decode(file_get_contents("php://input"), true);

     $autoestima = $data['autoestima'];
     $autorespeto = $data['autorespeto'];
     $autoaceptacion=$data['autoaceptacion'];
     $autovaloracion=$data['autovaloracion'];
     $autoconcepto=$data['autoconcepto'];
     $autoconocimiento = $data['autoconocimiento'];
     $usuario_id = $data['usuario_id'];

     if($autoestima=="" || $autorespeto=="" || $autoaceptacion=="" || $autovaloracion=="" || $autoconcepto=="" || $autoconocimiento==""){
         echo json_encode(["success" => false, "message" => "Por favor, complete todos los campos."]);
         exit;
     }

     $stmt = $con->prepare("SELECT id FROM autoestima WHERE id_usuario = ? ");
    $stmt->bind_param("i", $usuario_id);
     $stmt->execute();
     $stmt->store_result();

     if ($stmt->num_rows > 0) {
         $stmt->close();
         $stmt = $con->prepare("UPDATE autoestima SET autoestima =?, autorespeto =?, autoaceptacion =?, autovaloracion =?, autoconcepto =?, autoconocimiento =? WHERE id_usuario = $usuario_id");
         $stmt->bind_param("iiiiii", $autoestima, $autorespeto, $autoaceptacion, $autovaloracion, $autoconcepto, $autoconocimiento);

         if ($stmt->execute()) {
             echo json_encode(["success" => true, "message" => "Registro actualizado correctamente."]);
         } else {
             echo json_encode(["success" => false, "message" => "Error al actualizar: ". $stmt->error]);
         }
  
    }else{
        $stmt->close();
         $stmt = $con->prepare("INSERT INTO autoestima (autoestima, autorespeto, autoaceptacion, autovaloracion, autoconcepto, autoconocimiento, id_usuario) VALUES (?,?,?,?,?,?,?)");
         $stmt->bind_param("iiiiiii", $autoestima, $autorespeto, $autoaceptacion, $autovaloracion, $autoconcepto, $autoconocimiento, $usuario_id); 

         if ($stmt->execute()) {
             echo json_encode(["success" => true, "message" => "Registro guardado correctamente."]);
         } else {
             echo json_encode(["success" => false, "message" => "Error al guardar: ". $stmt->error]);
         }
    }
         $stmt->close();
        mysqli_close($con);
 } else {
   echo json_encode(["success" => false, "message" => "Método no permitido."]);
 
}
?>