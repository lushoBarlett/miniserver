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
		$c = Controller::Node(TestController::class, ["/argument"]);

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
				"/path/<argument>/<argument>/<argument>/" =>
				SimpleController::Node(
					function($r, $a, $b, $c) {
						return Response::withText($a . $b . $c);
					}
				)
			],
			$this->ping("/path/a/b/c")
		);
		$this->assertEquals("abc", $response->get_payload());
	}

	public function testControllerError() {
		$routes = new Router([
			"/nores" => SimpleController::Node(
				function($r) { return "not response"; }
			),
			"/excep" => SimpleController::Node(
				function($r) { throw new \Exception; }
			)
		]);

		$service = new Service($routes);
		$service->log = __DIR__ . "/__test.log";
		$response = $service->respond($this->ping("/nores"));
		
		$this->assertEquals(Response::serverError()->get_status(), $response->get_status());
		$this->assertTrue(file_exists(__DIR__ . "/__test.log"));
		$this->assertTrue(unlink(__DIR__ . "/__test.log"));

		$service = new Service($routes);
		$service->log = __DIR__ . DIRECTORY_SEPARATOR . "__test.log";
		$response = $service->respond($this->ping("/excep"));
		
		$this->assertEquals(Response::serverError()->get_status(), $response->get_status());
		$this->assertTrue(file_exists(__DIR__ . "/__test.log"));
		$this->assertTrue(unlink(__DIR__ . "/__test.log"));
	}

	public function testError404Handler() {
		$response = $this->gen_response(
			[
				"/path" => SimpleController::Node(
					function($r) { return Response::withText(""); }
				),
				"<404>" => SimpleController::Node(
					function($r) { return Response::withText("handled gracefully"); }
				)
			],
			$this->ping("/non/existent")
		);
		$this->assertEquals("handled gracefully", $response->get_payload());
	}
	
	public function testError500Handler() {
		$response = $this->gen_response(
			[
				"/excep" => SimpleController::Node(
					function($r) { throw \Exception; }
				),
				"<500>" => SimpleController::Node(
					function($r) { return Response::withText("handled gracefully"); }
				)
			],
			$this->ping("/excep")
		);
		$this->assertEquals("handled gracefully", $response->get_payload());
	}
	
	public function testPanic() {
		$response = $this->gen_response(
			[
				"/excep" => SimpleController::Node(
					function($r) { throw \Exception; }
				),
				"<500>" => SimpleController::Node(
					function($r) { return "non response"; }
				)
			],
			$this->ping("/excep")
		);
		$this->assertEquals(Response::serverError()->get_status(), $response->get_status());
	}

	public function testBaseUrl() {
		$routes = new Router([
			"/route" => SimpleController::Node(
				function($r) { return Response::withText("test"); }
			)
		]);

		$service = new Service($routes);
		$service->base_url = "/my/";

		$response = $service->respond($this->ping("/route"));
		$this->assertEquals(Response::notFound()->get_status(), $response->get_status());

		$service = new Service($routes);
		$service->base_url = "/my/";

		$response = $service->respond($this->ping("/my/route"));
		$this->assertEquals("test", $response->get_payload());
	}
}

?>