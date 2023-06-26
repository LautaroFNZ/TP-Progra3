<?php

require_once "./models/empleado.php";

class Archivos
{
    public function guardarArchivo($carpetaDestino,$nombreArchivo)
    {
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {

            $rutaArchivoTemporal = $_FILES['archivo']['tmp_name'];

            if (!is_dir($carpetaDestino)) {
                mkdir($carpetaDestino, 0777, true);
            }

            $rutaDestino = $carpetaDestino . $nombreArchivo;
            echo "Archivo guardado exitosamente en la carpeta BackUpUsuarios.";
            return move_uploaded_file($rutaArchivoTemporal, $rutaDestino);

        } else {
            echo "No se ha proporcionado un archivo válido.";
        }
    }

    public function leerUsuariosCSV($rutaArchivo)
    {
        $confirmacion = false;

        if (($handle = fopen($rutaArchivo, "r")) !== false) 
        {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) 
            {
                $empleado = new Empleado();

                
                $id = $data[0];
                $nombre = $data[1];
                $puesto = $data[2];
                $usuario = $data[3];
                $password = $data[4];
                
                $empleado->setter($nombre,$puesto,$usuario,$password);
                $empleado->id = $id;

                if(!Empleado::verificarUsuario($usuario))
                {
                    $empleado->alta();
                    $confirmacion = true;
                }
                

            }

            fclose($handle);
        }

        if($confirmacion)
        {
            return "Se han dado de alta los usuario no existentes en la base de datos!";
            
        }else return "No hemos dado de alta usuarios nuevos";

    }
}

?>