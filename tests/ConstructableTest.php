<?php

namespace Server;

use PHPUnit\Framework\TestCase;

class Test {
	public $args;
	public static $constructed = false;
	public function __construct() {
		self::$constructed = true;
		$this->args = func_get_args();
	}
}

class ConstructableTest extends TestCase {

	public function testConstructRecursive() {
		$dep = new Constructable(
			Test::class,

			new Constructable(Test::class),
			"testing string",
			new Constructable(Test::class, 1),
			null,
			Test::class
		);

		$result = $dep->construct();
		$this->assertInstanceOf(Test::class, $result);
		$this->assertInstanceOf(Test::class, $result->args[0]);
		$this->assertInstanceOf(Test::class, $result->args[2]);

		$this->assertEquals("testing string", $result->args[1]);
		$this->assertEquals(null, $result->args[3]);
		$this->assertEquals(Test::class, $result->args[4]);

		$this->assertEquals([], $result->args[0]->args);
		$this->assertEquals([1], $result->args[2]->args);
	}

	public function testStandByForConstruction() {
		Test::$constructed = false;
		$dep = new Constructable(Test::class);

		$this->assertFalse(Test::$constructed);
		$dep->construct();
		$this->assertTrue(Test::$constructed);
	}
}

?>
