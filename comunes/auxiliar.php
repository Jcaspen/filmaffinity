<?php

const PAR = [
    'titulo' => '',
    'anyo' => '',
    'sinopsis' => '',
    'duracion' => '',
    'genero_id' => '',
];

const PAR_GENEROS = [
    'genero' => '',
];

const PAR_LOGIN = ['login' => '', 'password' => ''];

class ValidationException extends Exception
{
}

class ParamException extends Exception
{
}

class EmptyParamException extends Exception
{
}

function conectar()
{
    return new PDO('pgsql:host=localhost;dbname=fa', 'fa', 'fa');
}

function buscarPelicula($pdo, $id)
{
    $st = $pdo->prepare('SELECT * FROM peliculas WHERE id = :id');
    $st->execute([':id' => $id]);
    return $st->fetch();
}

function buscarGenero($pdo, $id)
{
    $st = $pdo->prepare('SELECT * FROM generos WHERE id = :id');
    $st->execute([':id' => $id]);
    return $st->fetch();
}

function buscarGeneroPorGenero($pdo, $genero)
{
    $st = $pdo->prepare('SELECT * FROM generos WHERE genero = :genero');
    $st->execute([':genero' => $genero]);
    return $st->fetch();
}

function buscarUsuario($pdo, $id)
{
    $st = $pdo->prepare('SELECT * FROM usuarios WHERE id = :id');
    $st->execute([':id' => $id]);
    return $st->fetch();
}

function comprobarTitulo(&$error)
{
    $fltTitulo = trim(filter_input(INPUT_POST, 'titulo'));
    if ($fltTitulo === '') {
        $error['titulo'] = 'El título es obligatorio.';
    } elseif (mb_strlen($fltTitulo) > 255) {
        $error['titulo'] = "El título es demasiado largo.";
    }
    return $fltTitulo;
}

function comprobarAnyo(&$error)
{
    $fltAnyo = filter_input(INPUT_POST, 'anyo', FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 0,
            'max_range' => 9999,
        ],
    ]);
    if ($fltAnyo === false) {
        $error['anyo'] = "El año no es correcto.";
    }
    return $fltAnyo;
}

function comprobarDuracion(&$error)
{
    $fltDuracion = trim(filter_input(INPUT_POST, 'duracion'));
    if ($fltDuracion !== '') {
        $fltDuracion = filter_input(INPUT_POST, 'duracion', FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 0,
                'max_range' => 32767,
            ],
        ]);
        if ($fltDuracion === false) {
            $error['duracion'] = 'La duración no es correcta.';
        }
    } else {
        $fltDuracion = null;
    }
    return $fltDuracion;
}

function comprobarGeneroId($pdo, &$error)
{
    $fltGeneroId = filter_input(INPUT_POST, 'genero_id', FILTER_VALIDATE_INT);
    if ($fltGeneroId !== false) {
        // Buscar en la base de datos si existe ese género
        $st = $pdo->prepare('SELECT * FROM generos WHERE id = :id');
        $st->execute([':id' => $fltGeneroId]);
        if (!$st->fetch()) {
            $error['genero_id'] = 'No existe ese género.';
        }
    } else {
        $error['genero_id'] = 'El género no es correcto.';
    }
    return $fltGeneroId;
}

function comprobarGenero($pdo, &$error)
{
    $fltGenero = trim(filter_input(INPUT_POST, 'genero'));
    if ($fltGenero === '') {
        $error['genero'] = 'El género es obligatorio.';
    } elseif (mb_strlen($fltGenero) > 255) {
        $error['genero'] = "El género es demasiado largo.";
    }
    if (buscarGeneroPorGenero($pdo, $fltGenero)) {
        $error['genero'] = 'El género ya existe.';
    }
    return $fltGenero;
}

function comprobarAdmin()
{

    if (!isset($_SESSION['usuario'])) {
        $_SESSION['mensaje'] = 'Debe iniciar sesión para poder borrar películas';
        header('Location: index.php');
    } elseif ($_SESSION['usuario'] != 'admin') {
        $_SESSION['mensaje'] = 'Debe ser administrador para poder borrar películas';
        header('Location: index.php');
    }
}

function buscarPeliculasPorGenero($pdo, $genero_id)
{
    $st = $pdo->prepare('SELECT * FROM peliculas WHERE genero_id = :genero_id');
    $st->execute([':genero_id' => $genero_id]);
    return $st->fetchAll();
}



