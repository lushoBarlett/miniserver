<?php

namespace Mini\Data;

require "vendor/autoload.php";

use PHPUnit\Framework\TestCase;

class FileTest extends TestCase {

	public function testNoRulesComplies() {
		$f = new File("","","",0,0);
		$this->assertTrue($f->complies());
	}
	
	public function testSomeRulesComplies() {
		$f = new File("name.name","","",0,0);

		$f->add_rule("/\./");
		$f->add_rule("/(name)?/");
		$f->add_rule("/name$/");

		$this->assertTrue($f->complies());
	}
	
	public function testSomeRulesFails() {
		$f = new File("name.name","","",0,0);

		$f->add_rule("/name/");
		$f->add_rule("/f/");

		$this->assertFalse($f->complies());
	}

	// TODO: test File::save
}

?>