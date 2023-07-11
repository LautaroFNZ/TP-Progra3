<?php

include_once "./models/empleado.php";
include_once "./models/usuario.php";
include_once "./interfaces/ApiInterface.php";
include_once "./utilidades/archivos.php";
include_once "./PDF/fpdf.php";


class ControllerUsuario extends Usuario
{
    public function login($request,$response,$args)
    {
        $parametros = $request->getParsedBody();

        $empleado = new Empleado();
        $empleado->usuario = $parametros['usuario'];
        $empleado->password = $parametros['password'];

        if($empleadoEncontrado = Empleado::verificarUsuario($empleado->usuario))
        { 
            if($empleado->usuario == $empleadoEncontrado->usuario)
            {
                if($empleado->password == $empleadoEncontrado->password)
                {

                    $info = array('id'=> $empleadoEncontrado->id,'usuario'=>$empleadoEncontrado->usuario,'puesto'=>$empleadoEncontrado->puesto);
                    $token = AutentificadorJWT::CrearToken($info);
                    $payload = json_encode(array('jwt' => $token,'puesto'=>$empleadoEncontrado->puesto));
                    $response->getBody()->write($payload);
                    $usuario = new Usuario();
                    $usuario->setter($empleadoEncontrado->usuario,$empleadoEncontrado->puesto);
                    $usuario->alta();

            
                }else $response->getBody()->write('Verifique la password ingresada para continuar');

            }else $response->getBody()->write('Verifique el usuario ingresado para continuar');
        
        }else{
            $response->getBody()->write('Usuario no encontrado');
        }
        

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function listarFechaLogin($request,$response,$args)
    {
        $usuarios = Usuario::listar();
        $payload = json_encode(array('Registro de Login Usuarios'=> $usuarios));

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function leerUsuariosCsv($request,$response,$args)
    {
        $archivo = new Archivos();
        $retorno =json_decode($archivo->leerUsuariosCSV("usuarios.csv"));
        
        if($retorno->status)
        {
            $payload = json_encode(array('mensaje'=>$retorno->mensaje));

        }else $payload = json_encode(array('mensaje'=>$retorno->mensaje));

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function guardarEnCsv($request,$response,$args)
    {
        $archivo = new Archivos();

        if($csv = $archivo->guardarUsuariosCSV("usuarios.csv"))
        {
            $payload = $csv;
   
        }else $payload = json_encode(array('mensaje'=>'Error al guardar los empleados'));

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function descargarLogoEmpresa($request,$response,$args)
    {
        $pdf = new FPDF('p','mm','A3');
        $pdf->AddPage();
        $pdf->Image(__DIR__ . '\..\logoRestaurante.jpg', 11.25, null, 280, 280, 'jpg');

        return $pdf->Output();
    }

    public function listarRegistrosUsuario($request,$response,$args)
    {
        $parametros = $request->getParsedBody();

        $usuarios = Usuario::traerRegistroUsuario($parametros['usuario']);
        if($usuarios)
        {
            $payload = json_encode(array('Registro de loggeo del usuario'=> $parametros['usuario'],''=>$usuarios));

        }else $payload = json_encode(array('No existen registros para el usuario'=> $parametros['usuario']));
    
        

        $response->getBody()->write($payload);
    
        return $response->withHeader('Content-Type', 'application/json');
    }

   
}

?>