<?php

namespace Mini;

use PHPUnit\Framework\TestCase;

use Mini\Data\Cookie;
use Mini\Data\Header;

class ResponseTest extends TestCase {

	public function testStatus() {
		$r = (new Response)->status(123);
		$this->assertEquals(123, $r->status);
		(string)$r;
		
		$this->assertEquals(123, http_response_code());
	}

	public function testPayload() {
		$r = (new Response)->payload("text");
		$this->assertEquals("text", $r->payload);
		$this->assertEquals("text", (string)$r);
	}
	
	public function testRedirect() {
		$r = (new Response)->redirect("/url");
		$this->assertEquals("/url", $r->redirect);
	}
	
	public function testCookie() {
		$c1 = (new Cookie("name1", "value"))
				->expires(60)
				->secure()
				->http_only();

		$c2 = (new Cookie("name2"))
				->expires(-1)
				->http_only();

		$r = Response::withCookies($c1, $c2);
		
		$this->assertEquals([$c1, $c2], $r->cookies);
	}

	public function testHeaders() {
		$headers = [new Header("SomeHeader", "val"), new Header("OtherHeader", "val2")];
		$r = Response::withHeaders(...$headers);
		$this->assertEquals($headers, $r->headers);
	}
}

?>