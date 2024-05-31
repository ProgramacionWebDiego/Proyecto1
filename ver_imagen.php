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


if (!isset($_SESSION["megusta"])) {
    $_SESSION["megusta"] = array();
}

if (!isset($_SESSION["visitadas"])) {
    $_SESSION["visitadas"] = array();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comentario"])) {
        if (isset($_SESSION["usuario_id"])) {
            $usuario_id = $_SESSION["usuario_id"];
            $subida_id = $id;
            $comentario = $_POST["comentario"];
            $hora_actual = time(); 

            $sql_insertar_comentario = "INSERT INTO comentarios (usuarios_id, subida_id, contenido, fecha) VALUES (?, ?, ?, ?)";
            $stmt_insertar_comentario = $conexion->prepare($sql_insertar_comentario);
            $stmt_insertar_comentario->bind_param("iisi", $usuario_id, $subida_id, $comentario, $hora_actual);

            if ($stmt_insertar_comentario->execute()) {
                header("Location: ver_imagen.php?id=" . $id);
                exit();
            } else {
                echo "Error al registrar comentario: " . $conexion->error;
            }

            $stmt_insertar_comentario->close();
        } else {
            echo "Error: usuario_id no está configurado en la sesión.";
        }
    }

    if (isset($_POST["like_button"])) {
        if (isset($_SESSION["usuario_id"])) {
            $usuario_id = $_SESSION["usuario_id"];

            if (in_array($id, $_SESSION["megusta"])) {
                $key = array_search($id, $_SESSION["megusta"]);
                unset($_SESSION["megusta"][$key]);

                $sql_quitar_like = "DELETE FROM likes WHERE usuario_id = ? AND subida_id = ?";
                $stmt_quitar_like = $conexion->prepare($sql_quitar_like);
                $stmt_quitar_like->bind_param("ii", $usuario_id, $id);
                $stmt_quitar_like->execute();
                $stmt_quitar_like->close();
            } else {
                array_push($_SESSION["megusta"], $id);

                $sql_dar_like = "INSERT INTO likes (usuario_id, subida_id) VALUES (?, ?)";
                $stmt_dar_like = $conexion->prepare($sql_dar_like);
                $stmt_dar_like->bind_param("ii", $usuario_id, $id);
                $stmt_dar_like->execute();
                $stmt_dar_like->close();
            }
            session_write_close(); 
            header("Location: ver_imagen.php?id=" . $id);
            exit();
        }
    }

    if (!in_array($id, $_SESSION["visitadas"])) {
        array_push($_SESSION["visitadas"], $id);

        $sql_incrementar_visitas = "UPDATE subida SET visitas = visitas + 1 WHERE id = ?";
        $stmt_incrementar_visitas = $conexion->prepare($sql_incrementar_visitas);
        $stmt_incrementar_visitas->bind_param("i", $id);
        $stmt_incrementar_visitas->execute();
        $stmt_incrementar_visitas->close();
    }

    $sql = "SELECT s.id, s.usuario_id, s.nombre_archivo, s.titulo, s.texto, s.fecha_subida, s.visitas, u.nombre AS nombre_usuario 
            FROM subida AS s
            INNER JOIN usuarios AS u ON s.usuario_id = u.id
            WHERE s.id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $sql_contar_likes = "SELECT COUNT(*) as total_likes FROM likes WHERE subida_id = ?";
        $stmt_contar_likes = $conexion->prepare($sql_contar_likes);
        $stmt_contar_likes->bind_param("i", $id);
        $stmt_contar_likes->execute();
        $result_contar_likes = $stmt_contar_likes->get_result();
        $total_likes = $result_contar_likes->fetch_assoc()["total_likes"];
        $stmt_contar_likes->close();

        $le_dio_like = in_array($id, $_SESSION["megusta"]);

        echo "<!DOCTYPE html>
              <html lang='es'>
              <head>
                  <meta charset='UTF-8'>
                  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                  <title>Perfil Publico</title>
                  <link rel='stylesheet' href='./css/ver_imagen.css'>
                  <style>
                    .like-button {
                        background-color: " . ($le_dio_like ? "green" : "red") . ";
                    }
                  </style>
              </head>
              <body>
              <div class='container'>
                <h2>Detalle de la Imagen</h2>
                <p>ID: " . $row["id"] . "</p>
                <p>Usuario: <a href='perfil_usuario.php?usuario_id=" . $row["usuario_id"] . "'>" . $row["nombre_usuario"] . "</a></p>
                <p>Título: " . $row["titulo"] . "</p>
                <p>Texto: " . $row["texto"] . "</p>
                <p>Fecha de Subida: " . $row["fecha_subida"] . "</p>
                <p>Visitas: " . $row["visitas"] . "</p>
                <img src='imagenes/" . $row["nombre_archivo"] . "' alt='" . $row["titulo"] . "' class='image'>
                <h3>Comentarios</h3>
                <form action='ver_imagen.php?id=" . $id . "' method='post'>
                    <textarea name='comentario' placeholder='Escribe tu comentario aquí' required></textarea><br>
                    <input type='submit' value='Comentar'>
                </form>";

        echo "<form action='ver_imagen.php?id=" . $id . "' method='post'>
                <input type='hidden' name='like_button' value='1'>
                <button type='submit' class='like-button'>" . ($le_dio_like ? "Quitar Me gusta" : "Me gusta") . " (" . $total_likes . ")</button>
            </form>";

        $sql_comentarios = "SELECT c.Id_c, c.usuarios_id, c.subida_id, c.contenido, c.fecha, u.nombre AS nombre_usuario
                            FROM comentarios AS c
                            INNER JOIN usuarios AS u ON c.usuarios_id = u.id
                            WHERE c.subida_id = ?";
        $stmt_comentarios = $conexion->prepare($sql_comentarios);
        $stmt_comentarios->bind_param("i", $id);
        $stmt_comentarios->execute();
        $result_comentarios = $stmt_comentarios->get_result();

        $comentarios = []; 

        if ($result_comentarios->num_rows > 0) {
            while ($row_comentario = $result_comentarios->fetch_assoc()) {
                $comentarios[] = $row_comentario; 
            }
        }

        $stmt_comentarios->close();

        if (!empty($comentarios)) {
            foreach ($comentarios as $comentario) {
                echo "<div class='comment'>
                        <p><strong>Usuario: " . $comentario["nombre_usuario"] . "</strong> - " . $comentario["fecha"] . "</p>
                        <p>Contenido: " . $comentario["contenido"] . "</p>
                      </div>";
            }
        } else {
            echo "<p>No hay comentarios aún.</p>";
        }

        echo "<p><a href='index.php'>Volver</a></p>";
        echo "</div></body></html>";
    } else {
        echo "Imagen no encontrada.";
    }
} else {
    echo "ID de imagen no especificado.";
}
?>

