<?php

namespace Server;

include 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class ResolutionTest extends TestCase {

	public function testResolveObject() {
		$res = new Resolution(1, "a", [2], true);

		$this->assertEquals(1, $res->cons);
		$this->assertEquals("a", $res->route);
		$this->assertEquals([2], $res->args);
		$this->assertEquals(true, $res->failed);
	}

	public function testFail() {
		$this->assertFalse((new Resolution(0, ""))->failed);
		$this->assertTrue((Resolution::failed())->failed);
	}
}
