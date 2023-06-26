<?php

require_once "./db/accesoDatos.php";
require_once "./models/producto.php";

class ControllerProducto extends Producto implements IApiUsable
{
    public function darDeAlta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['nombre']) && isset($parametros['tipo']) && isset($parametros['precio']))
        {
            $nombre = $parametros['nombre'];
            $tipoProducto = $parametros['tipo'];
            $precio = $parametros['precio'];

            try
            {
                $producto = new Producto();
                $producto->setter($nombre,$tipoProducto,$precio);
                $producto->id = $producto->alta();

                if($producto->id>0)
                {
                    $payload = json_encode(array('mensaje'=> 'Producto dado de alta con exito!'));
                }else{
                    $payload = json_encode(array('mensaje'=> 'Error al dar de alta un producto!'));
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
