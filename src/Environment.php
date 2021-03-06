<?php

namespace Server;

use Server\Modules\Module;

class Environment {

	private $modules = [];
	private $providers = [];
	private $constants = [];

	public function __construct(array $env = []) {
		// TODO: error handling
		foreach($env as $code => $value) {
			switch($code[0]) {
			case '@':
				// should be a module
				$this->modules[substr($code, 1)] = $value;
				break;
			case '#':
				// should be a callable
				$this->providers[substr($code, 1)] = $value;
				break;
			default:
				$this->constants[$code] = $value;
			}
		}
	}

	public function module(string $d) : ?Module {
		return $this->modules[$d] ?? null;
	}

	public function provider(string $p) : ?callable {
		return $this->providers[$p] ?? null;
	}

	public function constant(string $c) {
		return $this->constants[$c] ?? null;
	}

	// NOTE: creates new environment in case you wanna answer another request
	// it will not override this environment
	public function extend($env) : self {
		$env = $env ?? new self;
		if (is_array($env))
			$env = new self($env);

		$e = new self;

		$e->modules   = array_merge($this->modules,   $env->modules  );
		$e->providers = array_merge($this->providers, $env->providers);
		$e->constants = array_merge($this->constants, $env->constants);
		return $e;
	}

	public function inyect_constants(Template $t) : Template {
		$t->add_vars($this->constants);
		return $t;
	}

	public function inyect_providers(Template $t) : Template {
		$t->add_vars($this->providers);
		return $t;
	}

	public function report(string $e_name, State $s) : State {
		foreach($this->modules as $d)
			$s = $d->{$e_name}($s);

		return $s;
	}
}

?>