<?php

class Pedido
{
    public $id;
    public $idMesa;
    public $idProductos;
    public $precioTotal;
    public $usuarioVenta;
    public $estadoPedido;
    public $fechaEntrega;
    public $fechaEstimada;

    public function setter($idmesa,$idProductos,$usuarioVenta,$precioTotal)
    {
        $this->idMesa = $idmesa;
        $this->idProductos = $idProductos;
        $this->usuarioVenta = $usuarioVenta;
        $this->precioTotal = $precioTotal;
        $this->estadoPedido = 'en Preparacion';
        $this->fechaEntrega = '';
        $this->fechaEstimada = date('d-m-y H:i:s', strtotime('+20 minutes'));
    }

    public function alta()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO pedidos (idMesa,idProductos,fechaEstimada,usuarioVenta,fechaEntrega,precioTotal,estadoPedido) VALUES (:idMesa,:idProductos,:fechaEstimada,:usuarioVenta,:fechaEntrega,:precioTotal,:estadoPedido)");
        
        $command->bindValue(':idMesa',$this->idMesa,PDO::PARAM_STR);
        $command->bindValue(':idProductos',$this->idProductos,PDO::PARAM_STR);
        $command->bindValue(':precioTotal',$this->precioTotal);
        $command->bindValue(':fechaEstimada',$this->fechaEstimada,PDO::PARAM_STR);
        $command->bindValue(':usuarioVenta',strtolower($this->usuarioVenta),PDO::PARAM_STR);
        $command->bindValue(':fechaEntrega',$this->fechaEntrega,PDO::PARAM_STR);
        $command->bindValue(':estadoPedido',strtolower($this->estadoPedido),PDO::PARAM_STR);
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

    public static function buscarPorId($id,$idMesa)
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pedidos WHERE id = :id AND idMesa = :idMesa");

        $command->bindValue(':id',$id);
        $command->bindValue(':idMesa',$idMesa);
        $command->execute();

        return $command->fetchObject('Pedido');
    }

    public static function asignarTiempoEntrega($id,$estadoPedido)
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("UPDATE pedidos SET fechaEntrega = :fechaEntrega, estadoPedido = :estadoPedido WHERE estadoPedido <> 'entregado' AND id = :id");
    

        $command->bindValue(':id',$id);
        $command->bindValue(':fechaEntrega',date('d-m-y H:i:s'));
        $command->bindValue(':estadoPedido',strtolower($estadoPedido));
        $filasAfectadas = $command->execute();

        return $filasAfectadas > 0;
    }


}