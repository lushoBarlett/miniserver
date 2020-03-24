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
		$service->log = __DIR__ . "/__test.log";
		$response = $service->respond();
		
		$this->assertEquals(Response::serverError(), $response);
		$this->assertTrue(file_exists(__DIR__ . "/__test.log"));
		$this->assertTrue(unlink(__DIR__ . "/__test.log"));

		$request = new Request(
			["action" => "/excep"]
		);

		$service = new Service($routes, $request);
		$service->log = __DIR__ . "/__test.log";
		$response = $service->respond();
		
		$this->assertEquals(Response::serverError(), $response);
		$this->assertTrue(file_exists(__DIR__ . "/__test.log"));
		$this->assertTrue(unlink(__DIR__ . "/__test.log"));
	}
}

?>
