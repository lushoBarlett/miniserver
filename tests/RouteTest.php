<?php

namespace Server;

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
		$this->assertEquals(
			["a" => "value", "c" => "other"],
			Route::arguments("/@a/b/@c/d", "value/b/other/d")
		);
	}
}

?>
