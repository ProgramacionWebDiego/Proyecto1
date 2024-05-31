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

$sql = "SELECT id, nombre_archivo, titulo FROM subida";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi galería de imágenes</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <h2 id="pepe">Bienvenido  <?php echo htmlspecialchars($_SESSION["usuario"]); ?></h2>
    <div id="container">
        <div id="header">
            <div>
                <img id="logo" src="./logo.jpg" alt="Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="upload.php">Subir imagen</a></li>
                    <li><a href="modificar_usuario.php">Modificar usuario</a></li>
                    <li><a href="indexlogin.php">Ver Imágenes Subidas</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>
                </ul>
            </nav>
        </div>
        <div id="gallery">
            <?php
            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    echo "<div>";
                    echo "<img src='imagenes/" . htmlspecialchars($row["nombre_archivo"]) . "' alt='" . htmlspecialchars($row["titulo"]) . "' onclick='verDetalles(" . $row["id"] . ")'>";
                    echo "</div>";
                }
            } else {
                echo "No se encontraron imágenes.";
            }
            ?>
        </div>
    </div>
</body>
<script>
    function verDetalles(id) {
        window.location.href = 'ver_imagen.php?id=' + id;
    }
</script>
</html>

<?php
$conexion->close();
?>
