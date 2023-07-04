<?php
class accesoDatos
{
    private static $ObjetoaccesoDatos;
    private $objetoPDO;
 
    private function __construct()
    {
        try { 
            $this->objetoPDO = new PDO('mysql:host=localhost;dbname=tp-comanda;charset=utf8', 'root', '', array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->objetoPDO->exec("SET CHARACTER SET utf8");
            } 
        catch (PDOException $e) { 
            print "Error!: " . $e->getMessage(); 
            die();
        }
    }
 
    public function preparer($sql)
    { 
        return $this->objetoPDO->prepare($sql); 
    }
     public function lastId()
    { 
        return $this->objetoPDO->lastInsertId(); 
    }
 
    public static function instance()
    { 
        if (!isset(self::$ObjetoaccesoDatos)) {          
            self::$ObjetoaccesoDatos = new accesoDatos(); 
        } 
        return self::$ObjetoaccesoDatos;        
    }
 
 
     // Evita que el objeto se pueda clonar
    public function __clone()
    { 
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR); 
    }
}
?>