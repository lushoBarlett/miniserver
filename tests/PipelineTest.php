<?php

namespace Mini;

include "vendor/autoload.php";

use PHPUnit\Framework\TestCase;

class PipelineTest extends TestCase {

	public function testPipeline() {
		$id = fn($x) => $x;
		$plus1 = fn($x) => $x + 1;
		$string = fn($x) => (string)$x;
		$split = fn($x) => [$x, $x, $x, $x];
		$concat = fn($xs) => implode("", $xs);

		$end = (new Pipeline)
			->then($split)
			->then($concat);

		$pipeline = new Pipeline($id, $plus1, $string, $end);

		$this->assertEquals("1111", $end("1"));
		$this->assertEquals("2222", $pipeline(1));
	}
}

?>