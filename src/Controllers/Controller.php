<?php

namespace Server\Controllers;

class Controller implements IController {

	private $service;
	private $metadata;

	public function __construct(Service $service, array $metadata = []) {
		$this->service = $service;
		// TODO: wtf with @args?
		$this->metadata = $metadata;
	}

	public function __service_init(Request $request) : Response {
		switch($request->method) {
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

	public static function Node($c_or_meta, $maybe_c) : object {
		if (is_string($c_or_meta))
			return (object)[
				"cons" => $c_or_meta,
				"meta" => []
			];

		else if (is_array($c_or_meta) and is_string($maybe_c))
			return (object)[
				"cons" => $maybe_c,
				"meta" => ["@args" => $c_or_meta]
			];

		else throw new Exception(
			"Wrong arguments supplied to Controller::Node\nExpected:\n"
			. "\t- controller class [string]"
			. "\t- controller metadata [array], controller class [string]\n"
		);
	}
}

?>
