<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}
//Si desea ejecutarlo en local profe, cambiale las credencias.
//Autor: Diego, Nefta y pio(Leonel).
$servidor = "localhost";
$usuariodb = "id22217406_tabla_galeria";
$passdb = "Diego2018@";
$db = "id22217406_tabla_galeria";
$conexion = new mysqli($servidor, $usuariodb, $passdb, $db);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if (isset($_SESSION["usuario_id"])) {
    $usuario_id = intval($_SESSION["usuario_id"]);
    $usuario = htmlspecialchars($_SESSION["usuario"]);

    echo "<title>Perfil Personal</title>";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imágenes de <?php echo $usuario; ?></title>
    <link rel="stylesheet" href="./css/indexlogin.css"> 
</head>
<body>
    <h2>Imágenes de <?php echo $usuario; ?></h2>
    <div id="gallery">
        <?php
        $sql_imagenes = "SELECT nombre_archivo FROM subida WHERE usuario_id = ?";
        $stmt_imagenes = $conexion->prepare($sql_imagenes);
        $stmt_imagenes->bind_param("i", $usuario_id);
        $stmt_imagenes->execute();
        $result_imagenes = $stmt_imagenes->get_result();

        if ($result_imagenes->num_rows > 0) {
            while ($row_imagen = $result_imagenes->fetch_assoc()) {
                $archivo = $row_imagen['nombre_archivo'];
                echo "<img class='imagen' src='imagenes/" . $archivo . "' alt='Imagen' onclick='mostrarInfo(\"$archivo\")'>";
            }
        } else {
            echo "<p>No hay imágenes asociadas con este usuario.</p>";
        }

        $stmt_imagenes->close();
        ?>
    </div>

    <div id="modal" style="display: none;">
        <div id="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <div id="info"></div>
        </div>
    </div>

    <script>
        function mostrarInfo(archivo) {
            document.getElementById('info').innerHTML = "<img src='imagenes/" + archivo + "' alt='Imagen'>";
            document.getElementById('modal').style.display = "block";
        }

        function cerrarModal() {
            document.getElementById('modal').style.display = "none";
        }
    </script>
</body>
</html>

<?php
} else {
    header("Location: index.php"); 
    exit();
}
$conexion->close();
?>
