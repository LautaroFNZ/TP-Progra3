<?php
interface IApiUsable
{
	//public function listarEspecifico($request, $response, $args);
	public function listarTodos($request, $response, $args);
	public function darDeAlta($request, $response, $args);
}