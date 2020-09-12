<?php

namespace Server;

use PHPUnit\Framework\TestCase;

use Server\Directives\Directive;
use Server\Controllers\IController;
use Server\Controllers\Node;

use Server\Routing\Route;
use Server\Routing\Router;
use Server\Routing\Resolution;

class ADirective extends Directive {

	public function request(State $s) : State {
		$s->request->action = "/other/path";
		return $s;
	}

	public function response(State $s) : State {
		$s->response = Response::withText("some content");
		return $s;
	}
}

class BDirective extends Directive {

	public function exception(State $s) : State {
		$s->response = Response::withText("EXCEPTION");
		return $s;
	}

	public function error(State $s) : State {
		$s->response = Response::withText("ERROR");
		return $s;
	}

	public function resolution(State $s) : State {
		// NOTE: no matter the route, a FailController will get instantiated 
		$s->resolution = new Resolution(FailController::Node(), $s->request->action);
		return $s;
	}
}

class FailController implements IController {
	public function __construct(Environment $env) {}

	public function __service_init(Request $r) : Response {
		if (Route::trim($r->action) == "except")
			throw new \Exception;
		else if (Route::trim($r->action) == "error")
			throw new \Error;
		return Response::withText("FAILED TO FAIL");
	}

	public static function Node(...$args) : Node {
		return new Node(self::class);
	}
}

class DirectiveTest extends TestCase {

	private function gen_response(array $routes, Directive $d, Request $request) {
		$s = new Service(new Router($routes), new Environment(["@test" => $d]));
		return $s->respond($request);
	}

	private function ping(string $route) {
		return new Request(["action" => $route]);
	}

	public function testRequestEvent() {
		$request = $this->ping("/some/path");
		// NOTE: object is always passed by reference
		$this->gen_response([], new ADirective, $request);
		$this->assertEquals("/other/path", $request->action);
	}

	public function testResponseEvent() {
		$response = $this->gen_response([], new ADirective, $this->ping("/some/path"));
		$this->assertEquals("some content", $response->get_payload());
	}

	public function testExceptionErrorEvent() {
		$routes = [
			"except" => FailController::Node(),
			"error" => FailController::Node()
		];
		$response = $this->gen_response($routes, new BDirective, $this->ping("except"));
		$this->assertEquals("EXCEPTION", $response->get_payload());

		$response = $this->gen_response($routes, new BDirective, $this->ping("error"));
		$this->assertEquals("ERROR", $response->get_payload());
	}

	public function testResolutionEvent() {
		$response = $this->gen_response([], new BDirective, $this->ping("/some/path"));
		$this->assertEquals("FAILED TO FAIL", $response->get_payload());
	}
}