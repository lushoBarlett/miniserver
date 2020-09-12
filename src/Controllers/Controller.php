<?php

namespace Server\Controllers;

class Controller implements IController {

	private $env;

	public function __construct(Environment $env) {
		$this->env = $env;
	}

	public function __service_init(Request $request) : Response {
		switch(strtolower($request->method)) {
		case 'get':
		case 'post':
		case 'put':
		case 'patch':
		case 'delete':
		case 'head':
		case 'options':
		case 'trace':
		case 'connect':
			return $this->{$request-method}(...func_get_args());
		}
		return Response::serverError();
	}

	public function get(Request $r)     : Response { return Response::notFound(); }
	public function post(Request $r)    : Response { return Response::notFound(); }
	public function put(Request $r)     : Response { return Response::notFound(); }
	public function patch(Request $r)   : Response { return Response::notFound(); }
	public function delete(Request $r)  : Response { return Response::notFound(); }
	public function head(Request $r)    : Response { return Response::notFound(); }
	public function options(Request $r) : Response { return Response::notFound(); }
	public function trace(Request $r)   : Response { return Response::notFound(); }
	public function connect(Request $r) : Response { return Response::notFound(); }

	public static function Node(string $cons, ?Environment $env = null) : Node {
		return new Node($cons, $env);
	}
}

?>
