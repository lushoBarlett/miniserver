<?php

namespace Server;

use PHPUnit\Framework\TestCase;
use function Server\route_trim;
use function Server\route_split;

class UtilsTest extends TestCase {

	public function path() {
		return [
			["generic/path"],
			["/generic/path"],
			["generic/path/"],
			["/generic/path/"],
			["/ generic/path/"],
			["/generic/path/ "]
		];
	}

	/**
	 * @dataProvider path
	 * Tests route_trim
	 */
	public function testTrim($route) {
		$this->assertEquals("generic/path", route_trim($route));
	}

	/**
	 * @dataProvider path
	 * Tests route_trim
	 */
	public function testSplit($route) {
		$this->assertEquals(["generic","path"], route_split($route));
	}
	
	public function testTemplatePath() {
		$this->assertEquals("/templates/test",template_path("test"));
		Service::$template_path = "/other/path/";
		$this->assertEquals("/other/path/test",template_path("test"));
	}
}

?>