function insertarPelicula($pdo, $fila)
{
    $st = $pdo->prepare('INSERT INTO peliculas (titulo, anyo, sinopsis, duracion, genero_id)
                         VALUES (:titulo, :anyo, :sinopsis, :duracion, :genero_id)');
    $st->execute($fila);
}

function insertarGenero($pdo, $fila)
{
    $st = $pdo->prepare('INSERT INTO generos (genero)
                         VALUES (:genero)');
    $st->execute($fila);
}

function insertarUsuario($pdo, $fila)
{

    $st = $pdo->prepare('INSERT INTO usuarios (login, password)
                         VALUES (:login,crypt(:password, gen_salt(\'bf\', 10)))');
    $st->execute($fila);
}

function modificarPelicula($pdo, $fila, $id)
{
    $st = $pdo->prepare('UPDATE peliculas
                            SET titulo = :titulo
                              , anyo = :anyo
                              , sinopsis = :sinopsis
                              , duracion = :duracion
                              , genero_id = :genero_id
                          WHERE id = :id');
    $st->execute($fila + ['id' => $id]);
}

function modificarGenero($pdo, $fila, $id)
{
    $st = $pdo->prepare('UPDATE generos
                            SET genero = :genero
                          WHERE id = :id');
    $st->execute($fila + ['id' => $id]);
}


function comprobarParametros($par)
{
    if (empty($_POST)) {
        throw new EmptyParamException();
    }
    if (!empty(array_diff_key($par, $_POST)) ||
        !empty(array_diff_key($_POST, $par))) {
        throw new ParamException();
    }
}

function comprobarErrores($error)
{
    if (!empty($error)) {
        throw new ValidationException();
    }
}

function hasError($key, $error)
{
    return array_key_exists($key, $error) ? 'has-error' : '';
}

function mensajeError($key, $error)
{
    if (isset($error[$key])) { ?>
        <small class="help-block"><?= $error[$key] ?></small>
        <script language='JavaScript'>alert ("<?= $error[$key] ?>"); </script>
    <?php
    }
}

function mostrarFormulario($valores, $error, $pdo, $accion,$donde)
{
    extract($valores);
    $st = $pdo->query('SELECT * FROM generos');
    $generos = $st->fetchAll();
    ?>
    <br>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?= $accion ?> <?= $donde ?>...</h3>
        </div>
        <div class="panel-body">
            <form action="" method="post">
                <div class="form-group <?= hasError('titulo', $error) ?>">
                    <label for="titulo" class="control-label">Título</label>
                    <input id="titulo" type="text" name="titulo"
                           class="form-control" value="<?= h($titulo) ?>">
                    <?php mensajeError('titulo', $error) ?>
                </div>
                <div class="form-group <?= hasError('anyo', $error) ?>">
                    <label for="anyo" class="control-label">Año</label>
                    <input id="anyo" type="text" name="anyo"
                           class="form-control" value="<?= h($anyo) ?>">
                    <?php mensajeError('anyo', $error) ?>
                </div>
                <div class="form-group">
                    <label for="sinopsis" class="control-label">Sinopsis</label>
                    <textarea id="sinopsis"
                              name="sinopsis"
                              rows="8"
                              cols="80"
                              class="form-control"><?= h($sinopsis) ?></textarea>
                </div>
                <div class="form-group <?= hasError('duracion', $error) ?>">
                    <label for="duracion" class="control-label">Duración</label>
                    <input id="duracion" type="text" name="duracion"
                           class="form-control"
                           value="<?= h($duracion) ?>">
                    <?php mensajeError('duracion', $error) ?>
                </div>
                <div class="form-group <?= hasError('genero_id', $error) ?>">
                    <label for="genero_id" class="control-label">Género</label>
                    <select class="form-control" name="genero_id">
                        <?php foreach ($generos as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= selected($g['id'], $genero_id) ?> >
                                <?= $g['genero'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <?php mensajeError('genero_id', $error) ?>
                </div>
                <input type="submit" value="<?= $accion ?>"
                       class="btn btn-success">
                <a href="index.php" class="btn btn-info">Volver</a>
            </form>
        </div>
    </div>
    <?php
}

function mostrarFormularioGenero($valores, $error, $pdo, $accion)
{
    extract($valores);
    ?>
    <br>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?= $accion ?> un nuevo género...</h3>
        </div>
        <div class="panel-body">
            <form action="" method="post">
                <div class="form-group <?= hasError('genero', $error) ?>">
                    <label for="genero" class="control-label">Género</label>
                    <input id="genero" type="text" name="genero"
                           class="form-control" value="<?= h($genero) ?>">
                    <?php mensajeError('genero', $error) ?>
                </div>
                <input type="submit" value="<?= $accion ?>"
                       class="btn btn-success">
                <a href="index.php" class="btn btn-info">Volver</a>
            </form>
        </div>
    </div>
    <?php
}

function mostrarFormularioUsuario($valores, $error, $pdo, $accion)
{
    extract($valores);
    ?>
    <br>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?= $accion ?> un nuevo usuario</h3>
        </div>
        <div class="panel-body">

                <div class="container">
                    <div class="row">
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="login">Usuario:</label>
                                <input class="form-control" type="text" name="login" value="">
                            </div>
                            <div class="form-group">
                                <label for="password">Contraseña:</label>
                                <input class="form-control" type="password" name="password" value="">
                            </div>
                            <input type="submit" value="<?= $accion ?>"
                            class="btn btn-success">
                            <a href="login.php" class="btn btn-info">Volver</a>
                        </form>
                    </div>
                </div>
        </div>
    </div>
    <?php
}

function h($cadena)
{
    return htmlspecialchars($cadena, ENT_QUOTES);
}

function comprobarId()
{
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id === null || $id === false) {
        throw new ParamException();
    }
    return $id;
}

function comprobarPelicula($pdo, $id)
{
    $fila = buscarPelicula($pdo, $id);
    if ($fila === false) {
        throw new ParamException();
    }
    return $fila;
}

function selected($a, $b)
{
    return $a == $b ? 'selected' : '';
}

function comprobarLogin(&$error)
{
    $login = trim(filter_input(INPUT_POST, 'login'));
    if ($login === '') {
        $error['login'] = 'El nombre de usuario no puede estar vacío.';
    }
    return $login;
}

function comprobarLogins($pdo, &$error)
{
    $fltLogin = trim(filter_input(INPUT_POST, 'login'));
    if ($fltLogin === '') {
        $error['login'] = 'El Usuario es obligatorio.';
    } elseif (mb_strlen($fltLogin) > 50) {
        $error['login'] = "El usuario es demasiado largo.";
    }
    if (buscarLoginporLogin($pdo, $fltLogin)) {
        $error['login'] = 'El Usuario ya existe.';
    }
    return $fltLogin;
}

function buscarLoginporLogin($pdo, $Login)
{
    $st = $pdo->prepare('SELECT * FROM usuarios WHERE login = :login');
    $st->execute([':login' => $Login]);
    return $st->fetch();
}

function comprobarPassword(&$error)
{
    $password = trim(filter_input(INPUT_POST, 'password'));
    if ($password === '') {
        $error['password'] = 'La contraseña no puede estar vacía.';
    }
    return $password;
}

/**
 * Comprueba si existe el usuario indicado en el array
 * $valores, con el nombre y la contraseña dados.
 *
 * @param  array      $valores El nombre y la contraseña
 * @param  PDO        $pdo     Objeto PDO usado para buscar al usuario
 * @param  array      $error   El array de errores
 * @return array|bool          La fila del usuario si existe; false e.o.c.
 */
function comprobarUsuario($valores, $pdo, &$error)
{
    extract($valores);
    $st = $pdo->prepare('SELECT *
                           FROM usuarios
                          WHERE login = :login');
    $st->execute(['login' => $login]);
    $fila = $st->fetch();
    if ($fila !== false) {
        if (password_verify($password, $fila['password'])) {
            return $fila;
        }
    }
    $error['sesion'] = 'El usuario no existe.';
    return false;
}

function mostrarMenu()
{ ?>
    <nav class="navbar navbar-default navbar-inverse">
        <div class="container">
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      Menú<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a href="../peliculas/index.php" class="btn">Películas</a></li>
                      <li><a href="../generos/index.php" class="btn">Géneros</a></li>
                    </ul>
                  </li>
                </ul>
            <div class="navbar-header">
                <a class="navbar-brand" href="#">FilmAffinity</a>
            </div>
            <div class="navbar-text navbar-right">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <?= $_SESSION['usuario'] ?>
                    <a href="../comunes/logout.php" class="btn btn-success">Logout</a>
                <?php else: ?>
                    <a href="../comunes/login.php" class="btn btn-success">Login</a>
                <?php endif ?>
            </div>
        </div>
    </nav>
<?php }

function pie()
{
    if (!isset($_COOKIE['acepta'])): ?>
        <nav class="navbar navbar-fixed-bottom navbar-inverse">
            <div class="container">
                <div class="navbar-text navbar-right">
                    Tienes que aceptar las políticas de cookies.
                    <a href="crear_cookie.php" class="btn btn-success">Aceptar cookies</a>
                </div>
            </div>
        </nav>
    <?php endif ?>
    <hr>
    <div class="row">
        <p class="text-left">Copyright (c) 2018 Jose Luis Castillo Peña</p>
    </div>
    <?php
}
