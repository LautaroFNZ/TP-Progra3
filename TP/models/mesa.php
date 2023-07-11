<?php



class Mesa
{
    public $id;
    public $status;
    
    public function setter($status)
    {
        $this->status = $status;
    } 

    public function alta()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO mesa (status) VALUES ('cerrada')");
        
        //$command->bindValue(':status',strtolower($this->status),PDO::PARAM_STR);
        $command->execute();

        return $instancia->lastId();
    }

    public function listar()
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM mesa");
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function buscarId($id)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM mesa WHERE id=:id");
        $command->bindValue(':id',$id);
        $command->execute();

        return $command->fetchObject('Mesa');
    }

    public static function mesaDisponible($id)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("SELECT mesa.status FROM mesa WHERE id=:id");
        $command->bindValue(':id',$id);
        $command->execute();

        if($command->fetchColumn() == 'cerrada')
        {
            return true;
            
        }else return false;
    }

    public static function cambiarEstado($id,$estado)
    {
        $instancia = accesoDatos::instance();
        $command = $instancia->preparer("UPDATE mesa SET status = :estado where id=:id");
        $command->bindValue(':id',$id);
        $command->bindValue(':estado',$estado);
        $filasAfectadas = $command->execute();

        return $filasAfectadas > 0;
    } 
    

    public static function validarEstado($estado)
    {   
       return strcasecmp($estado,"con cliente esperando pedido") == 0 || strcasecmp($estado,"con cliente comiendo") == 0 || strcasecmp($estado,"con cliente pagando");    
    }


    
}