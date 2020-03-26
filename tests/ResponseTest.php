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
		(string)$r;
		
		$this->assertEquals(123, http_response_code());
	}

	/**
	 * Tests the payload capability of the class
	 */
	public function testPayload() {
		$r = (new Response)->payload("text");
		$this->assertEquals("text", (string)$r);
	}
	
	# TODO:	ADD HEADER TESTING
	# 	ADD STATIC TESTING
}

ob_end_clean();

?>
