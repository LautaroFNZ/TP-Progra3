<?php

class Pedido
{
    public $_id;
    public $_idMesa;

    public function setter($idmesa)
    {
        $this->_idMesa = $idmesa;
    }

    public function alta()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO pedidos (idMesa) VALUES (:idMesa)");
        
        $command->bindValue(':nombre',$this->_idMesa,PDO::PARAM_STR);
        $command->execute();

        return "Di de alta un pedido";
    }

    public function listar()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pedidos");
        $command->execute();

        return "Listando todos los pedidos";
    }

}