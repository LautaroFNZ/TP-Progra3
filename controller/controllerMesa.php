<?php

require_once "./db/accesoDatos.php";
require_once "./models/mesa.php";

class ControllerMesa extends Mesa
{
    public function altaMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['codigo']) && isset($parametros['status']))
        {
            $codigoMesa = $parametros['codigo'];
            $status = $parametros['status'];

            try
            {
                $mesa = new Mesa();
                $mesa->setter($codigoMesa,$status);
                $mesa->_id = $mesa->alta();

                $payload = json_encode(array("mensaje" => $mesa->_id));

            }
            catch (Exception $e)
            {
                $payload = json_encode(array("mensaje" => $e->getMessage()));
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function listarMesas($request, $response, $args)
    {
        try
        {
            $mesa = new Mesa();
            $mesas = $mesa->listar();
            $payload = json_encode(array("listaDeMesas" => $mesas));

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
