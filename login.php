<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/login.css">
    <title>Iniciar sesión</title>
</head>
<body>
    <div class="container">
        <h1>Iniciar sesión</h1>
        <?php
        session_start();
        if(isset($_SESSION['login_error'])) {
            echo '<div class="error">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
            unset($_SESSION['login_error']); 
        }
        ?>
        <form action="validar_login.php" method="post">
            <div class="input-group">
                <label for="user">Nombre de usuario</label>
                <input type="text" name="user" id="user" required>
            </div>
            <div class="input-group">
                <label for="pass">Contraseña</label>
                <input type="password" name="pass" id="pass" required>
            </div>
            <input type="submit" value="Iniciar sesión">
        </form>
        <p>¿No tienes una cuenta? <a href="./registrar.php">Regístrate aquí</a></p>
    </div>
</body>
</html>
