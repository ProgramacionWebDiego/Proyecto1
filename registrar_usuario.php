<?php
session_start();
//Si desea ejecutarlo en local profe, cambiale las credencias.
//Autor: Diego, Nefta y pio(Leonel).
$servidor = "localhost";
$usuariodb = "id22217406_tabla_galeria";
$passdb = "Diego2018@";
$db = "id22217406_tabla_galeria";

$usuario = $_POST["user"];
$contrasena = $_POST["pass"];
$email = $_POST["email"];
$plan = $_POST["plan"]; 

$conexion = mysqli_connect($servidor, $usuariodb, $passdb, $db);

if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}

$consulta_usuario = "SELECT * FROM usuarios WHERE nombre = ?";
$stmt_usuario = mysqli_prepare($conexion, $consulta_usuario);
mysqli_stmt_bind_param($stmt_usuario, "s", $usuario);
mysqli_stmt_execute($stmt_usuario);
mysqli_stmt_store_result($stmt_usuario);
$existe_usuario = mysqli_stmt_num_rows($stmt_usuario) > 0;
mysqli_stmt_close($stmt_usuario);

$consulta_email = "SELECT * FROM usuarios WHERE correo_electronico = ?";
$stmt_email = mysqli_prepare($conexion, $consulta_email);
mysqli_stmt_bind_param($stmt_email, "s", $email);
mysqli_stmt_execute($stmt_email);
mysqli_stmt_store_result($stmt_email);
$existe_email = mysqli_stmt_num_rows($stmt_email) > 0;
mysqli_stmt_close($stmt_email);

if ($existe_usuario || $existe_email) {
    $_SESSION['registrar_error'] = 'Error: El usuario o correo electrónico ya existe.';
    header("Location: registrar.php"); 
    exit; 
} else {
    //Aqui esta el hashed de lo que le comente en clase
    $hashed_contrasena = password_hash($contrasena, PASSWORD_BCRYPT);

    $consulta_insertar = "INSERT INTO usuarios (nombre, correo_electronico, contrasena, plan) VALUES (?, ?, ?, ?)";
    $stmt_insertar = mysqli_prepare($conexion, $consulta_insertar);
    mysqli_stmt_bind_param($stmt_insertar, "ssss", $usuario, $email, $hashed_contrasena, $plan);

    if (mysqli_stmt_execute($stmt_insertar)) {
        $_SESSION['registro_exitoso'] = true;
        header("Location: index.php");
        exit;
    } else {
        echo "Error al registrar usuario: " . mysqli_error($conexion);
    }

    mysqli_stmt_close($stmt_insertar);
}

mysqli_close($conexion);
?>
