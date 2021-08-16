<?php

namespace Mini;

use PHPUnit\Framework\TestCase;

use Mini\Routing\Router;
use Mini\Routing\Route;
use Mini\Environment;
use Mini\Tools\Debug;
use Mini\Tools\HTTP;

class ServiceTest extends TestCase {

	public function router(string $route, int $method, callable $controller) : Router {
		return new Router(Route::define($route, $method, $controller));
	}

	public function noreturn(string $message) : callable {
		return function($x) use($message) {
			throw new \Exception($message);
		};
	}

	public function ping(string $route, int $method, callable $controller) : Response {
		$service = new Service($this->router($route, $method, $controller));
		return $service->respond(new Request([
			"action" => $route,
			"method" => $method
		]));
	}

	public function testNormal() {
		$response = $this->ping("/", HTTP::GET, fn($x) => Response::OK());
		$this->assertEquals(Response::OK(), $response);
	}

	public function testError() {
		$router = $this->router("/", HTTP::GET, fn($x) => Response::OK());

		$service = new Service($router, (new Environment)->request($this->noreturn("request")));
		$response = $service->respond(new Request(["action" => "/"]));
		$this->assertEquals(Response::serverError(), $response);
		$this->assertMatchesRegularExpression("/request/", (string)$service->debug);

		$service = new Service($router, (new Environment)->response($this->noreturn("response")));
		$response = $service->respond(new Request(["action" => "/"]));
		$this->assertEquals(Response::serverError(), $response);
		$this->assertMatchesRegularExpression("/response/", (string)$service->debug);
	}

	public function testPanic() {
		$router = $this->router("/", HTTP::GET, fn($x) => Response::OK());

		$service = new Service($router, (new Environment)
			->request($this->noreturn("request"))
			->fail($this->noreturn("fail")));
		$response = $service->respond(new Request(["action" => "/"]));
		$this->assertEquals(Response::serverError(), $response);
		$this->assertMatchesRegularExpression("/request/", (string)$service->debug);
		$this->assertMatchesRegularExpression("/fail/", (string)$service->debug);
		$this->assertMatchesRegularExpression("/panic/", (string)$service->debug);
	}

	public function testIntegration() {
		$controller = function(int $id, string $name) {
			return Response::withJSON(["id" => $id, "name" => $name]);
		};

		$remove_api = function(Request $request) {
			echo "hello there";
			$path = Route::split($request->action);
			if ($path[0] == "api") {
				$request->action = "{$path[1]}/{$path[2]}";
				echo $request->action;
			}
			return $request;
		};

		$continue = function(Response $response) {
			$response->status(201);
			return $response;
		};

		$router = new Router(
			Route::define("/@id/@name", HTTP::GET, $controller)
				->parameter_type("id", Route::Int)
				->omit_request()
				->environment((new Environment)->response($continue))
		);

		$service = new Service($router, (new Environment)->request($remove_api));
		$response = $service->respond(new Request([
			"action" => "/api/1/test",
			"method" => HTTP::GET
		]));

		$this->assertEquals(201, $response->status);
		$this->assertEquals('{"id":1,"name":"test"}', $response->payload);
	}
}

?>