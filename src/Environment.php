<?php

namespace Server;

class Environment {

	private $directives = [];
	private $providers = [];
	private $constants = [];

	public function __construct(array $env) {
		// TODO: proper error handling
		foreach($env as $code => $value) {
			assert(!empty($code));
			assert($code != '#');
			assert($code != '@');

			switch($code[0]) {
			case '@':
				assert($value instanceof IDirective);
				$this->directives[substr($code, 1)] = $value;
				break;
			case '#':
				// TODO: allow classes?
				assert(is_callable($value));
				$this->providers[substr($code, 1)] = $value;
				break;
			default:
				$this->constants[$code] = $value;
			}
		}
	}

	public function directive(string $d) : ?IDirective {
		return $this->directives[$d] ?? null;
	}

	public function provider(string $p) : ?callable {
		return $this->providers[$p] ?? null;
	}

	public function constant(string $c) {
		return $this->constants[$c] ?? null;
	}

	public function extend(self $env) : self {
		$this->directives = array_merge($this->directives, $env->directives);
		$this->providers  = array_merge($this->providers,  $env->providers);
		$this->constants  = array_merge($this->constants,  $env->constants);
		return $this;
	}

	public function inyect_constants(Template $t) : Template {
		$t->add_vars($this->constants);
		return $t;
	}

	public function inyect_providers(Template $t) : Template {
		$t->add_vars($this->providers);
		return $t;
	}

	public function report(string $e_name, array $args = []) {
		foreach($this->directives as $d) {
			$result = $d->{"{$e_name}_event"}(...$args);
			// NOTE: does not work with intentional null result
			if($result)
				$args[0] = $result;
		}
	}
}

?>
