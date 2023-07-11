<?php

class Pendientes
{
    public $id;
    public $idProducto;
    public $linkPedido;
    public $nroPedido;
    public $idEmpleado;
    public $sector;
    public $estado;
    public $fechaEstimada;
    public $fechaEntregaReal;

    public function setter($idProducto,$nroPedido,$sector,$linkPedido)
    {
        $this->idProducto = $idProducto;
        $this->nroPedido = $nroPedido;
        $this->idEmpleado = -1;
        $this->sector = $sector;
        $this->linkPedido = $linkPedido;
        $this->estado = 'pendiente';
        $this->fechaEstimada = '';
        $this->fechaEntregaReal = '';
    }

    public function alta()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO pendientes (idProducto,nroPedido,idEmpleado,sector,estado,fechaEstimada,fechaEntregaReal,linkPedido) VALUES (:idProducto,:nroPedido,:idEmpleado,:sector,:estado,:fechaEstimada,:fechaEntregaReal,:linkPedido)");
        

        $command->bindValue(':idProducto',$this->idProducto);
        $command->bindValue(':nroPedido',$this->nroPedido,PDO::PARAM_STR);
        $command->bindValue(':idEmpleado',$this->idEmpleado);
        $command->bindValue(':sector',strtolower($this->sector),PDO::PARAM_STR);
        $command->bindValue(':estado',strtolower($this->estado));
        $command->bindValue(':fechaEstimada',$this->fechaEstimada,PDO::PARAM_STR);
        $command->bindValue(':fechaEntregaReal',$this->fechaEntregaReal,PDO::PARAM_STR);
        $command->bindValue(':linkPedido',$this->linkPedido,PDO::PARAM_STR);
        $command->execute();

        return $instancia->lastId();
    }

    public static function asignarPendiente($idPendiente,$idEmpleado,$tiempo,$sector)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("UPDATE pendientes SET fechaEstimada = :tiempo, idEmpleado = :idEmpleado, estado = 'en Preparacion' WHERE sector = :sector AND id = :idPendiente");
        
        $command->bindValue(':tiempo',date('d-m-y H:i:s', strtotime("+{$tiempo} minutes")),PDO::PARAM_STR);
        $command->bindValue(':idEmpleado',intval($idEmpleado));
        $command->bindValue(':idPendiente',intval($idPendiente));
        $command->bindValue(':sector',$sector,PDO::PARAM_STR);
        
        
        $filasAfectadas = $command->execute();

        return $filasAfectadas > 0;

    }

    public function listar()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pendientes");
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Pendientes');
    }

    public function listarPorSector($puesto)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pendientes WHERE sector = :sector AND estado <> 'listo para servir'");
        
        $command->bindValue(':sector',strtolower($puesto),PDO::PARAM_STR);
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Pendientes');
    }

    public function listarPosUsuario($idEmpleado)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pendientes WHERE idEmpleado = :idEmpleado AND estado <> 'listo para servir'");
        
        $command->bindValue(':idEmpleado',strtolower($idEmpleado),PDO::PARAM_STR);
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Pendientes');
    }




    public static function buscarId($id)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pendientes WHERE id = :id");

        $command->bindValue(':id',$id);

        $command->execute();

        return $command->fetchObject('Pendientes');
    }


    public static function pendienteListo($id)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("UPDATE pendientes SET estado = 'listo para servir', fechaEntregaReal = :fechaReal WHERE id = :id");

        $command->bindValue(':id',$id);
        $command->bindValue(':fechaReal',date('d-m-y H:i:s'));
        $filasAfectadas = $command->execute();

        return $filasAfectadas > 0;
    }



    public static function consultarEstado($nroPedido,$idProducto)
    {
        $instancia = accesoDatos::instance();
        

        $command = $instancia->preparer("SELECT pendientes.estado FROM pendientes WHERE idProducto = :idProducto AND nroPedido = :nroPedido");

        $command->bindValue(':idProducto',$idProducto);
        $command->bindValue(':nroPedido',$nroPedido);
        $command->execute();

        return $command->fetchColumn();
    }

    public static function listarPendientesListos()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT p.idProducto, p.linkPedido, pd.estadoPedido, pd.idMesa FROM pendientes p INNER JOIN pedidos pd ON p.linkPedido = pd.linkPendiente WHERE p.estado = 'listo para servir';");
        $command->execute();

        return $command->fetchAll();
    }

    public static function pendienteNoAsignado($id)
    {
        $pendiente = Pendientes::buscarId($id);

        return $pendiente->idEmpleado == -1;
    }



}

?>