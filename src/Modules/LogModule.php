<?php

namespace Server\Modules;

use Server\State;

class LogModule extends Module {

	public $file;

	// TODO: timezone
	public function __construct(string $file) {
		$this->file = $file;
	}

	private function new_entry($text) {
		$date = date("Y-m-d [D] H:i:s [T O]");
		file_put_contents($this->file, "$date: $text\n", FILE_APPEND);
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