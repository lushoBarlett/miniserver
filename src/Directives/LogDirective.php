<?php

namespace Server\Directives;

class LogDirective extends Directive {
	
	private $log;

	public function __construct(string $filename) {
		$this->log = new Logger($filename);
	}

	public function exception_event(\Exception $e) {
		$this->log->error((string)$e);
	}

	public function error_event(\Error $e) {
		$this->log->error((string)$e);
	}
}

?>
