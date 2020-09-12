<?php

namespace Server\Modules;

use Server\State;

class LogModule extends Module {

	public $file;
	public $timezone;

	// TODO: timezone
	public function __construct(string $file) {
		$this->file = $file;
		$this->timezone = $timezone;
	}

	private function new_entry($text) {
		$date = date("Y-m-d [D] H:i:s [T O]");
		$content = file_get_contents($file);
		if ($content !== false)
			file_put_contents($file, "$content\n$date: $text");
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