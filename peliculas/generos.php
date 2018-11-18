<?php session_start() ?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bases de datos</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <style media="screen">
            #busqueda { margin-top: 1em; }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-default navbar-inverse">
            <div class="container">
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    <ul class="nav navbar-nav">
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                          Menú<b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                          <li><a href="index.php" class="btn">Películas</a></li>
                          <li><a href="generos.php" class="btn">Géneros</a></li>
                        </ul>
                      </li>
                    </ul>
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">FilmAffinity</a>
                </div>
                <div class="navbar-text navbar-right">
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <?= $_SESSION['usuario'] ?>
                        <a href="logout.php" class="btn btn-success">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-success">Login</a>
                    <?php endif ?>
                </div>
            </div>
        </nav>
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
                require '../comunes/auxiliar.php';

                $pdo = conectar();

                if (isset($_POST['id'])) {
                    $id = $_POST['id'];
                    $pdo->beginTransaction();
                    $pdo->exec('LOCK TABLE peliculas IN SHARE MODE');
                    if (!buscarPelicula($pdo, $id)) { ?>
                        <h3>La película no existe.</h3>
                        <?php
                    } else {
                        $st = $pdo->prepare('DELETE FROM peliculas WHERE id = :id');
                        $st->execute([':id' => $id]); ?>
                        <h3>Película borrada correctamente.</h3>
                        <?php
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
                                <label for="buscarGenero">Buscar por género: </label>
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
                            <?php foreach ($st as $fila): ?>
                                <tr>
                                    <td><?= h($fila['genero']) ?></td>
                                    <td>
                                        <a href="confirm_borrado.php?id=<?= $fila['id'] ?>"
                                           class="btn btn-xs btn-danger">
                                            Borrar
                                        </a>
                                        <a href="modificar.php?id=<?= $fila['id'] ?>"
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
                    <a href="insertar.php" class="btn btn-info">Insertar una nueva película</a>
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
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
