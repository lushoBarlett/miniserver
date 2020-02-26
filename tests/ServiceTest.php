<?php

namespace Server;

use PHPUnit\Framework\TestCase;

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

	# TODO: ADD CONTROLLER ERROR TEST
}

?>
