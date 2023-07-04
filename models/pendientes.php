<?php

class Pendientes
{
    public $id;
    public $idProducto;
    public $nroPedido;
    public $sector;
    public $estado;
    public $fechaEntregaPedido;
    public $fechaEntregaReal;

    public function setter($idProducto,$nroPedido,$sector,$fechaEntregaPedido)
    {
        $this->idProducto = $idProducto;
        $this->nroPedido = $nroPedido;
        $this->sector = $sector;
        $this->estado = 'en preparacion';
        $this->fechaEntregaPedido = $fechaEntregaPedido;
        $this->fechaEntregaReal = '';
    }

    public function alta()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO pendientes (idProducto,nroPedido,sector,estado,fechaEntregaPedido,fechaEntregaReal) VALUES (:idProducto,:nroPedido,:sector,:estado,:fechaEntregaPedido,:fechaEntregaReal)");
        

        $command->bindValue(':idProducto',$this->idProducto);
        $command->bindValue(':nroPedido',$this->nroPedido);
        $command->bindValue(':sector',strtolower($this->sector),PDO::PARAM_STR);
        $command->bindValue(':estado',strtolower($this->estado));
        $command->bindValue(':fechaEntregaPedido',$this->fechaEntregaPedido,PDO::PARAM_STR);
        $command->bindValue(':fechaEntregaReal',$this->fechaEntregaReal,PDO::PARAM_STR);
        $command->execute();

        return $instancia->lastId();
    }

    public function listar()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pendientes");
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Pendientes');
    }

    public function listarPorSector($puesto)
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pendientes WHERE sector = :sector AND estado <> 'listo'");
        
        $command->bindValue(':sector',strtolower($puesto),PDO::PARAM_STR);
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Pendientes');
    }

    public static function buscarId($id)
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pendientes WHERE id = :id");

        $command->bindValue(':id',$id);

        $command->execute();

        return $command->fetchObject('Pendientes');
    }


    public static function pendienteListo($id)
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("UPDATE pendientes SET estado = 'listo', fechaEntregaReal = :fechaReal WHERE id = :id");

        $command->bindValue(':id',$id);
        $command->bindValue(':fechaReal',date('d-m-y H:i:s'));
        $command->execute();

        return $command->fetchObject('Pendientes'); 
    }

    public static function consultarEstado($nroPedido,$idProducto)
    {
        $instancia = AccesoDatos::instance();
        

        $command = $instancia->preparer("SELECT pendientes.estado FROM pendientes WHERE idProducto = :idProducto AND nroPedido = :nroPedido");

        $command->bindValue(':idProducto',$idProducto);
        $command->bindValue(':nroPedido',$nroPedido);
        $command->execute();

        return $command->fetchColumn();
    }

}

?>