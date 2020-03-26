<?php

namespace Server;

use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase {

	public function normalText() {
		return [
			[""],
			["<p>normal</p>"],
			["<php>"],
			["?>"]
		];
	}
	
	public function phpText() {
		return [
			["text <?php echo 1 + 2; ?>", "text 3"],
			["<?php echo 1 + 2; ?>-<?php echo 2 + 3;", "3-5"],
			["<?php ?>", ""]
		];
	}

	public function phpVars() {
		return [
			[
				'<title><?php echo $title;?></title>',
				["title" => "Testing"],
				'<title>Testing</title>'
			],
			[
				'<?php echo $a;?>-<?php echo $b;?>',
				["a" => "test", "b" => "ing"],
				'test-ing'
			]
		];
	}

	public function errors() {
		return [
			["<?php 0/0; ?>", "Division by zero"],
			["<?php echo 1 + ?>", "ParseError"],
			["<?php echo \$var; ?>", "Undefined variable"]
		];
	}

	/**
	 * Test that a string with no php on it is returned as is
	 * @dataProvider normalText
	 */
	public function testNoTemplate(string $normaltext) {
		$template = new Template($normaltext);
		$r = $template->render();

		$this->assertEquals($normaltext, $r);
	}

	/**
	 * Test that php expressions get evaluated
	 * @dataProvider phpText
	 */
	public function testTemplate(string $template, string $result) {
		$template = new Template($template);
		$r = $template->render();

		$this->assertEquals($result, $r);
	}

	/**
	 * Test php expressions with outside variables
	 * using addVar and addVars functions
	 * @dataProvider phpVars
	 */
	public function testVar(string $template, array $vars, string $result) {
		$templateV = new Template($template);
		$templateVs = new Template($template);

		foreach ($vars as $var => $value)
			$templateV->addVar($var, $value);

		$templateVs->addVars($vars);

		$r = $templateV->render();
		$this->assertEquals($result, $r);
		
		$r = $templateVs->render();
		$this->assertEquals($result, $r);
	}

	/**
	 * Tests php expressions with errors
	 * @dataProvider errors
	 */
	public function testError(string $template, string $errorType) {
		$template = new Template($template);
		$r = $template->render();

		$this->assertRegexp("/$errorType/", $r);
	}
}

?>
