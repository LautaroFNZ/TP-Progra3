<?php



class Encuesta
{
    public $id;
    public $puntajeMesa;
    public $puntajeResto;
    public $puntajeMozo;
    public $puntajeCocinero;
    public $puntajePromedio;
    public $nroPedido;
    public $nombreCliente;

    public function setter($puntajeMesa, $puntajeResto, $puntajeMozo, $puntajeCocinero, $nroPedido, $nombreCliente)
    {
        $this->puntajeMesa = $puntajeMesa;
        $this->puntajeResto = $puntajeResto;
        $this->puntajeMozo = $puntajeMozo;
        $this->puntajeCocinero = $puntajeCocinero;
        $this->nroPedido = $nroPedido;
        $this->nombreCliente = $nombreCliente;
        $this->puntajePromedio = (intval($puntajeMesa) + intval($puntajeResto) + intval($puntajeMozo) + intval($puntajeCocinero)) / 4;
    }

    public function alta()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO encuestas (puntajeMesa,puntajeResto,puntajeMozo,puntajeCocinero,puntajePromedio,nroPedido,nombreCliente) VALUES (:puntajeMesa,:puntajeResto,:puntajeMozo,:puntajeCocinero,:puntajePromedio,:nroPedido,:nombreCliente)");
        
        $command->bindValue(':puntajeMesa',intval($this->puntajeMesa));
        $command->bindValue(':puntajeResto',intval($this->puntajeResto));
        $command->bindValue(':puntajeMozo',intval($this->puntajeMozo));
        $command->bindValue(':puntajeCocinero',intval($this->puntajeCocinero));
        $command->bindValue(':puntajePromedio',$this->puntajePromedio);
        $command->bindValue(':nroPedido',intval($this->nroPedido));
        $command->bindValue(':nombreCliente',strtolower($this->nombreCliente));

        $command->execute();

        return $instancia->lastId();
    }

    public function traerEncuestasAltas()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer('SELECT * FROM encuestas WHERE puntajePromedio >= 7');

        $command->execute();
        
        return $command->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    
}

?>