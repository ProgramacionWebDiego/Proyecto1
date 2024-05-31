<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php"); 
    exit(); 
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if(isset($_POST["user"]) && isset($_POST["pass"]) && isset($_POST["email"])){
        //Si desea ejecutarlo en local profe, cambiale las credencias.
        //Autor: Diego, Nefta y pio(Leonel).
        $servidor = "localhost";
        $usuariodb = "id22217406_tabla_galeria";
        $passdb = "Diego2018@";
        $db = "id22217406_tabla_galeria";

        $usuarioActual = $_SESSION["usuario"]; 

        $nuevoUsuario = $_POST["user"];
        $nuevaContrasena = $_POST["pass"];
        $nuevoCorreo = $_POST["email"];

        if(!empty($nuevoUsuario) && !empty($nuevaContrasena) && !empty($nuevoCorreo)){
            $conexion = mysqli_connect($servidor,$usuariodb,$passdb,$db);
            if (!$conexion) {
                die("Error de conexión: " . mysqli_connect_error());
            }

            $consulta_verificar = "SELECT * FROM usuarios WHERE (nombre=? OR correo_electronico=?) AND nombre != ?";
            $stmt_verificar = mysqli_prepare($conexion, $consulta_verificar);
            mysqli_stmt_bind_param($stmt_verificar, "sss", $nuevoUsuario, $nuevoCorreo, $usuarioActual);
            mysqli_stmt_execute($stmt_verificar);
            mysqli_stmt_store_result($stmt_verificar);
            
            if(mysqli_stmt_num_rows($stmt_verificar) == 0){
                $consulta_actualizar = "UPDATE usuarios SET nombre=?, correo_electronico=?, contrasena=? WHERE nombre=?";
                $stmt = mysqli_prepare($conexion, $consulta_actualizar);
                //Profa aqui esta la encriptacion que le habia comentado en la clase, tambien el login y register los tienen.
                $hashed_password = password_hash($nuevaContrasena, PASSWORD_DEFAULT); 
                mysqli_stmt_bind_param($stmt, "ssss", $nuevoUsuario, $nuevoCorreo, $hashed_password, $usuarioActual);
                mysqli_stmt_execute($stmt);

                if(mysqli_affected_rows($conexion) > 0){
                    $_SESSION['usuario'] = $nuevoUsuario;
                    header("Location: index.php?success=1"); 
                    exit; 
                } else {
                    echo "Error: No se pudo actualizar el usuario.";
                }

                mysqli_stmt_close($stmt);
            } else {
                echo "Error: El nombre de usuario o correo electrónico ya están en uso.";
            }
            
            mysqli_close($conexion);
        } else {
            echo "Error: Debes ingresar un nombre de usuario, correo electrónico y una contraseña.";
        }
    } else {
        echo "Error: Debes enviar los datos del formulario.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/modificar_usuario.css">
</head>
<body>
    <div class="container">
        <h2>Modificar Usuario</h2>
        <?php if(isset($_GET['success']) && $_GET['success'] == 1) { ?>
            <div class="success-message">¡Usuario actualizado con éxito!</div>
        <?php } ?>
        <form action="modificar_usuario.php" method="post">
            <label for="user">Nuevo nombre de usuario:</label>
            <input type="text" name="user" id="user" required>
            
            <label for="email">Nuevo correo electrónico:</label>
            <input type="email" name="email" id="email" required>
            
            <label for="pass">Nueva contraseña:</label>
            <input type="password" name="pass" id="pass" required>
            
            <input type="submit" value="Actualizar">
        </form>
    </div>
</body>
</html>
