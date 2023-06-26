<?php

require_once "./db/accesoDatos.php";
require_once "./models/mesa.php";

class ControllerMesa extends Mesa
{
    public function darDeAlta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['estado']))
        {
            $status = $parametros['estado'];

            try
            {
                $mesa = new Mesa();
                $mesa->setter($status);
                $mesa->id = $mesa->alta();

                $payload = json_encode(array("Mesa dada de alta con exito! ID" => $mesa->id));

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

    public function statusCerrado($request, $response, $args)
    {
        $params = $request->getParsedBody();

        if(isset($params['id']))
        {
            if(Mesa::buscarId(intval($params['id'])))
            {
                if(Mesa::cambiarEstado($params['id'],'cerrada'))
                {
                    $payload = "La mesa se cerro con exito!";
                }else{
                    $payload = "No pudimos cerrar la mesa!";
                }
            }else{
                $payload = 'No encontramos el id de la mesa';
            }
        }else $payload = 'Ingrese un id para continuar';

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>
