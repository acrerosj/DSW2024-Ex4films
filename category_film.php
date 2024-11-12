<?php include "top.php"; ?>
<?php require 'connection.php'; ?>
    <!--
    <div class="alert alert-success">¡Ejemplo mensaje de éxito!</div>
    <div class="alert alert-error">¡Ejemplo mensaje de error!</div>
    -->
    <nav>
        <p><a href="film.php">Volver</a></p>
    </nav>
<?php 
if (empty($_REQUEST['film_id']) || empty($_REQUEST['title'])) {
  echo '<div class="alert alert-error">No se han enviado los datos de la película</div>';
} else {
  $film_id = $_REQUEST['film_id'];
  $title = $_REQUEST['title']; 
   // -------- Actualizar categorías -----------------------
   if (isset($_POST['update'])) {  // Se está recibiendo del formulario la modificación en las categorías.
    $conn->beginTransaction();
    try {
      // Obtener id de las categorias a las que ya pertenecía la película.
      $stmtFilmOldIds = $conn->prepare("SELECT category_id FROM film_category WHERE film_id = :film_id");
      $stmtFilmOldIds->bindParam(':film_id', $film_id);
      $stmtFilmOldIds->execute();
      $rows = $stmtFilmOldIds->fetchAll(PDO::FETCH_OBJ);
      $listOldIds = [];
      foreach($rows as $row) {
        $listOldIds[] = $row->category_id;
      }
      $stmtFilmOldIds = null;

      // Del formulario se obtiene los id de las categorias que ahora queremos asignar a la película.
      $listNewIds = isset($_POST['category_ids']) ? $_POST['category_ids'] : [];
      
      
      // Se han de borrar los que estaban en $listOldIds y no están en $listNewIds.
      $deleteIds = array_diff($listOldIds, $listNewIds);

      $stmtDeleteIds = $conn->prepare('DELETE FROM film_category WHERE film_id = :film_id AND category_id = :category_id');
      $stmtDeleteIds->bindParam(':film_id', $film_id, PDO::PARAM_INT);
      $stmtDeleteIds->bindParam(':category_id', $category_id, PDO::PARAM_INT);

      foreach ($deleteIds as $category_id) {
        echo "<p>delete $category_id</p>";
        $stmtDeleteIds->execute();
      }

      $stmtDeleteIds = null;
      
      // Se han de insertar los que estan en $listNewIds y no están en $listOldIds.
      $insertIds = array_diff($listNewIds, $listOldIds);

      $stmtInsertIds = $conn->prepare('INSERT INTO film_category (film_id, category_id, last_update) VALUES (:film_id, :category_id, CURRENT_TIMESTAMP)');
      $stmtInsertIds->bindParam(':film_id', $film_id, PDO::PARAM_INT);
      $stmtInsertIds->bindParam(':category_id', $category_id, PDO::PARAM_INT);
      
      foreach ($insertIds as $category_id) {
        echo "<p>insert $category_id</p>";
        $stmtInsertIds->execute();
      }

      $stmtInsertIds = null;

      $conn->commit();
    } catch (Exception $e) {
      $conn->rollBack();
      echo '<div class="alert alert-error">No se han podido modificar las categorías de la película</div>';
    }
   }
   // -------- FIN de Actualizar categorías -----------------------
   try {
    // Se prepara la consulta de todas las categorías.
    $stmtCategories = $conn-> prepare('SELECT category_id, name FROM category');
    $stmtCategories->execute();
    $categories = $stmtCategories->fetchAll(PDO::FETCH_OBJ);

    // Se prepara la consulta para comprobar si dicha categoría pertenece a la película.
    $stmtBelong = $conn->prepare('SELECT * FROM film_category WHERE film_id = :film_id AND category_id = :category_id');
    $stmtBelong->bindParam(':film_id', $film_id, PDO::PARAM_INT);
    $stmtBelong->bindParam(':category_id', $category_id, PDO::PARAM_INT);
  ?>
      <section id="films">
        <h2>Categorías de la pelicula: <?=$title?></h2>
        <form action="category_film.php" method="post">
          <input type="hidden" name="film_id" value="<?=$film_id?>">
          <input type="hidden" name="title" value="<?=$title?>">
          <ul>
<?php
    foreach($categories as $category) {
      $category_id = $category->category_id;
      $stmtBelong->execute();
      $checked = $stmtBelong->rowCount() >0 ? 'checked' : '';
      printf('<li><label><input type="checkbox" name="category_ids[]" value="%d" %s>%s</label></li>', $category->category_id, $checked, $category->name);
    }            
?>
          </ul>
          <p>
            <input type="submit" value="Actualizar" name="update">
          </p>
        </form>
      <section>
  <?php
    $stmtBelong = null;
    $stmtCategories = null;
   } catch (PDOException $e) {
    die('<div class="alert alert-error">Problemas al mostrar las categorías de las películas: ' . $e->getMessage() . '</div>');
   }

}
$conn = null;
?>      
<?php include "bottom.php"; ?>