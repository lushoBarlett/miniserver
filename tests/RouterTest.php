<?php

namespace Server;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase {

	public function testRoot() {
		$routes = [
			"/" => 1
		];
		$r = new Router($routes);
		$this->assertEquals(1, $r->resolve("/")->cons);
	}

	public function testPathResolutionGenericValues() {
		$routes = [
			"/generic/path" => "stuff",
			"/more/path" => 3
		];
		$r = new Router($routes);

		$this->assertEquals("stuff", $r->resolve("/generic/path")->cons);
		$this->assertEquals(3, $r->resolve("/more/path")->cons);
	}
	
	public function testMixedPaths() {
		$routes = [
			"path/1" => 1,
			"path/2" => 2
		];
		$r = new Router($routes);

		$this->assertEquals(1, $r->resolve("path/1")->cons);
		$this->assertEquals(2, $r->resolve("path/2")->cons);
	}
	
	public function testPartialPaths() {
		$routes = [
			"partial" => 1,
			"partial/path" => 2
		];
		$r = new Router($routes);

		$this->assertEquals(1, $r->resolve("partial")->cons);
		$this->assertEquals(2, $r->resolve("partial/path")->cons);
	}
	
	public function testFail() {
		$routes = [
			"path" => 1,
		];
		$r = new Router($routes);

		$this->assertTrue($r->resolve("non/path")->failed);
	}

	public function testRouteWithArguments() {
		$routes = [
			"some/@value/path" => 1
		];
		$r = new Router($routes);

		$this->assertTrue($r->resolve("some/value/")->failed);
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

		$this->assertEquals(2, $r->resolve("path/specific")->cons);
		$this->assertEquals(1, $r->resolve("path/different")->cons);
	}

	public function testSubrouteFirst() {
		$routes = [
			"/path/specific" => 2,
			"/path" => 1
		];
		$r = new Router($routes);

		$this->assertEquals(2, $r->resolve("path/specific")->cons);
		$this->assertEquals(1, $r->resolve("path")->cons);
	}
}

?>
