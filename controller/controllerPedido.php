<?php

require_once "./db/accesoDatos.php";
require_once "./models/pedido.php";

class ControllerPedido extends Pedido
{
    public function altaPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['idMesa']))
        {
            $idMesa = $parametros['idMesa'];

            try
            {
                $pedido = new Pedido();
                $pedido->setter($idMesa);
                $pedido->_id = $pedido->alta();

                $payload = json_encode(array("mensaje" => $pedido->_id));

            }
            catch (Exception $e)
            {
                $payload = json_encode(array("mensaje" => $e->getMessage()));
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function listarPedidos($request, $response, $args)
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
