<?php



class Pedido
{
    public $id;
    public $nroPedido;
    public $linkPendiente;
    public $idMesa;
    public $idProducto;
    public $precio;
    public $idUsuario;
    public $estadoPedido;
    public $fechaEntrega;
    public $fechaEstimada;

    public function setter($idmesa,$nroPedido,$idProducto,$idUsuario,$precio,$linkPendiente)
    {
        $this->idMesa = $idmesa;
        $this->nroPedido = $nroPedido;
        $this->idProducto = $idProducto;
        $this->idUsuario = $idUsuario;
        $this->precio = $precio;
        $this->linkPendiente = $linkPendiente;
        $this->estadoPedido = 'tomado';
        $this->fechaEntrega = '';
        $this->fechaEstimada = '';
    }

    public function alta()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO pedidos (nroPedido,idMesa,idProducto,precio,idUsuario,estadoPedido,fechaEntrega,fechaEstimada,linkPendiente) VALUES (:nroPedido,:idMesa,:idProducto,:precio,:idUsuario,:estadoPedido,:fechaEntrega,:fechaEstimada,:linkPendiente)");
        
        
        $command->bindValue(':nroPedido',$this->nroPedido,PDO::PARAM_STR);
        $command->bindValue(':idMesa',intval($this->idMesa),PDO::PARAM_STR);
        $command->bindValue(':idProducto',intval($this->idProducto),PDO::PARAM_STR);
        $command->bindValue(':precio',$this->precio,PDO::PARAM_STR);
        $command->bindValue(':idUsuario',intval($this->idUsuario),PDO::PARAM_STR);
        $command->bindValue(':estadoPedido',strtolower($this->estadoPedido),PDO::PARAM_STR);
        $command->bindValue(':fechaEntrega',$this->fechaEntrega,PDO::PARAM_STR);
        $command->bindValue(':linkPendiente',$this->linkPendiente,PDO::PARAM_STR);
        $command->bindValue(':fechaEstimada',$this->fechaEstimada,PDO::PARAM_STR);
        
        $command->execute();

        return $instancia->lastId();
    }

    public function listar()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pedidos");
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function buscarPorId($id,$idMesa)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pedidos WHERE id = :id AND idMesa = :idMesa");

        $command->bindValue(':id',$id);
        $command->bindValue(':idMesa',$idMesa);
        $command->execute();

        return $command->fetchObject('Pedido');
    }

    public static function buscarPorNroPedido($nroPedido,$idMesa)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pedidos WHERE nroPedido = :nroPedido AND idMesa = :idMesa");

        $command->bindValue(':nroPedido',$nroPedido);
        $command->bindValue(':idMesa',$idMesa);
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function buscarPorLink($id,$idMesa,$link)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pedidos WHERE nroPedido = :id AND idMesa = :idMesa AND linkPendiente = :link");

        $command->bindValue(':id',$id);
        $command->bindValue(':idMesa',$idMesa);
        $command->bindValue(':link',$link);
        $command->execute();

        return $command->fetchObject('Pedido');
    }

    public static function asignarTiempoEstimado($id,$tiempo)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("UPDATE pedidos SET fechaEstimada = :fechaEstimada, estadoPedido = 'nuevo' WHERE id = :id");

        $command->bindValue(':fechaEstimada',$tiempo,PDO::PARAM_STR);
        $command->bindValue(':id',intval($id));
        $filasAfectadas = $command->execute();

        return $filasAfectadas > 0;
    
    }

    public static function asignarTiempoEntrega($id,$estadoPedido)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("UPDATE pedidos SET fechaEntrega = :fechaEntrega, estadoPedido = :estadoPedido WHERE estadoPedido <> 'entregado' AND id = :id");
    

        $command->bindValue(':id',$id);
        $command->bindValue(':fechaEntrega',date('d-m-y H:i:s'));
        $command->bindValue(':estadoPedido',strtolower($estadoPedido));
        $filasAfectadas = $command->execute();

        return $filasAfectadas > 0;
    }

    public static function modificarEstadoPedido($nroPedido,$estadoPedido)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("UPDATE pedidos SET estadoPedido = :estadoPedido WHERE nroPedido = :nroPedido");

        $command->bindValue(':nroPedido',$nroPedido);
        $command->bindValue(':estadoPedido',strtolower($estadoPedido));
        
        $filasAfectadas = $command->execute();

        return $filasAfectadas > 0;
    }

    public static function establecerPedidoListoParaServir($linkPendiente)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("UPDATE pedidos SET estadoPedido = 'listo para servir' WHERE linkPendiente = :linkPendiente");

        $command->bindValue(':linkPendiente',strtolower($linkPendiente));
        
        $filasAfectadas = $command->execute();

        return $filasAfectadas > 0;
    }

    public function traerIdMesaMasRepetido()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT idMesa FROM pedidos GROUP BY idMesa ORDER BY COUNT(*) DESC LIMIT 1");
        
        $command->execute();
        
        $row = $command->fetch(PDO::FETCH_ASSOC);
        return $row['idMesa'];
    }

    public function entregasFueraTiempo()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pedidos WHERE estadoPedido IN ('entregado', 'cobrado') AND STR_TO_DATE(fechaEntrega, '%d-%m-%y %H:%i:%s') > STR_TO_DATE(fechaEstimada, '%d-%m-%y %H:%i:%s');        ");

        $command->execute();


        return $command->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function listarPedidosListos()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM pedidos WHERE estadoPedido = 'listo para servir'");
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function generadorId($longitud) 
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyz';
        $id = '';
      
        for ($i = 0; $i < $longitud; $i++) {
          $indice = rand(0, strlen($caracteres) - 1);
          $id .= $caracteres[$indice];
        }
      
        return $id;
    }

    public static function obtenerProductosMasVendidos()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT p.nombre AS nombre_producto, COUNT(*) AS cantidad_vendida FROM pedidos pd JOIN productos p ON pd.idProducto = p.id WHERE pd.estadoPedido = 'cobrado' GROUP BY pd.idProducto ORDER BY cantidad_vendida DESC;");
        $command->execute();
    
        $resultados = array();
    
        while ($fila = $command->fetch(PDO::FETCH_ASSOC)) {
            $item = array(
                "nombre_producto" => $fila["nombre_producto"],
                "cantidad_vendida" => $fila["cantidad_vendida"]
            );
    
            $resultados[] = $item;
        }
    
        return $resultados;
    }
      


}