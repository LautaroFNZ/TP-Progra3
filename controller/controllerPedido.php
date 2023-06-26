<?php

require_once "./db/accesoDatos.php";
require_once "./models/pedido.php";
require_once './models/mesa.php';

class ControllerPedido extends Pedido
{
    public function darDeAlta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['idMesa']) && isset($parametros['nombreProducto']) && isset($parametros['tipoProducto']))
        {
            $idMesa = $parametros['idMesa'];
            $nombreProducto = $parametros['nombreProducto'];
            $tipoProducto = $parametros['tipoProducto'];
            //$usuarioVenta = $parametros['usuarioVenta'];

            try
            {
                $header = $request->getHeaderLine('Authorization');

                if ($header != null)
                {
                    $token = trim(explode("Bearer", $header)[1]);
                    $datos = AutentificadorJWT::ObtenerData($token);

                    //$response->getBody()->write($datos['puesto']);
                    if($datos->id)
                    {   
                        if(Mesa::buscarId($idMesa))
                        {
                            $pedido = new Pedido();
                            $pedido->setter($idMesa,$nombreProducto,$tipoProducto,$datos->id);
                            Mesa::cambiarEstado($idMesa,'cliente esperando pedido');

                            $pedido->id = $pedido->alta();

                            $payload = json_encode(array("Pedido generado!" => $pedido->id));

                        }else $payload = json_encode(array('mensaje'=>'Mesa no encontrada!'));

                        

                    }else{
                        $payload = 'No encontramos el usuario del pedido';
                    }
                }

                

            }
            catch (Exception $e)
            {
                $payload = json_encode(array("mensaje" => $e->getMessage()));
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function listarTodos($request, $response, $args)
    {
        try
        {
            $pedido = new Pedido();
            $pedidos = $pedido->listar();
            $payload = json_encode(array("listaDePedidos" => $pedidos));

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
