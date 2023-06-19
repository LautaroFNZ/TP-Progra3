<?php

require "./db/accesoDatos.php";

class Empleado
{
    public $_id;
    public $_nombre;
    public $_puesto;
    public $_usuario;
    public $_password;

    public $_fechaLogin;
    
    public function setter($nombre,$puesto,$usuario,$password)
    {
        $this->_nombre = $nombre;
        $this->_puesto = $puesto;
        $this->_usuario = $usuario;
        $this->_password = $password;
        $this->_fechaLogin = date('Y-m-d H:i:s');
    }


    public function alta()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO empleados (nombre,puesto,usuario,password) VALUES (:nombre,:puesto,:usuario,:password)");
        
        $command->bindValue(':nombre',$this->_nombre,PDO::PARAM_STR);
        $command->bindValue(':puesto',$this->_puesto,PDO::PARAM_STR);
        $command->bindValue(':usuario',$this->_usuario,PDO::PARAM_STR);
        $command->bindValue(':password',$this->_password,PDO::PARAM_STR);
        $command->execute();

        return $instancia->lastId();
    }

    public static function listar()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM empleados");
        $command->execute();

        return "Listando todos los empleados";
    }


}



?>