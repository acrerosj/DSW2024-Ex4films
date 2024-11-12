<?php include "top.php"; ?>
<section id="create">
    <h2>Nueva categoría</h2>
    <nav>
        <p><a href="film.php">Volver</a></p>
    </nav>
<?php 
if (empty($_POST['name'])) {
?>
    <form action="" autocomplete="off" method="post">
        <fieldset>
            <legend>Datos de la categoría</legend>
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" required>
            <p></p>
            <input type="reset" value="Limpiar">            
            <input type="submit" value="Crear">
        </fieldset>
    </form>
<?php
} else {
    require 'connection.php';
    $name = $_POST['name'];  
    try {
        $stmtInsert = $conn->prepare('INSERT INTO category (category_id, name, last_update) VALUES (NULL, :name, CURRENT_TIMESTAMP)');
        $stmtInsert->bindParam(':name', $name);
        $stmtInsert->execute();
        if ($stmtInsert->rowCount() > 0) {
            echo "<div class=\"alert alert-success\">Insertada categoria creada satisfactoriamente</div>";
        } else {
          echo "<div class=\"alert alert-error\">No se puede crear la categoría.</div>";
        } 
        $stmtInsert = null;
    } catch (PDOException $e) {
        die('<div class="alert alert-error">Error al insertar la categoría: ' . $e->getMessage() . '</div>');
    }

}
$conn = null;
?>
</section>
<?php include "bottom.php"; ?>