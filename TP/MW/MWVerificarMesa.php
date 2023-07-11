<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/mesa.php';

class MWVerificarMesa
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();

    $param = $request->getParsedBody();

    if (isset($param['idMesa']))
    {
        $idMesa= $param['idMesa'];

        if(Mesa::mesaDisponible($idMesa))
        {
            $response = $handler->handle($request);

        }else $response->getBody()->write(json_encode(array('mensaje'=>'Mesa invalida')));


    }else $response->getBody()->write(json_encode(array('mensaje'=>'Verifique los parametros')));

    return $response;
  }
}