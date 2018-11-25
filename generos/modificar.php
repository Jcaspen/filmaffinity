<?php session_start() ?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Modificar un Género</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
        <?php
        require '../comunes/auxiliar.php';

        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensaje'] = 'Debe iniciar sesión para modificar géneros.';
            header('Location: ../peliculas/generos.php');
        }

        try {
            $error = [];
            $id = comprobarId();
            $pdo = conectar();
            $fila = buscarGenero($pdo, $id);
            comprobarParametros(PAR_GENEROS);
            $valores = array_map('trim', $_POST);
            $flt['genero'] = comprobarGenero($pdo, $error);
            comprobarErrores($error);
            modificarGenero($pdo, $flt, $id);
            $_SESSION['mensaje'] = 'Género modificado correctamente.';
            header('Location: index.php');
        } catch (EmptyParamException|ValidationException $e) {
            // No hago nada
        } catch (ParamException $e) {
            header('Location: index.php');
        }
        ?>
        <?php mostrarMenu() ?>
        <div class="container">
            <?php mostrarFormularioGenero($fila, $error, $pdo, 'Modificar', 'un género') ?>
            <?php pie() ?>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
