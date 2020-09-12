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
			return $this->{$request-method}($request);
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

	public static function Node(...$args) : Node {
		list ($cons, $env) = $args;
		return new Node($cons, $env);
	}
}

?>