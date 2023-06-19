<?php

class Producto
{
    public $_id;
    public $_nombre;
    public $_tipoProducto;
    public $_cantidad;
    public $_precio;
    
    public function setter($nombre = null,$tipoProducto = null,$cantidad = null,$precio = null)
    {
        $this->_nombre = $nombre;
        $this->_tipoProducto = $tipoProducto;
        $this->_cantidad = $cantidad;
        $this->_precio = $precio;

    }

    public function alta()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO empleados (nombre,tipo,cantidad,precio) VALUES (:nombre,:tipo,:cantidad,:precio)");
        
        $command->bindValue(':nombre',$this->_nombre,PDO::PARAM_STR);
        $command->bindValue(':tipo',$this->_tipoProducto,PDO::PARAM_STR);
        $command->bindValue(':cantidad',$this->_cantidad,PDO::PARAM_STR);
        $command->bindValue(':precio',$this->_precio,PDO::PARAM_STR);
        $command->execute();

        return "Di de alta un producto";
    }

    public function listar()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM productos");
        $command->execute();

        return "Listando todos los productos";
    }

}


?>