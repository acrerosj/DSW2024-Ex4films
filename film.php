<?php include "top.php"; ?>
<?php require 'connection.php'; ?>
    <!--
    <div class="alert alert-success">¡Ejemplo mensaje de éxito!</div>
    <div class="alert alert-error">¡Ejemplo mensaje de error!</div>
    -->
    <section id="films">
        <h2>Peliculas</h2>
<?php
    try {
        $stmtCategories = $conn->prepare('SELECT category_idkk, name FROM category');
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
            printf('<option>%s</option>', $category->name);
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
        die('<p>Se jodio: ' . $e->getMessage() . '</p>');
    }
    
    $conn = null; 
?>
        <nav>
            <fieldset>
                <legend>Acciones</legend>                    
                <a href="create.php">
                    <button>Crear Categoria</button>
                </a>                    
            </fieldset>
        </nav>
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
                <tr>
                    <td>El tercer hombre</td>
                    <td class="center">1949</td>
                    <td class="center">108</td>
                    <td class="actions">                            
                        <a class="button" href="category_film.php?...">
                            <button>Cambiar categorías</button>
                        </a>               
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
<?php include "bottom.php"; ?>