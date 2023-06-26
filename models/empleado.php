<?php

require "./db/accesoDatos.php";


class Empleado
{
    public $id;
    public $nombre;
    public $puesto;
    public $usuario;
    public $password;
    
    public function setter($nombre,$puesto,$usuario,$password)
    {
        $this->nombre = $nombre;
        $this->puesto = $puesto;
        $this->usuario = $usuario;
        $this->password = $password;
    }


    public function alta()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO empleados (nombre,puesto,usuario,password) VALUES (:nombre,:puesto,:usuario,:password)");
        
        $command->bindValue(':nombre',$this->nombre,PDO::PARAM_STR);
        $command->bindValue(':puesto',$this->puesto,PDO::PARAM_STR);
        $command->bindValue(':usuario',$this->usuario,PDO::PARAM_STR);
        $command->bindValue(':password',$this->password,PDO::PARAM_STR);
        $command->execute();

        return $instancia->lastId();
    }

    public static function listar()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM empleados");
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function verificarUsuario($usuario)
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM empleados WHERE usuario = :usuario");
        
        $command->bindValue(':usuario',$usuario,PDO::PARAM_STR);
        $command->execute();

        return $command->fetchObject('Empleado');
    }

    public static function validarPuesto($puesto)
    {   
       return strcasecmp($puesto,"bartender") == 0 || strcasecmp($puesto,"cervecero") == 0 || strcasecmp($puesto,"cocinero") == 0 || strcasecmp($puesto,"mozo") == 0 || strcasecmp($puesto,"socio") == 0;    
    }

}



?>