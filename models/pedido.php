<?php

class Pedido
{
    public $id;
    public $idMesa;
    public $nombreProducto;
    public $tipoProducto;
    public $usuarioVenta;

    public function setter($idmesa,$nombreProducto,$tipoProducto,$usuarioVenta)
    {
        $this->idMesa = $idmesa;
        $this->nombreProducto = $nombreProducto;
        $this->tipoProducto = $tipoProducto;
        $this->usuarioVenta = $usuarioVenta;
    }

    public function alta()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO pedidos (idMesa,nombreProducto,tipoProducto,usuarioVenta) VALUES (:idMesa,:nombreProducto,:tipoProducto,:usuarioVenta)");
        
        $command->bindValue(':idMesa',$this->idMesa,PDO::PARAM_STR);
        $command->bindValue(':nombreProducto',$this->nombreProducto,PDO::PARAM_STR);
        $command->bindValue(':tipoProducto',$this->tipoProducto,PDO::PARAM_STR);
        $command->bindValue(':usuarioVenta',$this->usuarioVenta,PDO::PARAM_STR);
        $command->execute();

        return $instancia->lastId();
    }

    public function listar()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pedidos");
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

}