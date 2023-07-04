<?php

include_once "./models/encuesta.php";

class ControllerEncuesta extends Encuesta
{
    public function traerEncuestasPositivas($request,$response,$args)
    {
        try
        {
            $encuesta = new Encuesta();
            $encuesta = $encuesta->traerEncuestasAltas();
            
            if($encuesta)
            {
                $payload = json_encode(array("Las encuestas con puntaje promedio de 7 o mas son:" => $encuesta));

            }else $payload = json_encode(array('mensaje'=>"No hay encuestas positivas"));

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