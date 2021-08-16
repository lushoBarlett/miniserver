<?php

namespace Mini\Routing;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase {

	public function controller() {
		return fn() => 0;
	}

	public function testRoot() {
		$route = Route::forall("/", $this->controller());
		$router = new Router($route);
		$this->assertSame($route, $router->resolve("/"));
	}

	public function testMixedPaths() {
		$routes = [
			Route::forall("path/1", $this->controller()),
			Route::forall("path/2", $this->controller()),
		];
		$router = new Router(...$routes);

		$this->assertSame($routes[0], $router->resolve("path/1"));
		$this->assertSame($routes[1], $router->resolve("path/2"));
	}
	
	public function testPartialPaths() {
		$routes = [
			Route::forall("partial", $this->controller()),
			Route::forall("partial/path", $this->controller()),
		];
		$router = new Router(...$routes);

		$this->assertSame($routes[0], $router->resolve("partial"));
		$this->assertSame($routes[1], $router->resolve("partial/path"));
	}
	
	public function testFail() {
		$router = new Router(Route::forall("path", $this->controller()));
		$this->assertNull($router->resolve("non/path"));
	}

	public function testRouteWithArguments() {
		$router = new Router(Route::forall("some/@value/path", $this->controller()));

		$this->assertNull($router->resolve("some/1/"));
		$this->assertEquals(["value" => "1"], $router->resolve("some/1/path")->arguments("some/1/path"));
	}
	
	public function testRouteWithMultipleArguments() {
		$router = new Router(Route::forall("a/@b/c/@d", $this->controller()));
		$this->assertEquals(["b" => "1", "d" => "2"], $router->resolve("/a/1/c/2/")->arguments("/a/1/c/2/"));
	}
	
	public function testOverloadedDefinitions() {
		$routes = [
			Route::forall("/path/@argument/", $this->controller()),
			Route::forall("/path/specific/", $this->controller()),
		];
		$router = new Router(...$routes);

		$this->assertSame($routes[0], $router->resolve("path/different"));
		$this->assertSame($routes[1], $router->resolve("path/specific"));
	}

	public function testSubrouteFirst() {
		$routes = [
			Route::forall("/path/specific", $this->controller()),
			Route::forall("/path", $this->controller()),
		];
		$router = new Router(...$routes);

		$this->assertSame($routes[0], $router->resolve("path/specific"));
		$this->assertSame($routes[1], $router->resolve("path"));
	}
}

?>