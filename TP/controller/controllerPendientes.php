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

    public function listarPendientesPorUsuario($request, $response, $args)
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
            $pendientes = $pendiente->listarPosUsuario($datos->id);
            
            if($pendientes)
            {
                $payload = json_encode(array("Mostrando la lista de tus pendientes" => $pendientes));

            }else $payload = json_encode(array('mensaje'=>'No tienes pendientes asignados'));
            

        }
        catch (Exception $e)
        {
            $payload = json_encode(array('mensaje' => $e->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    
    public function asignarPendienteEmpleado($request, $response, $args)
    {
        $params = $request->getParsedBody();

        if(isset($params['idPendiente']) && isset($params['tiempoEstimado']))
        {
            $idPendiente = $params['idPendiente'];
            $tiempEstimado = $params['tiempoEstimado'];
            
            $header = $request->getHeaderLine('Authorization');
    
            if ($header != null)
            {
                $token = trim(explode("Bearer", $header)[1]);
                $datos = AutentificadorJWT::ObtenerData($token);

            $pendiente = Pendientes::buscarId($idPendiente);

            if(Producto::traerSector($pendiente->idProducto) == $datos->puesto)
            {
                if(Pendientes::pendienteNoAsignado($idPendiente))
                {

                    if(Pendientes::asignarPendiente($idPendiente,$datos->id,intval($tiempEstimado),$datos->puesto))
                    {
                        $pendienteModificado = Pendientes::buscarId($idPendiente);
                        
                        
                        if(Pedido::asignarTiempoEstimado($pendienteModificado->id,$pendienteModificado->fechaEstimada))
                        {
                            $payload = json_encode(array('mensaje'=>'Pedido asignado con exito!'));
        
                        }else $payload = json_encode(array('mensaje'=>'Error al asignar el tiempo de entrega'));
                        
                        //$payload = json_encode(array('mensaje'=> Pedido::asignarTiempoEstimado($pendienteModificado->id,$pendienteModificado->fechaEstimada)));
                        
                    }else $payload = json_encode(array('mensaje'=>'Error al asignar el pendiente'));

                }else $payload = json_encode(array('mensaje'=>'ERROR: Pendiente ya asignado'));

            }else $payload = json_encode(array('mensaje'=>'ERROR: Este pendiente no corresponde a su sector',));



            }

        }else $payload = json_encode(array('mensaje'=>'Verifique a los parametros'));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function establecerPendienteListo($request, $response, $args)
    {
        $id = $args['idPendiente'];
        $pendientes = Pendientes::buscarId($id);
        if($pendientes)
        {   
            if($pendientes->estado != 'listo para servir')
            {

                $header = $request->getHeaderLine('Authorization');
    
                if ($header != null)
                {
                    $token = trim(explode("Bearer", $header)[1]);
                    $datos = AutentificadorJWT::ObtenerData($token);
    
                }

                if($datos->id == $pendientes->idEmpleado)
                {
                    if($datos->puesto == $pendientes->sector)
                    {
                        if(Pedido::establecerPedidoListoParaServir($pendientes->linkPedido) && Pendientes::pendienteListo($id))
                        {

                            $payload = json_encode(array("mensaje" => 'El pendiente ha sido actualizado al estado de "listo para servir"'));

                        }else $payload = json_encode(array("mensaje" => 'Error al actualizar el estado del pendiente/pedido'));
                        

                     
                    }else $payload = json_encode(array("mensaje" => 'Esta accion solo puede realizarla una persona correspondiente al sector del producto'));
                
                }else $payload = json_encode(array("mensaje" => 'Esta accion solo puede hacerla el empleado a cargo del pendiente'));
                
            }else $payload = json_encode(array("mensaje" => 'El pedido ya fue establecido como listo para servir'));

        }else  $payload = json_encode(array("mensaje" => 'No hemos encontrado el id del pendiente ingresado o el pedido'));

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