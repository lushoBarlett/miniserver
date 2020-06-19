<?php

namespace Server;

use PHPUnit\Framework\TestCase;

/* 
 * output buffer for this test because of header management in Response
 * clashing with the phpunit test output
 */
ob_start();

class ResponseTest extends TestCase {

	/**
	 * Tests the status setting capability of the class
	 */
	public function testStatus() {
		$r = (new Response)->status(123);
		$this->assertEquals(123, $r->get_status());
		(string)$r;
		
		$this->assertEquals(123, http_response_code());
	}

	/**
	 * Tests the payload capability of the class
	 */
	public function testPayload() {
		$r = (new Response)->payload("text");
		$this->assertEquals("text", $r->get_payload());
		$this->assertEquals("text", (string)$r);
	}
	
	public function testRedirect() {
		$r = (new Response)->redirect("/url");
		$this->assertEquals("/url", $r->get_redirect());
	}
	
	public function testCookie() {
		$r = (new Response)
			->cookie("name1", "value", 60, true, true)
			->cookie("name2", "", -1, false, true);
		
		$this->assertEquals(
			[
				"name1" => [
					"value" => "value",
					"expire" => 60,
					"secure" => true,
					"httponly" => true
				],
				"name2" => [
					"value" => "",
					"expire" => -1,
					"secure" => false,
					"httponly" => true
				]
			],
			$r->get_cookies()
		);
	}

	public function testHeaders() {
		$r = (new Response)
			->header("SomeHeader", "val")
			->header("OtherHeader", "val2");

		$this->assertEquals(
			["SomeHeader" => "val", "OtherHeader" => "val2"],
			$r->get_headers()
		);
	}
	
	# TODO:	ADD HEADER TESTING
	# 	ADD STATIC TESTING
}

ob_end_clean();

?>
