<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "./models/empleado.php";

class MWVerificarUsuarioEmpleado
{
    public function __invoke(Request $request,RequestHandler $handler):Response
    {
        $response = new Response();
        $params = $request->getParsedBody();

        if(isset($params['usuario']))
        {

            $respuesta = Empleado::verificarUsuario($params['usuario']);
            
            if(!$respuesta)
            {
                $response = $handler->handle($request);
            }else{
                $response->getBody()->write("El usuario ya existe, por favor ingrese uno nuevo!");
            }
        }else{
            $response->getBody()->write("Ingrese un usuario para continuar con el alta.");
        }
            

        return $response;
    }
}

?>