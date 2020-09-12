<?php

namespace Server;

use PHPUnit\Framework\TestCase;

use Server\Routing\Router;

class RouterTest extends TestCase {

	public function testRoot() {
		$routes = [
			"/" => 1
		];
		$r = new Router($routes);
		$this->assertEquals(1, $r->resolve("/")->value);
	}

	public function testPathResolutionGenericValues() {
		$routes = [
			"/generic/path" => "stuff",
			"/more/path" => 3
		];
		$r = new Router($routes);

		$this->assertEquals("stuff", $r->resolve("/generic/path")->value);
		$this->assertEquals(3, $r->resolve("/more/path")->value);
	}
	
	public function testMixedPaths() {
		$routes = [
			"path/1" => 1,
			"path/2" => 2
		];
		$r = new Router($routes);

		$this->assertEquals(1, $r->resolve("path/1")->value);
		$this->assertEquals(2, $r->resolve("path/2")->value);
	}
	
	public function testPartialPaths() {
		$routes = [
			"partial" => 1,
			"partial/path" => 2
		];
		$r = new Router($routes);

		$this->assertEquals(1, $r->resolve("partial")->value);
		$this->assertEquals(2, $r->resolve("partial/path")->value);
	}
	
	public function testFail() {
		$routes = [
			"path" => 1,
		];
		$r = new Router($routes);

		$this->assertNull($r->resolve("non/path"));
	}

	public function testRouteWithArguments() {
		$routes = [
			"some/@value/path" => 1
		];
		$r = new Router($routes);

		$this->assertNull($r->resolve("some/value/"));
		$this->assertEquals(["value" => "value"], $r->resolve("some/value/path")->args);
	}
	
	public function testRouteWithMultipleArguments() {
		$routes = [
			"a/@b/c/@d" => 1
		];
		$r = new Router($routes);

		$this->assertEquals(["b" => "b", "d" => "d"], $r->resolve("/a/b/c/d/")->args);
	}
	
	public function testOverloadedDefinitions() {
		$routes = [
			"/path/@argument/" => 1,
			"/path/specific/" => 2
		];
		$r = new Router($routes);

		$this->assertEquals(2, $r->resolve("path/specific")->value);
		$this->assertEquals(1, $r->resolve("path/different")->value);
	}

	public function testSubrouteFirst() {
		$routes = [
			"/path/specific" => 2,
			"/path" => 1
		];
		$r = new Router($routes);

		$this->assertEquals(2, $r->resolve("path/specific")->value);
		$this->assertEquals(1, $r->resolve("path")->value);
	}
}

?>