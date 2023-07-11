<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MWBuscarUsuario
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();

    $parametros = $request->getParsedBody();

    if (isset($parametros['usuario']) && !empty($parametros['usuario']))
    {
        $response = $handler->handle($request);
        
    }else $response->getBody()->write("Verifique sus credenciales");

    return $response;
  }
}