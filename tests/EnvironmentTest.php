<?php

namespace Server;

include "vendor/autoload.php";

use PHPUnit\Framework\TestCase;

use Server\Modules\Module;

class TestModule extends Module {}

class EnvironmentTest extends TestCase {

	public function testEnvironment() {
		$env = new Environment([
			"const" => 1,
			"#call" => function() { return 2; },
			"@mod"  => new TestModule
		]);

		$this->assertNull($env->constant("no-const"));
		$this->assertNull($env->provider("no-call"));
		$this->assertNull($env->module("no-mod"));

		$this->assertEquals(1, $env->constant("const"));
		$this->assertEquals(2, ($env->provider("call"))());
		$this->assertTrue($env->module("mod") instanceof Module);
	}
}