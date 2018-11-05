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
        <div class="container">
            <div class="row">
                <?php
                require 'auxiliar.php';

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

                $buscarTitulo = isset($_GET['buscarTitulo'])
                ? trim($_GET['buscarTitulo'])
                : '';
                $st = $pdo->prepare('SELECT p.*, genero
                                       FROM peliculas p
                                       JOIN generos g
                                         ON genero_id = g.id
                                      WHERE position(lower(:titulo) in lower(titulo)) != 0');
                $st->execute([':titulo' => $buscarTitulo]);
                ?>
            </div>
            <div class="row" id="busqueda">
                <div class="col-md-12">
                    <fieldset>
                        <legend>Buscar...</legend>
                        <form action="" method="get" class="form-inline">
                            <div class="form-group">
                                <label for="buscarTitulo">Buscar por título:</label>
                                <input id="buscarTitulo" type="text" name="buscarTitulo"
                                       value="<?= $buscarTitulo ?>"
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
                            <th>Título</th>
                            <th>Año</th>
                            <th>Sinopsis</th>
                            <th>Duración</th>
                            <th>Género</th>
                            <th>Acciones</th>
                        </thead>
                        <tbody>
                            <?php foreach ($st as $fila): ?>
                                <tr>
                                    <td><?= $fila['titulo'] ?></td>
                                    <td><?= $fila['anyo'] ?></td>
                                    <td><?= $fila['sinopsis'] ?></td>
                                    <td><?= $fila['duracion'] ?></td>
                                    <td><?= $fila['genero'] ?></td>
                                    <td>
                                        <a href="confirm_borrado.php?id=<?= $fila['id'] ?>"
                                           class="btn btn-xs btn-danger">
                                            Borrar
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
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
