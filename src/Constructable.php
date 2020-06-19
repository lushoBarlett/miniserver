<?php

namespace Server;

class Constructable {

	private $class;
	private $maybe_constructables;

	public function __construct(string $class) {
		$this->class = $class;
		$this->maybe_constructables = array_slice(func_get_args(), 1);
	}

	public function construct() {
		$dependencies = [];
		foreach($this->maybe_constructables as $mc)
			$dependencies[] =
				(is_object($mc) and get_class($mc) == self::class) ?
				$mc->construct() : $mc;

		return new $this->class(...$dependencies);
	}
}

?>
