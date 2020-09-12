<?php

namespace Server\Directives;

use Server\State;

class LogDirective extends Directive {

	public $file;

	public function __construct(string $file) {
		$this->file = $file;
	}

	private function new_entry($text) {
		$content = file_get_contents($file);
		if ($content !== false)
			file_put_contents($file, "$content\n$text");
	}

	public function error(State $s) : State {
		$this->new_entry(end($s->error_list));
		return $s;
	}

	public function except(State $s) : State {
		$this->new_entry(end($s->error_list));
		return $s;
	}
}

?>