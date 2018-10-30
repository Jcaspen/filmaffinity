<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Confirmar borrado</title>
    </head>
    <body>
        <?php $id = $_GET['id'] ?>
        <h3>¿Seguro que desea borrar la fila?</h3>
        <form action="index.php" method="post">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="submit" value="Sí">
            <a href="index.php">No</a>
        </form>
    </body>
</html>
