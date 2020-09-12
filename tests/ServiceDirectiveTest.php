<?php

namespace Server;

use PHPUnit\Framework\TestCase;

use Server\Directives\Directive;
use Server\Controllers\IController;
use Server\Controllers\Node;

use Server\Routing\Router;
use Server\Routing\Resolution;

class ADirective extends Directive {

	public function request_event(State $s) : State {
		$s->request->action = "/other/path";
		return $s;
	}

	public function response_event(State $s) : State {
		$s->response = Response::withText("some content")->status(200);
		return $s;
	}
}

class BDirective extends Directive {

	public function exception_event(State $s) : State {
		$s->response = Response::withText("EXCEPTION")->status(500);
		return $s;
	}

	public function error_event(State $s) : State {
		$s->response = Response::withText("ERROR")->status(500);
		return $s;
	}

	public function resolution_event(State $s) : State {
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
		return Response::withText("FAILED TO FAIL")->status(200);
	}

	public static function Node(...$args) : Node {
		return new Node(self::class);
	}
}

class ServiceDirectiveTest extends TestCase {

	private function gen_response(array $routes, Directive $d, Request $request) {
		return (new Service(
			new Router($routes), new Environment(["@test" => $d]))
		)->respond($request);
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
		$this->assertEquals(200, $response->get_status());
	}

	public function testExceptionErrorEvent() {
		$routes = [
			"except" => FailController::Node(),
			"error" => FailController::Node()
		];
		$response = $this->gen_response($routes, new BDirective, $this->ping("except"));
		$this->assertEquals("EXCEPTION", $response->get_payload());
		$this->assertEquals(500, $response->get_status());

		$response = $this->gen_response($routes, new BDirective, $this->ping("error"));
		$this->assertEquals("ERROR", $response->get_payload());
		$this->assertEquals(500, $response->get_status());
	}

	public function testResolutionEvent() {
		$response = $this->gen_response([], new BDirective, $this->ping("/some/path"));
		$this->assertEquals("FAILED TO FAIL", $response->get_payload());
		$this->assertEquals(200, $response->get_status());
	}
}