<?php
session_start();
if(!isset($_SESSION["usuario"])){
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/login.css">
    <title>Registrar</title>
    <style>
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Regístrate</h1>
        <?php
        if(isset($_SESSION['registrar_error'])) {
            echo '<div class="error">' . $_SESSION['registrar_error'] . '</div>';
            unset($_SESSION['registrar_error']); 
        }
        if(isset($_SESSION['registro_exitoso']) && $_SESSION['registro_exitoso']) {
            echo '<div class="success">Registro exitoso. ¡Inicia sesión ahora!</div>';
            unset($_SESSION['registro_exitoso']);
        }
        ?>
        <form action="registrar_usuario.php" method="post">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" required>
            
            <label for="user">Nombre de usuario</label>
            <input type="text" name="user" id="user" required>
            
            <label for="pass">Contraseña</label>
            <input type="password" name="pass" id="pass" required>
            
            <label for="plan">Selecciona un plan:</label>
            <select id="plan" name="plan">
                <option value="basico">Plan Básico</option>
                <option value="normal">Plan Normal</option>
                <option value="premium">Plan Premium</option>
            </select>

            <input type="submit" value="Registrar">
        </form>
        <p>¿Ya tienes una cuenta? <a href="./login.php">Inicia sesión</a></p>
    </div>
</body>
</html>
<?php
}else{
    header("Location: index.php");
}
?>
