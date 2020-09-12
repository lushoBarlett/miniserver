<?php

namespace Server;

use PHPUnit\Framework\TestCase;

use Server\Controllers\IController;
class TestController implements IController {

	public $env;
	public function __construct(Environment $env) {
		$this->env = $env;
	}

	public function __service_init(Request $request) : Response {
		return Response::withText($this->env->constant("route"));
	}

	public static function Node(string $route) : object {
		return (object)[
			"cons" => self,
			"env" => new Environment(["route" => $route])
		];
	}
}

class Dep implements IController {
	
	public $dep;
	public function __construct() {
		$this->dep = func_get_args();
	}

	public function process(Request $request) : Response {
		// use serialization to preserve class names and private fields
		return Response::withText(serialize($this->dep));
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
		$c = Controller(TestController::class, ["/argument"]);

		$this->assertEquals(TestController::class, $c->name);
		$this->assertEquals(["/argument"], $c->args);
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
				"/path" => Service::SimpleController(
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
				Service::SimpleController(
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
		$routes = [
			"/nores" => Service::SimpleController(
				function($r) { return "not response"; }
			),
			"/excep" => Service::SimpleController(
				function($r) { throw new \Exception; }
			)
		];

		$service = new Service($routes, $this->ping("/nores"));
		$service->log = __DIR__ . "/__test.log";
		$response = $service->respond();
		
		$this->assertEquals(Response::serverError()->get_status(), $response->get_status());
		$this->assertTrue(file_exists(__DIR__ . "/__test.log"));
		$this->assertTrue(unlink(__DIR__ . "/__test.log"));

		$service = new Service($routes, $this->ping("/excep"));
		$service->log = __DIR__ . DIRECTORY_SEPARATOR . "__test.log";
		$response = $service->respond();
		
		$this->assertEquals(Response::serverError()->get_status(), $response->get_status());
		$this->assertTrue(file_exists(__DIR__ . "/__test.log"));
		$this->assertTrue(unlink(__DIR__ . "/__test.log"));
	}

	public function testError404Handler() {
		$response = $this->gen_response(
			[
				"/path" => Service::SimpleController(
					function($r) { return Response::withText(""); }
				),
				"<404>" => Service::SimpleController(
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
				"/excep" => Service::SimpleController(
					function($r) { throw \Exception; }
				),
				"<500>" => Service::SimpleController(
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
				"/excep" => Service::SimpleController(
					function($r) { throw \Exception; }
				),
				"<500>" => Service::SimpleController(
					function($r) { return "non response"; }
				)
			],
			$this->ping("/excep")
		);
		$this->assertEquals(Response::serverError()->get_status(), $response->get_status());
	}

	public function testBaseUrl() {
		$routes = [
			"/route" => Service::SimpleController(
				function($r) { return Response::withText("test"); }
			)
		];

		$service = new Service($routes, $this->ping("/route"));
		$service->base_url = "/my/";

		$response = $service->respond();
		$this->assertEquals(Response::notFound()->get_status(), $response->get_status());

		$service = new Service($routes, $this->ping("/my/route"));
		$service->base_url = "/my/";

		$response = $service->respond();
		$this->assertEquals("test", $response->get_payload());
	}

	public function testControllerWithConstructable() {
		$response = $this->gen_response(
			[
				"/route" => Service::Controller(
					Dep::class,
					[
						new Constructable(TestController::class, "/route"),
						new Constructable(Dep::class, new Constructable(Dep::class, "s", 1), null)
					]
				)
			],
			$this->ping("route")
		);
		$this->assertEquals(
			[new TestController("/route"), new Dep(new Dep("s", 1), null)],
			unserialize($response->get_payload())
		);
	}
}

?>
