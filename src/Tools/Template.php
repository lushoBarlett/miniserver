<?php

namespace Mini\Tools;

class Template {

	private string $filename = "";
	private array $vars = [];

	public function __construct(string $filename) {
		$this->filename = $filename;
	}

	/**
	 * @param mixed $value
	 */
	public function declare(string $var, $value) : void {
		$this->vars[$var] = $value;
	}

	public function declare_all(array $variables) : void {
		$this->vars = array_merge($this->vars, $variables);
	}

	public function render() : string {
		// set scope for evaluation
		foreach($this->vars as $var => $value)
			${$var} = $value;

		// TODO: error handling
		\ob_start();
			/**
			 * @psalm-suppress UnresolvableInclude
			 * The success of this is left for the user to handle
			 */
			require $this->filename;
		return \ob_get_clean();
	}
}

?>