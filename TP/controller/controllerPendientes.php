<?php

include_once "./db/accesoDatos.php";
include_once "./models/pedido.php";
include_once "./models/pendientes.php";

class ControllerPendientes extends Pendientes
{
    
    public function listarTodos($request, $response, $args)
    {
        try
        {
            $pendiente = new Pendientes();
            $pendientes = $pendiente->listar();
            $payload = json_encode(array("listaPendientes" => $pendientes));

        }
        catch (Exception $e)
        {
            $payload = json_encode(array('mensaje' => $e->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    

    public function listarPendientesPorSector($request, $response, $args)
    {
        try
        {
            $header = $request->getHeaderLine('Authorization');

            if ($header != null)
            {
                $token = trim(explode("Bearer", $header)[1]);
                $datos = AutentificadorJWT::ObtenerData($token);
                //$datos->puesto = 'cocinero';
            }

            $pendiente = new Pendientes();
            $pendientes = $pendiente->listarPorSector($datos->puesto);
            
            if($pendientes)
            {
                $payload = json_encode(array("Mostrando la lista de pendientes en preparacion asignados a su sector" => $pendientes));

            }else $payload = json_encode(array('mensaje'=>'No hay pendientes en su sector'));
            

        }
        catch (Exception $e)
        {
            $payload = json_encode(array('mensaje' => $e->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function establecerPendienteListo($request, $response, $args)
    {
        $id = $args['id'];
        $pendientes = Pendientes::buscarId($id);
        if($pendientes)
        {   
            $header = $request->getHeaderLine('Authorization');

            if ($header != null)
            {
                $token = trim(explode("Bearer", $header)[1]);
                $datos = AutentificadorJWT::ObtenerData($token);
                //$datos->puesto = 'cocinero';
            }
            
            if($datos->puesto == $pendientes->sector)
            {
                Pendientes::pendienteListo($id);
                $payload = json_encode(array("mensaje" => 'El pendiente ha sido actualizado al estado de "listo"'));
             
            }else $payload = json_encode(array("mensaje" => 'Esta accion solo puede realizarla una persona correspondiente al sector del producto'));


           

        }else  $payload = json_encode(array("mensaje" => 'No hemos encontrado el id del pendiente ingresado'));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function verificarEstadoPendientes($nroPedido,$idProductos)
    {   
        $retorno = false;
        $respuestas = array();
        $ids = json_decode($idProductos);
        
        
        if($ids)
        {
            foreach($ids as $id)
            {
                $estado = Pendientes::consultarEstado($nroPedido,$id->id);
                
                
                if($estado == "listo")
                {
                    array_push($respuestas,$estado);                    
                }
                
            }

            if(count($ids) == count($respuestas))
            {
                $retorno = true;
            }
        }
        

        return $retorno;
    }
}

?>