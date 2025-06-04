<?php
 ini_set('display_errors', 0); // Evita mostrar errores en la respuesta JSON
ini_set('log_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");
require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/PHPMailer-master/src/Exception.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'conexion.php';

    $email = $_POST['username'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($nombre) || empty($apellido) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Correo inválido']);
        exit;
    }

    $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
    $codigo = random_int(1000, 9999);

    $stmt = $conexion->prepare("REPLACE INTO registro_temp (email, nombre, apellido, password, codigo) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Error en la consulta SQL: ' . $conexion->error]);
        exit;
    }
    $stmt->bind_param("ssssi", $email, $nombre, $apellido, $passwordHashed, $codigo);
    $exec = $stmt->execute();
    if (!$exec) {
        echo json_encode(['success' => false, 'message' => 'Error al guardar datos: ' . $stmt->error]);
        exit;
    }
    $stmt->close();

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp-relay.sendinblue.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '';
        $mail->Password   = '';
        $mail->SMTPSecure = '';
        $mail->Port       = ;
        $mail->CharSet = 'UTF-8'; 
        $mail->setFrom('correo', 'Tu App');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Código de verificación';
        $mail->Body    = "Tu código de verificación es: <b>$codigo</b>";

        $mail->send();

        echo json_encode(['success' => true, 'message' => 'El código se envió correctamente']);
    } catch (PHPMailer\PHPMailer\Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al enviar el correo: ' . $e->getMessage()]);
    }

    $conexion->close();
}
?>
