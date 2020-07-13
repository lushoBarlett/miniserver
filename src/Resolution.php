<?php

namespace Server;

class Resolution {

	public $cons;
	public $args;
	public $fail;

	public function __construct($cons, array $args = [], bool $fail = false) {
		$this->cons = $cons;
		$this->args = $args;
		$this->fail = $fail;
	}

	public static function failed() {
		return new self("", [], true);
	}
}

?>
