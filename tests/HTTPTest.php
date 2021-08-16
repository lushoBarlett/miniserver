<?php

namespace Mini\Tools;

include "vendor/autoload.php";

use PHPUnit\Framework\TestCase;

class HTTPTest extends TestCase {

	public function testMatch() {
		$this->assertTrue(HTTP::match(HTTP::GET | rand(), HTTP::GET));
		$this->assertFalse(HTTP::match(HTTP::POST, rand() && ~HTTP::POST));
		$this->assertTrue(HTTP::match(HTTP::ANY, rand()));
	}

	public function testCast() {
		$this->assertEquals(HTTP::ANY,     HTTP::from_string("ANY"));
		$this->assertEquals(HTTP::GET,     HTTP::from_string("GET"));
		$this->assertEquals(HTTP::POST,    HTTP::from_string("POST"));
		$this->assertEquals(HTTP::PUT,     HTTP::from_string("PUT"));
		$this->assertEquals(HTTP::PATCH,   HTTP::from_string("PATCH"));
		$this->assertEquals(HTTP::DELETE,  HTTP::from_string("DELETE"));
		$this->assertEquals(HTTP::HEAD,    HTTP::from_string("HEAD"));
		$this->assertEquals(HTTP::OPTIONS, HTTP::from_string("OPTIONS"));
		$this->assertEquals(HTTP::TRACE,   HTTP::from_string("TRACE"));
		$this->assertEquals(HTTP::CONNECT, HTTP::from_string("CONNECT"));
	}
}

?>