<?php

namespace Mini\Tools;

include "vendor/autoload.php";

use PHPUnit\Framework\TestCase;

class DebugTest extends TestCase {

	public function testDebugOutput() {
		$debug = new Debug;

		$debug->start();
		echo "debug";
		$debug->collect();

		$this->assertEquals("Debug output:\ndebug", (string)$debug);
	}

	public function testDebugFile() {
		$debug = new Debug;
		$debug->print("debug")->output("file.out")->append("file.out");
		
		$this->assertTrue(\file_exists("file.out"));
		$this->assertEquals("debugdebug", \file_get_contents("file.out"));

		unlink("file.out");

		$this->assertFalse(\file_exists("file.out"));
	}

	public function testCloseStream() {
		$this->expectNotToPerformAssertions();
		$debug = new Debug;
		$debug->start();
		echo "debug";
	}

	public function testThrowable() {
		$debug = (new Debug)->throwed(new \Exception("debug"));
		$this->assertMatchesRegularExpression("/(.*)debug(.*)/", (string)$debug);
		$this->assertMatchesRegularExpression("/(.*)Exception(.*)/", (string)$debug);
	}
}

?>