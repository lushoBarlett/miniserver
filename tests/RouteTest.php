<?php

namespace Mini\Routing;

include "vendor/autoload.php";

use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase {

	public function path() {
		return [
			["generic/path"],
			["/generic/path"],
			["generic/path/"],
			["/generic/path/"],
			[" /generic/path/"],
			["/generic/path/ "]
		];
	}

	/**
	 * @dataProvider path
	 */
	public function testTrim($route) {
		$this->assertEquals("generic/path", Route::trim($route));
	}

	/**
	 * @dataProvider path
	 */
	public function testSplit($route) {
		$this->assertEquals(["generic","path"], Route::split($route));
	}

	public function testIsArgument() {
		$this->assertFalse(Route::is_argument("somepath"));
		$this->assertTrue(Route::is_argument("@somepath"));
	}

	public function testArgumentName() {
		$this->assertEquals("someargument", Route::argument_name("@someargument"));
	}

	public function testArguments() {
		$name = "/@a/b/@c/d";
		$route = Route::forall($name, fn() => 0)
			->parameter_type("a", Route::Int)
			->parameter_type("c", Route::Float);

		$this->assertEquals(["a" => 1, "c" => 1.0], $route->arguments("1/b/1.0/d"));
	}
}

?>