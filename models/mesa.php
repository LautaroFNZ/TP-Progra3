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
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO mesa (status) VALUES (:status)");
        
        $command->bindValue(':status',$this->status,PDO::PARAM_STR);
        $command->execute();

        return $instancia->lastId();
    }

    public function listar()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM mesa");
        $command->execute();

        return $command->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function buscarId($id)
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM mesa WHERE id=:id");
        $command->bindValue(':id',$id);
        $command->execute();

        return $command->fetchObject('Mesa');
    }

    public static function cambiarEstado($id,$estado)
    {
        $instancia = AccesoDatos::instance();
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