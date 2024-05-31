<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["user"]) && isset($_POST["pass"])) {
        //Si desea ejecutarlo en local profe, cambiale las credencias.
        //Autor: Diego, Nefta y pio(Leonel).
        $servidor = "localhost";
        $usuariodb = "id22217406_tabla_galeria";
        $passdb = "Diego2018@";
        $db = "id22217406_tabla_galeria";

        $usuario = $_POST["user"];
        $contrasena = $_POST["pass"];

        $conexion = mysqli_connect($servidor, $usuariodb, $passdb, $db);
        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }

        $consulta = "SELECT id, nombre, contrasena FROM usuarios WHERE nombre=?";
        $stmt = mysqli_prepare($conexion, $consulta);
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $id, $nombre, $hashed_contrasena);
            mysqli_stmt_fetch($stmt);
            
            if (password_verify($contrasena, $hashed_contrasena)) {
                $_SESSION['usuario'] = $nombre;
                $_SESSION['usuario_id'] = $id;
                header("Location: upload.php");
                exit;
            } else {
                $_SESSION['login_error'] = 'Error: Usuario y/o contraseña son incorrectos.';
                header("Location: login.php");
                exit;
            }
        } else {
            $_SESSION['login_error'] = 'Error: Usuario y/o contraseña son incorrectos.';
            header("Location: login.php");
            exit;
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
    } else {
        $_SESSION['login_error'] = 'Error: Debes enviar los datos del formulario.';
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['login_error'] = 'Error: Debes enviar los datos del formulario.';
    header("Location: login.php");
    exit;
}
?>


