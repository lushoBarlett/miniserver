<?php

namespace Server\Controllers;

use Server\Environment;
use Server\Request;
use Server\Response;

class Controller implements IController {

	private $env;

	public function __construct(Environment $env) {
		$this->env = $env;
	}

	public function __service_init(Request $request) : Response {
		switch($request->method) {
		case Request::GET:
		case Request::POST:
		case Request::PUT:
		case Request::PATCH:
		case Request::DELETE:
		case Request::HEAD:
		case Request::OPTIONS:
		case Request::TRACE:
		case Request::CONNECT:
			$cons = $this->env->constant("__controller_construction");
			$controller = new $cons($this->env);

			return method_exists($controller, $request->method)
			       ? $controller->{$request->method}($request)
			       : Response::notFound();
		}
		return Response::serverError();
	}

	public static function Node(...$args) : Node {
		$cons = $args[0];
		$env = $args[1] ?? [];
		$env["__controller_construction"] = $cons;
		return new Node(self::class, $env);
	}
}

?>