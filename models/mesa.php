<?php

class Mesa
{
    public $_id;
    public $_codigoMesa;
    public $_status;
    
    public function setter($codigoMesa,$status)
    {
        $this->_codigoMesa = $codigoMesa;
        $this->_status = $status;
    } 

    public function alta()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("INSERT INTO mesas (codigo,status) VALUES (:codigo,:status)");
        
        $command->bindValue(':codigo',$this->_codigoMesa,PDO::PARAM_STR);
        $command->bindValue(':status',$this->_status,PDO::PARAM_STR);
        $command->execute();

        return "Di de alta una mesa";
    }

    public function listar()
    {
        $instancia = AccesoDatos::instance();
        $command = $instancia->preparer("SELECT * FROM mesas");
        $command->execute();

        return "Listando todas las mesas";
    }

    
}