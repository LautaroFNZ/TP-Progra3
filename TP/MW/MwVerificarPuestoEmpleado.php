<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

include_once "./models/empleado.php";

class MWVerificarPuestoEmpleado
{
    public function __invoke(Request $request,RequestHandler $handler):Response
    {
        $response = new Response();
        $params = $request->getParsedBody();

        if(isset($params['puesto']) && !empty($params['puesto']))
        {
            if(Empleado::validarPuesto($params['puesto']))
            {
                $response = $handler->handle($request);
            }else{
                $response->getBody()->write("Ingrese un PUESTO valido para continuar con el alta");
            }
            
        }else{
            $response->getBody()->write("Ingrese un usuario para continuar con el alta.");
        }
            

        return $response;
    }
}

?>