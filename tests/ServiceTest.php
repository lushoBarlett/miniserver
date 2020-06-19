<?php

namespace Server;

use PHPUnit\Framework\TestCase;
use Cologger\Logger;

class TestController implements IController {

	private $path;
	public function __construct(string $path) {
		$this->path = $path;
	}

	public function process(Request $request) : Response {
		return Response::withText($this->path);
	}
}

class DepController implements IController {
	
	private $dep;
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
		return (new Service($routes, $request))->respond();
	}
	private function ping(string $route) {
		return new Request(["action" => $route]);
	}

	public function testControllerFactory() {
		$c = Service::Controller(TestController::class, ["/argument"]);

		$this->assertEquals(TestController::class, $c->name);
		$this->assertEquals(["/argument"], $c->args);
	}

	public function testServiceResponse() {
		$response = $this->gen_response(
			["/path" => Service::Controller(TestController::class, ["/path"])],
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
					DepController::class,
					[new Constructable(TestController::class, "/route"), "string"]
				)
			],
			$this->ping("route")
		);
		$this->assertEquals(
			[new TestController("/route"), "string"],
			unserialize($response->get_payload())
		);
	}
}

?>
