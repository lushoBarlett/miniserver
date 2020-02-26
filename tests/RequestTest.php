<?php

namespace Server;

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {

	/**
	 * Tests wether instanciating the class
	 * as is breaks the execution or not
	 */
	public function testNoBreak() {
		$r = new Request;
		$this->assertEquals(__NAMESPACE__ . "\\Request", get_class($r));
	}

	/**
	 * Tests that a failed property
	 * should still exist
	 */
	public function testPersistentProperty() {
		$r = new Request;
		$this->assertObjectHasAttribute("action", $r);
		$this->assertObjectHasAttribute("secure", $r);
		$this->assertObjectHasAttribute("method", $r);
		$this->assertObjectHasAttribute("get", $r);
		$this->assertObjectHasAttribute("post", $r);
		$this->assertObjectHasAttribute("raw", $r);
		$this->assertObjectHasAttribute("json", $r);
		$this->assertObjectHasAttribute("contentType", $r);
		$this->assertObjectHasAttribute("cookies", $r);
	}
	
	/**
	 * Tests that a new property
	 * should be added
	 */
	public function testNewProperty() {
		$r = new Request([
			"new" => "prop"
		]);

		$this->assertObjectHasAttribute("new", $r);
		$this->assertEquals("prop", $r->new);
	}
	
	/**
	 * Tests that a new property
	 * with the same name as a predefined one
	 * prevails over the latter
	 */
	public function testOverwriteProperty() {
		$r = new Request([
			"action" => "newaction"
		]);

		$this->assertObjectHasAttribute("action", $r);
		$this->assertEquals("newaction", $r->action);
	}
}

?>
