<?php

class Producto
{
    public $id;
    public $nombre;
    public $sector;
    public $precio;
    
    public function setter($nombre = null,$sector = null,$precio = null)
    {
        $this->nombre = $nombre;
        $this->sector = $sector;
        $this->precio = $precio;

    }

    public function alta()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO productos (nombre,sector,precio) VALUES (:nombre,:sector,:precio)");
        
        $command->bindValue(':nombre',strtolower($this->nombre),PDO::PARAM_STR);
        $command->bindValue(':sector',strtolower($this->sector),PDO::PARAM_STR);
        $command->bindValue(':precio',$this->precio,PDO::PARAM_STR);
        $command->execute();

        return $instancia->lastId();
    }

    public function productoExiste($nombre,$sector)
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT id FROM productos WHERE nombre = :nombre AND sector = :sector");
        
        $command->bindValue(':nombre',$nombre);
        $command->bindValue(':sector',$sector);
        $command->execute();

        return $command->fetch(PDO::FETCH_ASSOC);
    }

    public function listar()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM productos");
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function verificarsector($sector)
    {
        return strcasecmp($sector,"bartender") == 0 || strcasecmp($sector,"cervecero") == 0 || strcasecmp($sector,"cocinero") == 0;
    }

    public static function buscarPorId($id)
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM productos WHERE id = :id");

        $command->bindValue(':id',$id);
        $command->execute();

        return $command->fetchObject('Producto');
    }

    public static function traerSector($id)
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT productos.sector FROM productos WHERE id = :id");

        $command->bindValue(':id',$id);
        $command->execute();

        return $command->fetchColumn();
    }

}


?>