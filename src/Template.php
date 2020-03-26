<?php

namespace Server;

class Template {

	private $template = "";
	private $vars = [];

	public function __construct(string $template) {
		$this->template = $template;
	}

	public function addVar(string $var, $value) : void {
		$this->vars = array_merge(
			$this->vars,
			[$var => $value]
		);
	}

	public function addVars(array $variables) : void {
		$this->vars = array_merge(
			$this->vars,
			$variables
		);
	}

	public function render() : string {
		/* Set scope for evaluation */
		foreach($this->vars as $var => $value)
			${$var} = $value;

		ob_start();
			try{
				eval("?>{$this->template}");
			}
			catch(\Exception $e) {
				echo $e;
			}
			catch(\Error $e) {
				echo $e;
			}
			finally {
				$output = ob_get_contents();
			}
		ob_end_clean();
		
		return $output;
	}
}

?>
