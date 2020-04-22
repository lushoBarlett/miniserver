<?php

namespace Server;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase {

	public function testResolveObject() {
		$r = new Router;
		$resolution = $r->resolve("");

		$this->assertObjectHasAttribute('value', $resolution);
		$this->assertObjectHasAttribute('route_args', $resolution);
		$this->assertObjectHasAttribute('failed', $resolution);
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

		$this->assertTrue($r->resolve("non/path")->failed);
	}

	public function testRouteWithArguments() {
		$routes = [
			"some/<argument>/path" => 1
		];
		$r = new Router($routes);

		// incomplete
		$this->assertTrue($r->resolve("some/value/")->failed);

		$this->assertEquals(["value"], $r->resolve("some/value/path")->route_args);
	}
	
	public function testRouteWithMultipleArguments() {
		$routes = [
			"a/<argument>/c/<argument>" => 1
		];
		$r = new Router($routes);

		$this->assertEquals(["b","d"], $r->resolve("/a/b/c/d/")->route_args);
	}
	
	public function testOverloadedDefinitions() {
		$routes = [
			"/path/<argument>/" => 1,
			"/path/specific/" => 2
		];
		$r = new Router($routes);

		$this->assertEquals(2, $r->resolve("path/specific")->value);
		$this->assertEquals(1, $r->resolve("path/different")->value);
	}
}

?>
