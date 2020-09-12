<?php

namespace Server;

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {

	public function testNoBreak() {
		$r = new Request;
		$this->assertInstanceOf(Request::class, $r);
	}

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
		$this->assertObjectHasAttribute("files", $r);
	}
	
	public function testNewProperty() {
		$r = new Request([
			"new" => "prop"
		]);

		$this->assertObjectHasAttribute("new", $r);
		$this->assertEquals("prop", $r->new);
	}

	public function testDontCollectPropertiesOnDebug() {
		$r = new Request([]);

		$this->assertNull($r->action);
		$this->assertNull($r->secure);
		$this->assertNull($r->method);
		$this->assertNull($r->get);
		$this->assertNull($r->post);
		$this->assertNull($r->raw);
		$this->assertNull($r->json);
		$this->assertNull($r->contentType);
		$this->assertNull($r->cookies);
		$this->assertNull($r->files);
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
