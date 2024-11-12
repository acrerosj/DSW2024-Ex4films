<?php include "top.php"; ?>
<?php require 'connection.php'; ?>
    <!--
    <div class="alert alert-success">¡Ejemplo mensaje de éxito!</div>
    <div class="alert alert-error">¡Ejemplo mensaje de error!</div>
    -->
<?php
    // Compruebo si está la opción de borrar:
    if(isset($_GET['delete']) && !empty($_GET['category'])) {
        $category_id = $_GET['category'];
        try {            
            $stmtDelete = $conn->prepare("DELETE FROM category WHERE category_id = :category_id");
            $stmtDelete->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmtDelete->execute();
            if ($stmtDelete->rowCount() > 0) {
                echo "<div class=\"alert alert-success\">Categoría eliminada</div>";
            } else {
              echo "<div class=\"alert alert-error\">No se puede borrar dicha categoría porque no la encuentra.</div>";
            }                    
            $stmtDelete = null;
        } catch (PDOException $e) {
            echo "<div class=\"alert alert-error\">No se puede borrar dicha categoría porque tiene películas asociadas a ella.</div>";
        }
    }
?>
    
    <section id="films">
        <h2>Peliculas</h2>
<?php
    try {
        $stmtCategories = $conn->prepare('SELECT category_id, name FROM category');
        $stmtCategories->execute();
        $categories = $stmtCategories->fetchAll(PDO::FETCH_OBJ);

?>
        <form action="film.php" method="get">
          <fieldset>
            <legend>Categorías</legend>
            <select name="category" id="">
              <option selected disabled>Elige una categoría</option>
<?php
        foreach($categories as $category) {
            printf('<option value="%d">%s</option>', $category->category_id, $category->name);
        }
?>
            </select>
            <input type="submit" name="search" value="buscar">
            <input type="submit" name="delete" value="eliminar">
          </fieldset>
        </form>
<?php
        $stmtCategories = null;
    } catch (Exception $e) {
        die('<div class="alert alert-error">Error al consultar las categorías: ' . $e->getMessage() . '</div>');
    }

?>
        <nav>
            <fieldset>
                <legend>Acciones</legend>                    
                <a href="create.php">
                    <button>Crear Categoria</button>
                </a>                    
            </fieldset>
        </nav>
<?php
    if(isset($_GET['search']) && !empty($_GET['category'])) {
        $category = $_GET['category'];
        try {
            $stmtFilms = $conn->prepare('SELECT film.film_id, film.title, film.release_year, film.length FROM film, film_category WHERE film.film_id = film_category.film_id AND film_category.category_id = :category_id');
            $stmtFilms->bindParam(':category_id', $category);
            $stmtFilms->execute();
            if ($stmtFilms->rowCount() == 0) {
                echo "<h2>No hay películas para esta categoría</h2>";
            } else {
    ?>
             <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Año</th>
                        <th>Duración</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
    <?php
                $films = $stmtFilms->fetchAll(PDO::FETCH_OBJ);
                foreach($films as $film) {
    ?>
                    <tr>
                        <td><?=$film->title?></td>
                        <td class="center"><?=$film->release_year?></td>
                        <td class="center"><?=$film->length?></td>
                        <td class="actions">                            
                            <a class="button" href="category_film.php?film_id=<?=$film->film_id?>&title=<?=$film->title?>">
                                <button>Cambiar categorías</button>
                            </a>               
                        </td>
                    </tr>
<?php
                }
?>
                </tbody>
            </table>
<?php
            }
            $stmtFilms = null;            
        } catch (PDOException $e) {
            die('<div class="alert alert-error">Error al consultar las películas: ' . $e->getMessage() . '</div>');
        }
    }  
    $conn = null; 
?>
    </section>
<?php include "bottom.php"; ?>