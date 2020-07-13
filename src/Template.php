<?php

namespace Server;

class Template {

	private $filename = "";
	private $vars = [];

	public function __construct(string $filename) {
		$this->filename = $filename;
	}

	public function add_var(string $var, $value) : void {
		$this->vars[$var] = $value;
	}

	public function add_vars(array $variables) : void {
		$this->vars = array_merge($this->vars, $variables);
	}

	public function render() : string {
		// set scope for evaluation
		foreach($this->vars as $var => $value)
			${$var} = $value;

		ob_start();
			include $this->filename;
		return ob_get_clean();
	}
}

?>
