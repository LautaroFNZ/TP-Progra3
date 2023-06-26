<?php

require_once "./models/empleado.php";
require_once "./models/usuario.php";
require_once "./interfaces/ApiInterface.php";

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
                    $payload = json_encode(array('jwt' => $token));
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

   
}

?>