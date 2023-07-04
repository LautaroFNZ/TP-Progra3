<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/vendor/autoload.php';

//require controllers
require_once './controller/controllerEmpleado.php';
require_once './controller/controllerMesa.php';
require_once './controller/controllerPedido.php';
require_once './controller/controllerProducto.php';
require_once './controller/controllerUsuario.php';
require_once './controller/controllerPendientes.php';

//require mw
require_once './MW/MWVerificarUsuarioEmpleado.php';
require_once './MW/MWVerificarPuestoEmpleado.php';
require_once './MW/MWLogin.php';
require_once './MW/MWVerificarTokenValido.php';


require_once './MW/MWVerificarSocio.php';


//require utilidades
require_once './utilidades/jwt.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

date_default_timezone_set("America/Argentina/Buenos_Aires");



// Routes

//Login empleado

$app->post('/login', \ControllerUsuario::class .':login')->add(new MWLogin());

$app->group('/usuarios', function (RouteCollectorProxy $group){
    $group->get('[/]', \ControllerUsuario::class . ':listarFechaLogin');
    $group->get('/guardarUsuarios', \ControllerUsuario::class . ':guardarEnCsv');
    $group->get('/leerUsuarios', \ControllerUsuario::class . ':leerUsuariosCsv');
})->add(new MWVerificarSocio());

    


//Manejo de Empleados
$app->group('/empleado', function (RouteCollectorProxy $group){
    $group->post('/darDeAlta', \ControllerEmpleado::class . ':darDeAlta')
    ->add(new MWVerificarUsuarioEmpleado())
    ->add(new MWVerificarPuestoEmpleado());
    $group->get('[/]', \ControllerEmpleado::class . ':listarTodos');
})->add(new MWVerificarSocio());

//Manejo de Mesas
$app->group('/mesa', function (RouteCollectorProxy $group){
    $group->post('/darDeAlta', \ControllerMesa::class . ':darDeAlta');
    $group->post('/modificarEstado', \ControllerMesa::class . ':modificarEstado');
    $group->get('[/]', \ControllerMesa::class . ':listarTodos');  
    $group->post('/cerrar', \ControllerMesa::class . ':statusCerrado')->add(new MWVerificarSocio());
    $group->get('/test/{id}', \ControllerMesa::class . ':mesaEstaDisponible');  
})->add(new MWVerificarTokenValido());

//Manejo de Pedidos
$app->group('/pedido', function (RouteCollectorProxy $group){
    $group->post('/darDeAlta', \ControllerPedido::class . ':darDeAlta');
    $group->post('/entregarPedido', \ControllerPedido::class . ':entregarPedido');
    $group->get('/listarPedidos', \ControllerPedido::class . ':listarPedidos');
})->add(new MWVerificarTokenValido());;

$app->group('/cliente',function (RouteCollectorProxy $group)
{
    $group->post('/estadoPedido', \ControllerPedido::class . ':clienteVerificaEstado');
});

$app->group('/pendientes', function (RouteCollectorProxy $group){
    $group->get('[/]', \ControllerPendientes::class . ':listarTodos');
    $group->get('/sector', \ControllerPendientes::class . ':listarPendientesPorSector');
    $group->get('/establecerListo/{id}',\ControllerPendientes::class . ':establecerPendienteListo');
})->add(new MWVerificarTokenValido());;

//Manejo de Productos
$app->group('/producto', function (RouteCollectorProxy $group){
    $group->post('/darDeAlta', \ControllerProducto::class . ':darDeAlta');
    $group->get('[/]', \ControllerProducto::class . ':listarTodos');
})->add(new MWVerificarTokenValido());;





//Default
$app->post('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array('method' => 'POST', 'msg' => "Bienvenido a TP-LaComanda 2023"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});


$app->run();
