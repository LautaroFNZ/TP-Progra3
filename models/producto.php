<?php

class Producto
{
    public $id;
    public $nombre;
    public $tipo;
    public $precio;
    
    public function setter($nombre = null,$tipo = null,$precio = null)
    {
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->precio = $precio;

    }

    public function alta()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO producto (nombre,tipo,precio) VALUES (:nombre,:tipo,:precio)");
        
        $command->bindValue(':nombre',$this->nombre,PDO::PARAM_STR);
        $command->bindValue(':tipo',$this->tipo,PDO::PARAM_STR);
        $command->bindValue(':precio',$this->precio,PDO::PARAM_STR);
        $command->execute();

        return $instancia->lastId();
    }

    public function productoExiste($nombre,$tipo)
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT id FROM producto WHERE nombre = :nombre AND tipo = :tipo");
        
        $command->bindValue(':nombre',$nombre);
        $command->bindValue(':tipo',$tipo);
        $command->execute();

        return $command->fetch(PDO::FETCH_ASSOC);
    }

    public function listar()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM producto");
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function verificarTipo($tipo)
    {
        return strcasecmp($tipo,"plato principal") == 0 || strcasecmp($tipo,"postre") == 0 || strcasecmp($tipo,"bebida") == 0 || strcasecmp($tipo,"trago") == 0;
    }

}


?>