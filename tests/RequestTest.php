<?php

namespace Mini;

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {

	public function testNewProperty() {
		$r = new Request([
			"new" => "prop"
		]);

		$this->assertObjectHasAttribute("new", $r);
		$this->assertEquals("prop", $r->new);
	}
	
	public function testOverwriteProperty() {
		$r = new Request([
			"action" => "newaction"
		]);

		$this->assertObjectHasAttribute("action", $r);
		$this->assertEquals("newaction", $r->action);
	}
}

?>