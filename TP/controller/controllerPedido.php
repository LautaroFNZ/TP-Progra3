<?php

include_once "./db/accesoDatos.php";
include_once "./models/pedido.php";
include_once "./models/producto.php";
include_once './models/mesa.php';
include_once './models/pendientes.php';
include_once './models/factura.php';
include_once './models/encuesta.php';
include_once './controller/controllerPendientes.php';


class ControllerPedido extends Pedido
{
    public function darDeAlta($request, $response, $args)
    {
        $params = $request->getParsedBody();

        if(isset($params['idMesa']) && isset($params['idProductos']))
        {
            $idMesa = $params['idMesa'];
            $idProductos = explode(',',$params['idProductos']);

            $header = $request->getHeaderLine('Authorization');

            if ($header != null)
            {
                $token = trim(explode("Bearer", $header)[1]);
                $datos = AutentificadorJWT::ObtenerData($token);

                $cantidadId = 0;

                $nroPedido = Pedido::generadorId(5);
                $idNoEncontradas = array();
                
                if($datos->puesto == 'mozo')
                {
                    foreach($idProductos as $id)
                    {
                        if($producto = Producto::buscarPorId($id))
                        {
                            $linkPendiente = Pedido::generadorId(3);
                            $pedido = new Pedido();
                            
                            $pedido->setter($idMesa,$nroPedido,$producto->id,$datos->id,$producto->precio,$linkPendiente);
                            $pedido->alta();
                            $pendiente = new Pendientes();
                            
                            $pendiente->setter($producto->id,$nroPedido,Producto::traerSector($producto->id),$linkPendiente);
                            $pendiente->alta();
                            
                            Mesa::cambiarEstado($idMesa,'con cliente esperando pedido');
                            $cantidadId += 1;
                            
                        }else array_push($idNoEncontradas,$id);
                    }
                    
                    if(count($idProductos) == $cantidadId)
                    {
                        $payload = json_encode(array('mensaje'=>'Hemos generado los pedidos','Su numero de pedido es'=> $nroPedido));
                    }else{
                        $payload = json_encode(array('mensaje'=>'No hemos podido dar de alta los siguientes productos','Lista'=> $idNoEncontradas));
                    }

                }else $payload = json_encode(array('mensaje'=>'Accion solo valida para mozos.'));
            }
            
        }else $payload = json_encode(array('mensaje'=>'Verifique los parametros'));

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function entregarPedido($request, $response, $args)
    {
        $params = $request->getParsedBody();

        if(isset($params['idMesa']) && isset($params['idPedido']))
        {
            $idMesa = $params['idMesa'];
            $idPedido = $params['idPedido'];

            $pedidos = Pedido::buscarPorId($idPedido,$idMesa);

            if($pedidos && Mesa::buscarId($idMesa))
            { 
                
                if($pedidos->estadoPedido != 'entregado')
                {
                    if(Pedido::asignarTiempoEntrega($idPedido,'entregado'))
                    {
                        Mesa::cambiarEstado($idMesa,"con cliente comiendo");
                        $payload = json_encode(array('mensaje'=>'Pedido entregado con exito!'));

                    }else $payload = json_encode(array('mensaje'=>'No se pudo entregar el pedido!'));
                    
            
                }else $payload = json_encode(array('mensaje'=>'El pedido ya fue entregado'));
                

            }else $payload = json_encode(array('mensaje'=>'ERROR al verificar los datos')); 

        }else $payload = json_encode(array('mensaje'=>'Verifique los parametros')); 
    
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function listarPedidos($request, $response, $args)
    {
        try
        {
            $pedido = new Pedido();
            $pedido = $pedido->listar();
            $payload = json_encode(array("listaPedidos" => $pedido));

        }
        catch (Exception $e)
        {
            $payload = json_encode(array('mensaje' => $e->getMessage()));
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function clienteVerificaEstado($request, $response, $args)
    {
        $params = $request->getParsedBody();

        if(isset($params['idMesa']) && isset($params['nroPedido']))
        {
            $nroPedido = $params['nroPedido'];
            $idMesa = $params['idMesa'];

            if($pedidos = Pedido::buscarPorNroPedido($nroPedido,$idMesa))
            {
                $tiempoEstimadoProductos = array();
                foreach($pedidos as $pedido)
                {
                    if($pedido->fechaEstimada != '')
                    {

                        $fechaActual = new DateTime(date('d-m-y H:i:s'));   
                        $fechaEntregaEstimada = new DateTime($pedido->fechaEstimada);
                        
                        if ($fechaActual < $fechaEntregaEstimada)
                        {
                            $intervalo = $fechaActual->diff($fechaEntregaEstimada);
                            $diferenciaEnMinutos = $intervalo->i;
        
                            $registro = array('idProducto'=>$pedido->idProducto,'Aproximadamente'=>$diferenciaEnMinutos . ' minutos');
                            array_push($tiempoEstimadoProductos,$registro);
        
                        }else {
                            $registro = array('idProducto'=> $pedido->idProducto,'mensaje'=>'Estamos atrasados, disculpe la demora.');
                            array_push($tiempoEstimadoProductos,$registro);
                        }
                    }
                }

                $payload = json_encode(array('mensaje'=>$tiempoEstimadoProductos));
            
            }else $payload = json_encode(array('mensaje'=>'No hemos encontrado su pedido'));

        }else $payload = json_encode(array('mensaje'=>'Verifique los parametros'));


        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function cobrarPedido($request, $response, $args)
    {
        $params = $request->getParsedBody();

        if(isset($params['nroPedido']) && isset($params['idMesa']) && isset($params['puntajeMesa']) && isset($params['puntajeResto']) && isset($params['puntajeCocinero']) &&  isset($params['puntajeMozo']) && isset($params['nombreCliente']) && isset($params['comentarios']))
        {

            $nroPedido = $params['nroPedido'];
            $idMesa = $params['idMesa'];
            $puntajeMesa = $params['puntajeMesa'];
            $puntajeResto = $params['puntajeResto'];
            $puntajeCocinero = $params['puntajeCocinero'];
            $puntajeMozo = $params['puntajeMozo'];
            $nombreCliente = $params['nombreCliente'];
            $comentarios = $params['comentarios'];

            if($pedidos = Pedido::buscarPorNroPedido($nroPedido,$idMesa))
            {   
                $precioTotal = 0;
                $contador = 0;

                foreach($pedidos as $pedido)
                {
                    if($pedido->estadoPedido == 'entregado')
                    {
                        $precioTotal += $pedido->precio;
                        $contador++;
                    }

                    if($contador == count($pedidos))
                    {
                        $payload = json_encode(array('mensaje'=> 'Todos los pedidos fueron entregados','precio'=>$precioTotal));
                        if(strlen($comentarios)<66)
                        {
                            if(Encuesta::validarPuntajes($puntajeMesa,$puntajeResto,$puntajeMozo,$puntajeCocinero))
                            {   
                                Pedido::modificarEstadoPedido($nroPedido,'cobrado');
                                Mesa::cambiarEstado($idMesa,'con cliente pagando');
                                
                                $factura = new Factura();
                                $factura->setter($precioTotal,$nroPedido,$idMesa,$nombreCliente);
                                $factura->id = $factura->alta();
                            
                                $encuesta = new Encuesta();
                                $encuesta->setter($puntajeMesa,$puntajeResto,$puntajeMozo,$puntajeCocinero,$nroPedido,$nombreCliente,$comentarios);
                                $encuesta->id = $encuesta->alta();
                
                                $payload = json_encode(array('mensaje'=>'Pedido cobrado con exito! Vuelva pronto'));//,'Factura generada'=>$factura,'Encuesta generada'=>$encuesta));
                            }else $payload = json_encode(array('mensaje'=> 'Verifique que todos los puntajes sean menor o igual a 10'));

                        }else $payload = json_encode(array('mensaje'=> 'Ingrese un comentario menor a 66 caracteres para continuar','Caracteres actuales'=>strlen($comentarios)));

                    }else $payload = json_encode(array('mensaje'=> 'No fueron entregados todos los pedidos'));
                    
                }
                
            }else $payload = json_encode(array('mensaje'=>'El pedido no fue encontrado'));

        }else $payload = json_encode(array('mensaje'=>'Verifique los parametros'));

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function traerMesaRepetida($request, $response, $args)
    {
        try
        {
            $pedido = new Pedido();
            $pedido = $pedido->traerIdMesaMasRepetido();
            if($pedido)
            {
                $payload = json_encode(array("La mesa mas usada fue la numero:" => $pedido));

            }else $payload = json_encode(array('mensaje'=>"No hay una mesa que destaque sobre el resto"));

        }
        catch (Exception $e)
        {
            $payload = json_encode(array('mensaje' => $e->getMessage()));
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');

    }

    public function traerEntregasFueraTiempo($request, $response, $args)
    {
        try
        {
            $pedido = new Pedido();
            $pedido = $pedido->entregasFueraTiempo();
            if($pedido)
            {
                $payload = json_encode(array("Estos fueron los pedidos entregados fuera de termino:" => $pedido));

            }else $payload = json_encode(array('mensaje'=>"Todos los pedidos fueron entregados a termino"));

        }
        catch (Exception $e)
        {
            $payload = json_encode(array('mensaje' => $e->getMessage()));
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function mostrarListosParaEntregar($request, $response, $args)
    {
        try
        {
            if($pedidos = Pedido::listarPedidosListos())
            {
                $payload = json_encode(array("listaPedidos" => $pedidos));

            }else $payload = json_encode(array("mensaje" =>'No hay pendientes listos para entregar!'));

        }
        catch (Exception $e)
        {
            $payload = json_encode(array('mensaje' => $e->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');

    }

    public function mostrarProductosMasVendidos($request, $response, $args)
    {
        try
        {
            if($pedidos = Pedido::obtenerProductosMasVendidos())
            {
                $payload = json_encode(array("Productos mas vendidos" => $pedidos));

            }else $payload = json_encode(array("mensaje" =>'No se vendieron productos'));

        }
        catch (Exception $e)
        {
            $payload = json_encode(array('mensaje' => $e->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    
    

}

?>
