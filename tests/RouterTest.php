<?php

namespace Server;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase {

	public function testPathResolutionGenericValues() {
		$routes = [
			"/generic/path" => "stuff",
			"/more/path" => 3
		];
		$r = new Router($routes);

		$this->assertEquals("stuff", $r->resolve("/generic/path"));
		
		$this->assertEquals(3, $r->resolve("/more/path"));
	}
	
	public function testMixedPaths() {
		$routes = [
			"path/1" => 1,
			"path/2" => 2
		];
		$r = new Router($routes);

		$this->assertEquals(1, $r->resolve("path/1"));
		
		$this->assertEquals(2, $r->resolve("path/2"));
	}
	
	public function testPartialPaths() {
		$routes = [
			"partial" => 1,
			"partial/path" => 2
		];
		$r = new Router($routes);

		$this->assertEquals(1, $r->resolve("partial"));
		
		$this->assertEquals(2, $r->resolve("partial/path"));
	}
	
	public function testFail() {
		$routes = [
			"path" => 1,
		];
		$r = new Router($routes);

		$this->assertNull($r->resolve("non/path"));
	}
}

?>
