<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/vendor/autoload.php';

require_once './controller/controllerEmpleado.php';
require_once './controller/controllerMesa.php';
require_once './controller/controllerPedido.php';
require_once './controller/controllerProducto.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

$app->group('/empleado', function (RouteCollectorProxy $group){
    $group->post('/altaEmpleado', \ControllerEmpleado::class . ':altaEmpleado');
    $group->get('[/]', \ControllerEmpleado::class . ':listarEmpleados');  
});

$app->group('/mesa', function (RouteCollectorProxy $group){
    $group->post('/altaMesa', \ControllerMesa::class . ':altaMesa');
    $group->get('[/]', \ControllerMesa::class . ':listarMesas');  
});

$app->group('/pedido', function (RouteCollectorProxy $group){
    $group->post('/altaPedido', \ControllerPedido::class . ':altaPedido');
    $group->get('[/]', \ControllerPedido::class . ':listarPedidos');  
});

$app->group('/producto', function (RouteCollectorProxy $group){
    $group->post('/altaProducto', \ControllerProducto::class . ':altaProducto');
    $group->get('[/]', \ControllerProducto::class . ':listarProductos');
});



$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("<h1>This is a test brother");
    return $response;
});

$app->post('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array('method' => 'POST', 'msg' => "Bienvenido a SlimFramework 2023"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/test', function (Request $request, Response $response) {    
    $payload = json_encode(array('aca le estoy pegando a tu vieja'));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
