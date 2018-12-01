<?php session_start() ?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Registrarse</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
        <?php
        require 'auxiliar.php';

        $valores = PAR_LOGIN;

        try {
            $error = [];
            $pdo = conectar();
            comprobarParametros(PAR_LOGIN);
            $valores = array_map('trim', $_POST);
            $fltu['login'] = comprobarLogins($pdo, $error);
            $fltu['password']=comprobarPassword($error);
            comprobarErrores($error);
            insertarUsuario($pdo, $fltu);
            header('Location: registrar.php');
        } catch (EmptyParamException|ValidationException $e) {
            // No hago nada
        } catch (ParamException $e) {
            header('Location: registrar.php');
        }
        ?>
        <?php mostrarMenu() ?>
        <div class="container">
            <?php mostrarFormularioUsuario($valores, $error, $pdo) ?>
            <?php pie() ?>
        </div>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
