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
include_once './controller/controllerEmpleado.php';
include_once './controller/controllerMesa.php';
include_once './controller/controllerPedido.php';
include_once './controller/controllerProducto.php';
include_once './controller/controllerUsuario.php';
include_once './controller/controllerPendientes.php';
include_once './controller/controllerEncuestas.php';
include_once './controller/controllerFactura.php';

//require mw
include_once './MW/MWVerificarUsuarioEmpleado.php';
include_once './MW/MWVerificarPuestoEmpleado.php';
include_once './MW/MWLogin.php';
include_once './MW/MWVerificarTokenValido.php';
include_once './MW/MWVerificarMesa.php';
include_once './MW/MWVerificarIdPendiente.php';


include_once './MW/MWVerificarSocio.php';
include_once './MW/MWVerificarMozo.php';

include_once './MW/MWBuscarUsuario.php';


//require utilidades
include_once './utilidades/jwt.php';

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
    $group->get('/descargarLogo', \ControllerUsuario::class . ':descargarLogoEmpresa');
    $group->post('/registrosUsuario', \ControllerUsuario::class . ':listarRegistrosUsuario')->add(new MWBuscarUsuario());

})->add(new MWVerificarSocio());

$app->group('/mozo', function (RouteCollectorProxy $group){
    $group->get('[/]', \ControllerPedido::class . ':mostrarListosParaEntregar');
    $group->post('/entregarPedido', \ControllerPedido::class . ':entregarPedido');
    $group->post('/cobrarPedido', \ControllerPedido::class . ':cobrarPedido');
})->add(new MWVerificarMozo());
    

$app->group('/factura', function (RouteCollectorProxy $group){
    $group->get('/mesaMasFacturo', \ControllerFactura::class . ':listarMesasOrdFact')->add(new MWVerificarSocio());
    $group->post('/mesaFacturadoFechas', \ControllerFactura::class . ':traerFacturadoMesaEnFechas')->add(new MWVerificarSocio());
})->add(new MWVerificarTokenValido());


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
    $group->post('/darDeAlta', \ControllerPedido::class . ':darDeAlta')->add(new MWVerificarMesa());
    $group->get('/listarPedidos', \ControllerPedido::class . ':listarPedidos');
    $group->get('/mesaRepetida', \ControllerPedido::class . ':traerMesaRepetida')->add(new MWVerificarSocio);
    $group->get('/encuestasPositivas', \ControllerEncuesta::class . ':traerEncuestasPositivas')->add(new MWVerificarSocio);
    $group->get('/pedidosFueraTiempo', \ControllerPedido::class . ':traerEntregasFueraTiempo')->add(new MWVerificarSocio);
    $group->get('/productosMasVendidos', \ControllerPedido::class . ':mostrarProductosMasVendidos')->add(new MWVerificarSocio);

})->add(new MWVerificarTokenValido());;

//CLIENTE
$app->group('/cliente',function (RouteCollectorProxy $group)
{
    $group->post('/estadoPedido', \ControllerPedido::class . ':clienteVerificaEstado');
    $group->post('/cobrarPedido', \ControllerPedido::class . ':cobrarPedido');
});

//PENDIENTES
$app->group('/pendientes', function (RouteCollectorProxy $group){
    $group->get('[/]', \ControllerPendientes::class . ':listarTodos');
    $group->get('/sector', \ControllerPendientes::class . ':listarPendientesPorSector');
    $group->get('/usuario', \ControllerPendientes::class . ':listarPendientesPorUsuario');
    $group->post('/asignarPendiente',\ControllerPendientes::class . ':asignarPendienteEmpleado')->add(new MWVerificarIdPendiente());
    $group->get('/establecerListo/{idPendiente}',\ControllerPendientes::class . ':establecerPendienteListo');
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



//TODO: ENTREGAR PEDIDO -> ENLAZAR PENDIENTE CON PEDIDO. VERIFICAR FUNCIONAMIENTO PARTES HECHAS + AGREGAR PARTES NUEVAS Y MIERDA DEL PDF