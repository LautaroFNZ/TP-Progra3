<?php




class Factura
{
    public $id;
    public $precio;
    public $nroPedido;
    public $idMesa;
    public $nombreCliente;
    public $fecha;

    public function setter($precio, $nroPedido, $idMesa, $nombreCliente)
    {
        $this->precio = $precio;
        $this->nroPedido = $nroPedido;
        $this->idMesa = $idMesa;
        $this->nombreCliente = $nombreCliente;
        $this->fecha = date('d-m-y');
    }

    public function alta()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO facturas (precio,nroPedido,idMesa,nombreCliente,fecha) VALUES (:precio,:nroPedido,:idMesa,:nombreCliente,:fecha)");

        $command->bindValue(':precio',$this->precio);
        $command->bindValue(':nroPedido',$this->nroPedido);
        $command->bindValue(':idMesa',$this->idMesa);
        $command->bindValue(':nombreCliente',strtolower($this->nombreCliente));
        $command->bindValue(':fecha',$this->fecha,PDO::PARAM_STR);
        $command->execute();
        
        return $instancia->lastId();
    }

    public function listarMesasOrdenadasPorFactura()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT idMesa,precio FROM facturas ORDER BY precio DESC");
        $command->execute();

        $resultados = array();

        while ($fila = $command->fetch(PDO::FETCH_ASSOC)) 
        {
            $registro = array("idMesa" => $fila["idMesa"],"precio" => $fila["precio"]);
            array_push($resultados,$registro);
        }
    
        return $resultados;
    }

    public function totalFacturadoMesaFechas($mesa, $fechaInicio, $fechaFin)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT SUM(precio) AS total_facturado FROM facturas WHERE idMesa = :mesa AND STR_TO_DATE(fecha, '%Y-%m-%d') >= :fecha_inicio AND STR_TO_DATE(fecha, '%Y-%m-%d') <= :fecha_fin");
        $command->bindParam(':mesa', $mesa);
        $command->bindParam(':fecha_inicio', $fechaInicio);
        $command->bindParam(':fecha_fin', $fechaFin);
        $command->execute();
    
        $resultado = $command->fetch(PDO::FETCH_ASSOC);
    
        return $resultado['total_facturado'];
    }


}


?>