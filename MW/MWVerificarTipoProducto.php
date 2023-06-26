<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "./models/producto.php";

class MWVerificarTipoProducto
{
    public function __invoke(Request $request,RequestHandler $handler):Response
    {
        $response = new Response();
        $params = $request->getParsedBody();

        if(isset($params['tipo']) && !empty($params['tipo']))
        {
            if(Producto::verificarTipo($params['tipo']))
            {
                $response = $handler->handle($request);
            }else{
                $response->getBody()->write("Ingrese un TIPO de producto valido");
            }
            
        }else{
            $response->getBody()->write("Ingrese un usuario para continuar con el alta.");
        }
            

        return $response;
    }
}

?>