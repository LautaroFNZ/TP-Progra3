<?php

include_once "./db/accesoDatos.php";
include_once "./models/factura.php";

class ControllerFactura extends Factura
{
    public function listarMesasOrdFact($request, $response, $args)
    {
        try
        {
            $factura = new Factura();
            $facturas = $factura->listarMesasOrdenadasPorFactura();
            $payload = json_encode(array("Mesas ordenadas desde la que mas facturo hasta la que menos." => $facturas));

        }
        catch (Exception $e)
        {
            $payload = json_encode(array('mensaje' => $e->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function traerFacturadoMesaEnFechas($request, $response, $args)
    {
        $params = $request->getParsedBody();

        if(isset($params['idMesa']) && isset($params['fecha1']) && isset($params['fecha2']))
        {
            $fecha1 = $params['fecha1'];
            $fecha2 = $params['fecha2'];
            $idMesa = $params['idMesa'];

            $factura = new Factura();

            $total = $factura->totalFacturadoMesaFechas($idMesa,$fecha1,$fecha2);
            if($total >0)
            {
                $payload = json_encode(array('Entre esas fechas, la mesa facturo' =>$total));

            }else $payload = json_encode(array('mensaje' =>'No hay registros en esas fechas'));



        }else $payload = json_encode(array('mensaje' =>'Verifique los parametros'));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>
