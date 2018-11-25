<?php session_start() ?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Géneros</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <style media="screen">
            #busqueda { margin-top: 1em; }
        </style>
    </head>
    <body>
        <?php require '../comunes/auxiliar.php';
        mostrarMenu() ?>
        <div class="container">
            <br>
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="row">
                    <div class="alert alert-success" role="alert">
                        <?= $_SESSION['mensaje'] ?>
                    </div>
                </div>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif ?>
            <div class="row">
                <?php

                $pdo = conectar();
                if (isset($_POST['id'])) {
                    $id = $_POST['id'];
                    $pdo->beginTransaction();
                    $pdo->exec('LOCK TABLE generos IN SHARE MODE');
                    if (!buscarGenero($pdo, $id)) { ?>
                        <h3>El género no existe.</h3>
                        <?php
                    } else {
                        if (buscarPeliculasPorGenero($pdo, $id)) { ?>
                            <h3>Hay películas de ese género.</h3>
                            <?php
                        } else {
                            $st = $pdo->prepare('DELETE FROM generos WHERE id = :id');
                            if (!$st->execute([':id' => $id])) {
                                print_r($st->errorInfo());
                            } else { ?>
                                <h3>Género borrado correctamente.</h3>
                                <?php
                            }
                        }
                    }
                    $pdo->commit();
                }

                $buscarGenero = isset($_GET['buscarGenero'])
                ? trim($_GET['buscarGenero'])
                : '';
                $st = $pdo->prepare('SELECT *
                                       FROM generos
                                      WHERE position(lower(:generoBuscado) in lower(genero)) != 0
                                   ORDER BY id');
                $st->execute([':generoBuscado' => $buscarGenero]);
                ?>
            </div>
            <div class="row" id="busqueda">
                <div class="col-md-12">
                    <fieldset>
                        <legend>Buscar...</legend>
                        <form action="" method="get" class="form-inline">
                            <div class="form-group">
                                <label for="buscarGenero"></label>
                                <input id="buscarGenero" type="text" name="buscarGenero"
                                       value="<?= $buscarGenero ?>"
                                       class="form-control">
                            </div>
                            <input type="submit" value="Buscar" class="btn btn-primary">
                        </form>
                    </fieldset>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <th>Géneros</th>
                            <th>Acciones</th>

                        </thead>
                        <tbody>
                            <?php
                            foreach ($st as $fila): ?>
                                <tr>
                                    <td><?= h($fila['genero']) ?></td>
                                    <td>
                                        <a href="../generos/confirm_borrado.php?id=<?= $fila['id']?>"
                                           class="btn btn-xs btn-danger">
                                            Borrar
                                        </a>
                                        <a href="../generos/modificar.php?id=<?= $fila['id'] ?>"
                                           class="btn btn-xs btn-info">
                                            Modificar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="text-center">
                    <a href="insertar.php" class="btn btn-info">Insertar un nuevo género</a>
                </div>
            </div>
            <?php if (!isset($_COOKIE['acepta'])): ?>
                <nav class="navbar navbar-fixed-bottom navbar-inverse">
                    <div class="container">
                        <div class="navbar-text navbar-right">
                            Tienes que aceptar las políticas de cookies.
                            <a href="crear_cookie.php" class="btn btn-success">Aceptar cookies</a>
                        </div>
                    </div>
                </nav>
            <?php endif ?>
            <?php pie() ?>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
