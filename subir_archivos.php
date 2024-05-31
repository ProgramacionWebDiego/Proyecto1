<?php
session_start();

//Si desea ejecutarlo en local profe, cambiale las credencias.
//Autor: Diego, Nefta y pio(Leonel).
$servername = "localhost";
$username = "id22217406_tabla_galeria";
$password = "Diego2018@"; 
$dbname = "id22217406_tabla_galeria"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$carpeta = "imagenes/";

$errors = array();

if (!empty($_FILES["archivo"]["name"]) && isset($_POST["titulo"]) && isset($_POST["texto"])) {
    $usuario = $_SESSION["usuario"];
    $titulo = $conn->real_escape_string($_POST["titulo"]);
    $texto = $conn->real_escape_string($_POST["texto"]);

    $sql = "SELECT ID, plan FROM usuarios WHERE nombre = '$usuario'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $usuario_id = $row["ID"];
        $plan = $row["plan"];
        
        $limites = [
            "basico" => 1,     
            "normal" => 5,    
            "premium" => -1   
        ];
        
        $sql = "SELECT COUNT(*) as total_fotos FROM subida WHERE usuario_id = '$usuario_id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $total_fotos = $row["total_fotos"];

        if ($limites[$plan] !== -1 && $total_fotos >= $limites[$plan]) {
            $errors[] = "Error: Has alcanzado el límite de subida de fotos para tu plan ($plan).";
        }
    } else {
        $errors[] = "Error: Usuario no encontrado.";
    }

    $archivo_nombre = $_FILES["archivo"]["name"];
    $archivo_ruta_temporal = $_FILES["archivo"]["tmp_name"];
    $archivo_tipo = $_FILES["archivo"]["type"];
    $archivo_tamano = $_FILES["archivo"]["size"];

    if ($archivo_tipo != "image/jpeg" && $archivo_tipo != "image/png" && $archivo_tipo != "image/gif") {
        $errors[] = "Error: solo se permiten imágenes jpg, png y gif";
    }

    if ($archivo_tamano > 1000000) {
        $errors[] = "Error: El archivo es demasiado grande";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    } else {
        $nombre_archivo_final = $usuario . "_" . time() . "_" . $archivo_nombre;

        if (move_uploaded_file($archivo_ruta_temporal, $carpeta . $nombre_archivo_final)) {
            $sql = "INSERT INTO subida (usuario_id, nombre_archivo, titulo, texto) VALUES ('$usuario_id', '$nombre_archivo_final', '$titulo', '$texto')";
            if ($conn->query($sql) === TRUE) {
                echo "Se ha enviado el archivo al servidor";
                header("Location: upload.php");
                exit; 
            } else {
                echo "Error al registrar la subida en la base de datos: " . $conn->error;
            }
        } else {
            echo "Error al subir el archivo";
        }
    }
} else {
    echo "No se ha enviado ningún archivo o falta título/texto";
}

$conn->close();
?>

