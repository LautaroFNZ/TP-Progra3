<?php




class Factura
{
    public $id;
    public $precio;
    public $nroPedido;
    public $idMesa;
    public $nombreCliente;

    public function setter($precio, $nroPedido, $idMesa, $nombreCliente)
    {
        $this->precio = $precio;
        $this->nroPedido = $nroPedido;
        $this->idMesa = $idMesa;
        $this->nombreCliente = $nombreCliente;
    }

    public function alta()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO facturas (precio,nroPedido,idMesa,nombreCliente) VALUES (:precio,:nroPedido,:idMesa,:nombreCliente)");

        $command->bindValue(':precio',$this->precio);
        $command->bindValue(':nroPedido',$this->nroPedido);
        $command->bindValue(':idMesa',$this->idMesa);
        $command->bindValue(':nombreCliente',strtolower($this->nombreCliente));
        $command->execute();
        
        return $instancia->lastId();
    }


}


?>