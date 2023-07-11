<?php



class Usuario{
    public $id;
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
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO info_login (usuario,fechaString,puesto) VALUES (:usuario,:fechaString,:puesto)");
        
        $command->bindValue(':usuario',strtolower($this->usuario),PDO::PARAM_STR);
        $command->bindValue(':fechaString',$this->fechaString,PDO::PARAM_STR);
        $command->bindValue(':puesto',strtolower($this->puesto),PDO::PARAM_STR);
        $command->execute();

        return $instancia->lastId();
    }

    public static function listar()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM info_login");
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function traerRegistroUsuario($usuario)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM info_login WHERE usuario = :usuario");
        $command->bindValue(':usuario',strtolower($usuario),PDO::PARAM_STR);
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }
}


?>