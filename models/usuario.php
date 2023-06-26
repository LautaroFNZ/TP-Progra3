<?php

class Usuario{
    public $usuario;
    public $fechaString;
    public $puesto;

    public function setter($usuario,$puesto)
    {
        $this->usuario = $usuario;
        $this->puesto = $puesto;
        $this->fechaString = date('d-m-y H:i:s');
    }

    public function alta()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO info_login (usuario,fechaString,puesto) VALUES (:usuario,:fechaString,:puesto)");
        
        $command->bindValue(':usuario',$this->usuario,PDO::PARAM_STR);
        $command->bindValue(':fechaString',$this->fechaString,PDO::PARAM_STR);
        $command->bindValue(':puesto',$this->puesto,PDO::PARAM_STR);
        $command->execute();

        return $instancia->lastId();
    }

    public static function listar()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM info_login");
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }
}


?>