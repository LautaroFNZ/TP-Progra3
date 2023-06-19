<?php

require_once "./models/empleado.php";

class ControllerEmpleado extends Empleado
{
    public function altaEmpleado($request,$response,$args)
    {   
        $parametros = $request->getParsedBody();

        if(isset($parametros['nombre']) && isset($parametros['puesto']) && isset($parametros['usuario']) && isset($parametros['password']))
        {
            $nombre = $parametros['nombre'];
            $puesto = $parametros['puesto'];
            $usuarios = $parametros['usuario'];
            $password = $parametros['password'];

            try
            {
                $empleado = new Empleado();
                $empleado->setter($nombre,$puesto,$usuarios,$password);
                $empleado->_id = $empleado->alta();

                if($empleado->_id > 0)
                {
                    $payload = json_encode(array('mensaje'=> 'Empleado dado de alta con exito!'));
                }else{
                    
                    $payload = json_encode(array('mensaje'=> 'Empleado alta'));
                }


            }catch(Exception $e)
            {
                $payload = json_encode(array("mensaje" => $e->getMessage()));
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type','application/json');
        }
    }

    public function listarEmpleados($request,$response,$args)
    {
        try{
            $empleados = Empleado::listar();
            $payload = json_encode(array("listaDeEmpleados" => $empleados));

        }catch(Exception $e)
        {
            $payload = json_encode(array('mensaje'=> $e->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type','application/json');
    }
}

?>