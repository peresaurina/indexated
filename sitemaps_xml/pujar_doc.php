<html>
    <body>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="file">Pujar doc webmastertools:</label>
            <input type="file" name="archivo" id="archivo" />
            <input type="submit" name="boton" value="Subir" />
        </form>
        <div class="resultado">
            <?php
            if (isset($_POST['boton'])) {
                //Si es que hubo un error en la subida, mostrarlo, de la variable $_FILES podemos extraer el valor de [error], que almacena un valor booleano (1 o 0).
                if ($_FILES["archivo"]["error"] > 0) {
                    echo $_FILES["archivo"]["error"] . "";
                } else {
                    // Si no hubo ningun error, hacemos otra condicion para asegurarnos que el archivo no sea repetido
                    if (file_exists("rebuts/" . $_FILES["archivo"]["name"])) {
                        echo $_FILES["archivo"]["name"] . " ja existeix. ";
                    } else {
                        // Si no es un archivo repetido y no hubo ningun error, procedemos a subir a la carpeta /archivos, seguido de eso mostramos la imagen subida
                        move_uploaded_file($_FILES["archivo"]["tmp_name"], "rebuts/" . $_FILES["archivo"]["name"]);
                        echo "Archiu pujat!";
                        //echo '<img src="archivos/".$_FILES["archivo"]["name"]."">';
                    }
                }
            } else {
                // Si el usuario intenta subir algo que no es una imagen o una imagen que pesa mas de 20 KB mostramos este mensaje
                echo "Archivo no permitido";
            }
            ?>
        </div>
    </body>
</html>
