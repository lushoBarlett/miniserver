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
		/* Call model stuff */
		return Response::withText($this->path);
	}
}

class ServiceTest extends TestCase {
/*
	public function testControllerFactory() {
		$c = Service::Controller(
			TestController::class, ["/argument"]
		);

		$this->assertTrue(is_object($c));

		$this->assertEquals(__NAMESPACE__ . "\\TestController", $c->name);
		$this->assertEquals(["/argument"], $c->args);
	}

	public function testServiceResponse() {
		$routes = [
			"/path" => Service::Controller(
				TestController::class, ["/path"]
			)
		];
		$request = new Request(
			["action" => "/path"]
		);
		$service = new Service($routes, $request);

		$response = $service->respond();
		$this->assertEquals("/path", (string)$response);
	}

	public function testSimpleController() {
		$routes = [
			"/path" => Service::SimpleController(
				function($r) { return Response::withText("text"); }
			)
		];

		$request = new Request(
			["action" => "/path"]
		);

		$service = new Service($routes, $request);

		$response = $service->respond();
		$this->assertEquals("text", (string)$response);
	}
	
	public function testControllerExtraArguments() {
		$routes = [
			"/path/<argument>/<argument>/<argument>/" =>
			Service::SimpleController(
				function($r, $a, $b, $c) {
					return Response::withText($a . $b . $c);
				}
			)
		];

		$request = new Request(
			["action" => "/path/a/b/c"]
		);

		$service = new Service($routes, $request);

		$response = $service->respond();
		$this->assertEquals("abc", (string)$response);
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

		$request = new Request(
			["action" => "/nores"]
		);

		$service = new Service($routes, $request);
		$service->log = __DIR__ . DIRECTORY_SEPARATOR . "__test.log";
		$response = $service->respond();
		
		$this->assertEquals((string)Response::serverError(), (string)$response);
		$this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . "__test.log"));
		$this->assertTrue(unlink(__DIR__ . DIRECTORY_SEPARATOR . "__test.log"));

		$request = new Request(
			["action" => "/excep"]
		);

		$service = new Service($routes, $request);
		$service->log = __DIR__ . DIRECTORY_SEPARATOR . "__test.log";
		$response = $service->respond();
		
		$this->assertEquals((string)Response::serverError(), (string)$response);
		$this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . "__test.log"));
		$this->assertTrue(unlink(__DIR__ . DIRECTORY_SEPARATOR . "__test.log"));
	}

	public function testError404Handler() {
		$routes = [
			"/path" => Service::SimpleController(
				function($r) { return Response::withText(""); }
			),
			"<404>" => Service::SimpleController(
				function($r) { return Response::withText("handled gracefully"); }
			)
		];

		$request = new Request(
			["action" => "/non/existent"]
		);

		$service = new Service($routes, $request);

		$response = $service->respond();
		$this->assertEquals("handled gracefully", (string)$response);
	}
	
	public function testError500Handler() {
		$routes = [
			"/excep" => Service::SimpleController(
				function($r) { throw \Exception; }
			),
			"<500>" => Service::SimpleController(
				function($r) { return Response::withText("handled gracefully"); }
			)
		];

		$request = new Request(
			["action" => "/excep"]
		);

		$service = new Service($routes, $request);

		$response = $service->respond();
		$this->assertEquals("handled gracefully", (string)$response);
	}
	
	public function testPanic() {
		$routes = [
			"/excep" => Service::SimpleController(
				function($r) { throw \Exception; }
			),
			"<500>" => Service::SimpleController(
				function($r) { return "non response"; }
			)
		];

		$request = new Request(
			["action" => "/excep"]
		);

		$service = new Service($routes, $request);

		$response = $service->respond();
		$this->assertEquals((string)Response::serverError(), (string)$response);
	}
*/
	public function testBaseUrl() {
		$routes = [
			"/route" => Service::SimpleController(
				function($r) { return Response::withText("test"); }
			)
		];
		
		$request = new Request(
			["action" => "route"]
		);

		$service = new Service($routes, $request);
		$service->base_url = "/my/";

		$response = $service->respond();
		$this->assertEquals((string)Response::notFound(), (string)$response);
		
		$request = new Request(
			["action" => "my/route"]
		);

		$service = new Service($routes, $request);
		$service->base_url = "/my/";

		$response = $service->respond();
		$this->assertEquals("test", (string)$response);
	}
}

?>
