<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "./models/producto.php";

class MWVerificarProductoExistente
{
    private $esVenta;

    public function __construct($esVenta)
    {
        $this->esVenta = $esVenta;
    }


    public function __invoke(Request $request,RequestHandler $handler):Response
    {
        $response = new Response();
        $params = $request->getParsedBody();

        if(isset($params['nombre']) && !empty($params['nombre']) && isset($params['tipo']) && !empty($params['tipo']) )
        {
            $producto = new Producto();
            $nombre = $params['nombre'];
            $tipoProducto = $params['tipo'];

            if($producto->productoExiste($nombre,$tipoProducto))
            {
                if($this->esVenta)
                {
                    $response = $handler->handle($request);
                }else{
                    $response->getBody()->write("El producto ya fue dado de alta!");

                }
                
            }else{
                if($this->esVenta)
                {
                    $response->getBody()->write("El producto no existe!");
                }else{
                    $response = $handler->handle($request);
                }
            }
            
        }else{
            $response->getBody()->write("Ingrese un usuario para continuar con el alta.");
        }
            

        return $response;
    }
}

?>