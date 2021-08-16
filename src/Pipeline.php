<?php

namespace Mini;

class Pipeline {

	/** @var array<callable(mixed...):mixed> */
	public array $callables;

	/**
	 * @param array<callable(mixed...):mixed> $callables
	 */
	public function __construct(...$callables) {
		$this->callables = $callables;
	}

	/**
	 * @param callable(mixed...):mixed $callable
	 */
	public function then(callable $callable) : self {
		$this->callables[] = $callable;
		return $this;
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public function __invoke($value) {
		foreach ($this->callables as $callable)
			$value = $callable($value);
		return $value;
	}
}

?>