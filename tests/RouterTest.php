<?php

namespace Mini\Routing;

use PHPUnit\Framework\TestCase;

use Mini\Tools\HTTP;

class RouterTest extends TestCase {

	public function controller() {
		return fn() => 0;
	}

	public function testRoot() {
		$route = Route::forall("/", $this->controller());
		$router = new Router($route);
		$this->assertSame($route, $router->resolve("/")[HTTP::POST]);
	}

	public function testMixedPaths() {
		$routes = [
			Route::forall("path/1", $this->controller()),
			Route::forall("path/2", $this->controller()),
		];
		$router = new Router(...$routes);

		$this->assertSame($routes[0], $router->resolve("path/1")[HTTP::CONNECT]);
		$this->assertSame($routes[1], $router->resolve("path/2")[HTTP::GET]);
	}
	
	public function testPartialPaths() {
		$routes = [
			Route::forall("partial", $this->controller()),
			Route::forall("partial/path", $this->controller()),
		];
		$router = new Router(...$routes);

		$this->assertSame($routes[0], $router->resolve("partial")[HTTP::PUT]);
		$this->assertSame($routes[1], $router->resolve("partial/path")[HTTP::PATCH]);
	}
	
	public function testFail() {
		$router = new Router(Route::forall("path", $this->controller()));
		$this->assertEmpty($router->resolve("non/path"));
	}

	public function testRouteWithArguments() {
		$router = new Router(Route::forall("some/@value/path", $this->controller()));

		$this->assertEmpty($router->resolve("some/1/"));
		$this->assertEquals(["value" => "1"], $router->resolve("some/1/path")[HTTP::DELETE]->arguments("some/1/path"));
	}
	
	public function testRouteWithMultipleArguments() {
		$router = new Router(Route::forall("a/@b/c/@d", $this->controller()));
		$this->assertEquals(["b" => "1", "d" => "2"], $router->resolve("/a/1/c/2/")[HTTP::GET]->arguments("/a/1/c/2/"));
	}
	
	public function testOverloadedDefinitions() {
		$routes = [
			Route::forall("/path/@argument/", $this->controller()),
			Route::forall("/path/specific/", $this->controller()),
		];
		$router = new Router(...$routes);

		$this->assertSame($routes[0], $router->resolve("path/different")[HTTP::POST]);
		$this->assertSame($routes[1], $router->resolve("path/specific")[HTTP::OPTIONS]);
	}

	public function testSubrouteFirst() {
		$routes = [
			Route::forall("/path/specific", $this->controller()),
			Route::forall("/path", $this->controller()),
		];
		$router = new Router(...$routes);

		$this->assertSame($routes[0], $router->resolve("path/specific")[HTTP::HEAD]);
		$this->assertSame($routes[1], $router->resolve("path")[HTTP::TRACE]);
	}

	public function testDifferentMethods() {
		$routes = [
			Route::define("/same/path", HTTP::GET | HTTP::POST, $this->controller()),
			Route::define("same/path", HTTP::OPTIONS, $this->controller()),
		];
		$router = new Router(...$routes);

		$this->assertSame($routes[0], $router->resolve("/same/path")[HTTP::GET]);
		$this->assertSame($routes[0], $router->resolve("/same/path")[HTTP::POST]);
		$this->assertNotSame($routes[1], $router->resolve("same/path")[HTTP::GET]);
		$this->assertNotSame($routes[1], $router->resolve("same/path")[HTTP::POST]);

		$this->assertSame($routes[1], $router->resolve("same/path")[HTTP::OPTIONS]);
		$this->assertNotSame($routes[0], $router->resolve("same/path")[HTTP::OPTIONS]);

		$this->assertCount(3, $router->resolve("same/path"));
	}
}

?>