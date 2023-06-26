<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MWVerificarSocio
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    $esValido = false;
    $payload = "";
    $token = "";

    try {
      $header = $request->getHeaderLine('Authorization');

      if ($header != null)
      {
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);

        //$response->getBody()->write($datos['puesto']);
        if($datos->puesto == 'socio')
        {
            $esValido = true;
        }
      }
    
      
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    if ($esValido) {
      $response = $handler->handle($request);
    }else $payload = json_encode(array('Error:'=> 'Accion solo valida para socios administradores'));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}