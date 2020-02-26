<?php

namespace Server;

interface IController {

	/**
	 * Revisa permisos, procesa datos y devuelve datos o una página web
	 * 
	 * @param Request $data todos los datos enviados
	 * 
	 * @return Response
	 */
	public function process(Request $request) : Response;
}

?>
