<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Insertar una nueva película</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
        <?php
        require './auxiliar.php';

        const PAR = [
            'titulo' => '',
            'anyo' => '',
            'sinopsis' => '',
            'duracion' => '',
            'genero_id' => '',
        ];

        extract(PAR);

        if (isset($_POST['titulo'], $_POST['anyo'], $_POST['sinopsis'],
                  $_POST['duracion'], $_POST['genero_id'])) {
            extract(array_map('trim', $_POST), EXTR_IF_EXISTS);
            // Filtrado de la entrada
            $pdo = conectar();
            $st = $pdo->prepare('INSERT INTO peliculas (titulo, anyo, sinopsis, duracion, genero_id)
                                 VALUES (:titulo, :anyo, :sinopsis, :duracion, :genero_id)');
            $st->execute([
                ':titulo' => $titulo,
                ':anyo' => $anyo,
                ':sinopsis' => $sinopsis,
                ':duracion' => $duracion,
                ':genero_id' => $genero_id,
            ]);
            header('Location: index.php');
        }
        ?>
        <br>
        <div class="container">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Insertar una nueva película...</h3>
                </div>
                <div class="panel-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="titulo">Título</label>
                            <input id="titulo" type="text" name="titulo"
                                   class="form-control" value="<?= $titulo ?>">
                        </div>
                        <div class="form-group">
                            <label for="anyo">Año</label>
                            <input id="anyo" type="text" name="anyo"
                                   class="form-control" value="<?= $anyo ?>">
                        </div>
                        <div class="form-group">
                            <label for="sinopsis">Sinopsis</label>
                            <textarea id="sinopsis"
                                      name="sinopsis"
                                      rows="8"
                                      cols="80"
                                      class="form-control"><?= $sinopsis ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="duracion">Duración</label>
                            <input id="duracion" type="text" name="duracion"
                                   class="form-control"
                                   value="<?= $duracion ?>">
                        </div>
                        <div class="form-group">
                            <label for="genero_id">Género</label>
                            <input id="genero_id" type="text" name="genero_id"
                                   class="form-control"
                                   value="<?= $genero_id?>">
                        </div>
                        <input type="submit" value="Insertar"
                               class="btn btn-success">
                        <a href="index.php" class="btn btn-info">Volver</a>
                    </form>
                </div>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
