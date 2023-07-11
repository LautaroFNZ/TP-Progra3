<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

include_once "./models/pendientes.php";

class MWVerificarIdPendiente
{
    public function __invoke(Request $request,RequestHandler $handler):Response
    {
        $response = new Response();
        $params = $request->getParsedBody();

        if(isset($params['idPendiente']))
        {
            $id = $params['idPendiente'];

            if(Pendientes::buscarId($id))
            {
                $response = $handler->handle($request);

            }else $response->getBody()->write(json_encode(array('mensaje'=>'No encontramos ese ID del pendiente')));


            
        }else $response->getBody()->write(json_encode(array('mensaje'=>'Verifique los parametros')));

        return $response;
    }

}

?>