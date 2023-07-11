<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MWVerificarMozo
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

        if($datos->puesto == 'mozo')
        {
          $esValido = true;
        }
      }
    
      
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    if($esValido) 
    {

      $response = $handler->handle($request);
      
    }else $payload = json_encode(array('Error'=> 'Accion valida solo para mozos'));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}