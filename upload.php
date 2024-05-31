<?php
session_start();
if(isset($_SESSION["usuario"])){
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/upload.css">
    <title>Subir archivos al servidor</title>
</head>
<body>
    <header>
        <h1>Subir Archivos</h1>
    </header>
    <div class="container">
        <h2><?php echo "Bienvenido, ".$_SESSION["usuario"]?></h2>
        <nav>
            <ul>
                <li><a href="index.php">Ver galería</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
        <div class="upload-section">
            <h3>Subir Archivo</h3>
            <?php
            if(isset($_SESSION['upload_error'])) {
                echo '<div class="error">' . $_SESSION['upload_error'] . '</div>';
                unset($_SESSION['upload_error']); 
            }
            ?>
            <form action="subir_archivos.php" enctype="multipart/form-data" method="post">
                <p>Ingresa el título de tu imagen</p>
                <input type="text" name="titulo" required>
                <p>Ingresa el texto de tu imagen</p>
                <textarea name="texto" id="texto" rows="4" cols="50" required></textarea>
                <p>Selecciona un archivo para subirlo al servidor.</p>
                <input type="file" name="archivo" id="archivo" required>
                <p></p>
                <input type="submit" value="Enviar">
            </form>
        </div>
    </div>
</body>
</html>
<?php
}else{
    header("Location: login.php");
}
?>



