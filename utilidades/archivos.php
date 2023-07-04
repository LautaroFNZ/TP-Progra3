<?php

require_once "./models/empleado.php";

class Archivos
{
    public function leerUsuariosCSV($path)
    {
        $retorno = json_encode(array('mensaje'=>'Error al leer los usuarios','status'=> false));

        if($ar =fopen($path,'r'))
        {
            while(($empleado = fgetcsv($ar)) !== false)
            {   
                
                $empleadoCsv = new Empleado();
                if($empleado[1]!= 'NOMBRE' && $empleado[2]!='PUESTO' && $empleado[3]!='USUARIO' && $empleado[4]!='PASSWORD')
                {
                    if(!Empleado::verificarUsuario($empleado[3]))
                    {
                        $empleadoCsv->setter($empleado[1],$empleado[2],$empleado[3],$empleado[4]);
                    
                        $empleadoCsv->alta();
                        $retorno = json_encode(array('mensaje'=>'Empleados guardados con exito!','status'=> true));
                    }else $retorno = json_encode(array('mensaje'=>'Empleados existente en la base de datos! No se guardará','status'=> false));

                }                                    

            }

            fclose($ar);           
        }

        return $retorno;
    }

    public function guardarUsuariosCSV($path)
    {
        $retorno = false;

        if($ar = fopen($path,"w"))
        {
            $empleados = Empleado::listar();

            if(!empty($empleados))
            {
                $encabezado = "ID,NOMBRE,PUESTO,USUARIO,PASSWORD\n";
                fwrite($ar,$encabezado);
                foreach($empleados as $empleado)
                {
                    $linea = $empleado->id . "," . $empleado->nombre . "," . $empleado->puesto . ",". $empleado->usuario . ","  . $empleado->password . "\n";
                    fwrite($ar,$linea);
                }

                $retorno = true;
            }   
        }   

        return $retorno;
    }

}

?>