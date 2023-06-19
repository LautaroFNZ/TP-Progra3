<?php

require_once "./db/accesoDatos.php";
require_once "./models/producto.php";

class ControllerProducto extends Producto
{
    public function altaProducto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['nombre']) && isset($parametros['tipo']) && isset($parametros['cantidad']) && isset($parametros['precio']))
        {
            $nombre = $parametros['nombre'];
            $tipoProducto = $parametros['tipo'];
            $cantidad = $parametros['cantidad'];
            $precio = $parametros['precio'];

            try
            {
                $producto = new Producto();
                $producto->setter($nombre,$tipoProducto,$cantidad,$precio);
                $producto->_id = $producto->alta();

                $payload = json_encode(array("mensaje" => $producto->_id));

            }
            catch (Exception $e)
            {
                $payload = json_encode(array("mensaje" => $e->getMessage()));
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function listarProductos($request, $response, $args)
    {
        try
        {
            $producto = new Producto();
            $productos = $producto->listar();
            $payload = json_encode(array("listaDeProductos" => $productos));

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
