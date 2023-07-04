<?php

require_once "./db/accesoDatos.php";
require_once "./models/pedido.php";
require_once "./models/producto.php";
require_once './models/mesa.php';
require_once './models/pendientes.php';
require_once './controller/controllerPendientes.php';


class ControllerPedido extends Pedido
{
    public function darDeAlta($request, $response, $args)
    {
        $params = $request->getParsedBody();

        if(isset($params['idMesa']) && isset($params['idProductos']))
        {
            $idMesa = $params['idMesa'];
            $retorno = json_decode($this->buscarIdProducto(explode(',',$params['idProductos'])));
            
            if(isset($retorno->lista) && isset($retorno->precio)) 
            {
                $pedido = new Pedido();

                $header = $request->getHeaderLine('Authorization');

                if ($header != null)
                {
                    $token = trim(explode("Bearer", $header)[1]);
                    $datos = AutentificadorJWT::ObtenerData($token);
                    
                    if(Mesa::buscarId($idMesa))
                    {
                        if(Mesa::mesaDisponible($idMesa))
                        {
                            $pedido->setter($idMesa,json_encode($retorno->lista),$datos->usuario,$retorno->precio);
                            Mesa::cambiarEstado($idMesa,"con cliente esperando pedido");
                            $pedido->id = $pedido->alta();
                            $this->agregarPendientes($retorno->lista,$pedido->id,$pedido->fechaEstimada);
                            
                            $payload = json_encode(array('Mensaje'=>'Pedido dato de alta con exito!','Pedido'=>$pedido));

                        }else $payload = json_encode(array('Mensaje'=>'La mesa no esta disponible'));


                    }else $payload = json_encode(array('mensaje'=>'No hemos encontrado la mesa'));
                    
                }
                
            }else $payload = json_decode($retorno);
            

        }else $payload = json_encode(array('mensaje'=>'Verifique los parametros'));


        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function buscarIdProducto($array)
    {       
        $dev = array();
        $precio = 0;

        if($array)
        {
            foreach($array as $idProducto)
            {
                if($pExist = Producto::buscarPorId($idProducto))
                {
                    array_push($dev,array('id'=>$idProducto));
                    $precio += $pExist->precio;
                }
            }
        }

        if(count($array) == count($dev))
        {
            return json_encode(array('lista'=>$dev,'precio'=>$precio));

        }else return json_encode(array('mensaje'=> 'No hemos podido validar todos los ID ingresados'));
    }

    public function agregarPendientes($lista,$nroPedido,$fechaEntregaPedido)
    {
        $retorno = false;
        if($lista)
        {
            foreach($lista as $idPedido)
            {
                $pendientes = new Pendientes();

                $pendientes->setter($idPedido->id,$nroPedido,Producto::traerSector($idPedido->id),$fechaEntregaPedido);
                $pendientes->alta();
                $retorno = true;
            }
        }

        return $retorno;
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
                
                if(ControllerPendientes::verificarEstadoPendientes($idPedido,$pedidos->idProductos))
                {
                    if(Pedido::asignarTiempoEntrega($idPedido,'entregado'))
                    {
                        Mesa::cambiarEstado($idMesa,"con cliente comiendo");
                        $payload = json_encode(array('mensaje'=>'Pedido entregado con exito!'));

                    }else $payload = json_encode(array('mensaje'=>'No se pudo entregar el pedido!'));
                    
            
                }else $payload = json_encode(array('mensaje'=>'Todavia no estan listos todos los productos!'));
                

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

            if($pedido = Pedido::buscarPorId($nroPedido,$idMesa))
            {
                $fechaActual = new DateTime(date('d-m-y H:i:s'));
                $fechaEntregaEstimada = new DateTime($pedido->fechaEstimada);
                
                if ($fechaActual < $fechaEntregaEstimada) {
                    $fechaEstimadaEntrega = $fechaActual->diff($fechaEntregaEstimada);
                    $payload = json_encode(array('El tiempo de espera estimado para la entrega es de:'=> $fechaEstimadaEntrega->format('$h:$i')));
                } else {
                    $payload = json_encode(array('mensaje'=>'Estamos atrasados, disculpe la demora.'));
                }



            }

        }else $payload = json_encode(array('mensaje'=>'conchitaPutita'));


        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
    

}

?>
