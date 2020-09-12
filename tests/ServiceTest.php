<?php

namespace Server;

use PHPUnit\Framework\TestCase;

use Server\Controllers\IController;
use Server\Controllers\Controller;
use Server\Controllers\SimpleController;
use Server\Controllers\Node;

use Server\Routing\Router;

class TestController implements IController {

	public $env;
	public function __construct(Environment $env) {
		$this->env = $env;
	}

	public function __service_init(Request $request) : Response {
		return Response::withText($this->env->constant("route"));
	}

	public static function Node(...$args) : Node {
		list($route) = $args;
		return new Node(self::class, ["route" => $route]);
	}
}

class ServiceTest extends TestCase {

	private function gen_response(array $routes, Request $request) {
		return (new Service(
			new Router($routes)
		))->respond($request);
	}

	private function ping(string $route) {
		return new Request(["action" => $route]);
	}

	public function testControllerFactory() {
		$c = Controller::Node(TestController::class, ["route" => "/argument"]);

		$this->assertEquals(TestController::class, $c->cons);
		$this->assertEquals(new Environment(["route" => "/argument"]), $c->env);
	}

	public function testServiceResponse() {
		$response = $this->gen_response(
			["/path" => TestController::Node("/path")],
			$this->ping("/path")
		);
		$this->assertEquals("/path", $response->get_payload());
	}

	public function testSimpleController() {
		$response = $this->gen_response(
			[
				"/path" => SimpleController::Node(
					function($r) { return Response::withText("text"); }
				)
			],
			$this->ping("/path")
		);
		$this->assertEquals("text", $response->get_payload());
	}
	
	public function testControllerExtraArguments() {
		$response = $this->gen_response(
			[
				"/path/@a/@b/@c/" =>
				SimpleController::Node(
					function($r) {
						return Response::withText($r->args["a"] . $r->args["b"] . $r->args["c"]);
					}
				)
			],
			$this->ping("/path/a/b/c")
		);
		$this->assertEquals("abc", $response->get_payload());
	}
}

?>