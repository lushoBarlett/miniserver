<?php

namespace Mini\Routing;

use Mini\Tools\HTTP;
use Mini\Environment;
use Mini\Request;
use Mini\Response;

class Route {

	public const Argument = '@';
	public const String   = "string";
	public const Int      = "int";
	public const Float    = "float";

	public ?string $name = null;
	/** @var array<string> */
	public array $types = [];
	public int $methods = HTTP::ANY;
	public bool $request = true;
	public Environment $environment;

	/** @var null|callable(mixed...):Response */
	public $controller = null;

	public function __construct() {
		$this->environment = new Environment;
	}

	public function name(string $name) : self {
		$this->name = $name;
		return $this;
	}

	public function parameter_type(string $parameter, string $type) : self {
		$this->types[$parameter] = $type;
		return $this;
	}

	public function methods(int $methods) : self {
		$this->methods = $methods;
		return $this;
	}

	public function omit_request() : self {
		$this->request = false;
		return $this;
	}

	public function environment(Environment $environment) : self {
		$this->environment = $environment;
		return $this;
	}

	public function controller(callable $controller) : self {
		$this->controller = $controller;
		return $this;
	}

	public function arguments(string $action) : array {
		if ($this->name === null)
			throw new \Exception("Cannot extract arguments, route name is missing");

		$route = self::split($this->name);
		$action = self::split($action);

		if (count($route) != count($action))
			throw new \ValueError("Route and action do not match");

		$arguments = [];
		for($i = 0; $i < count($route); $i++) {
			if (self::is_argument($route[$i])) {
				$name = self::argument_name($route[$i]);
				$arguments[$name] = $action[$i];

				if (isset($this->types[$name]))
					settype($arguments[$name], $this->types[$name]);
			}
		}

		return $arguments;
	}

	public static function define(string $route, int $methods, callable $controller) : self {
		return (new self)
			->name($route)
			->controller($controller)
			->methods($methods);
	}

	public static function forall(string $route, callable $controller) : self {
		return (new self)
			->name($route)
			->controller($controller)
			->methods(HTTP::ANY);
	}

	public static function trim(string $route) : string {
		return trim($route, '/ ');
	}

	public static function split(string $route) : array {
		return explode('/', self::trim($route));
	}

	public static function is_argument(string $p) : bool {
		return !strncmp($p, self::Argument, 1);
	}

	public static function argument_name(string $p) : string {
		if (!self::is_argument($p))
			throw new \ValueError("Route is not an argument");

		if (strlen($p) <= 1)
			throw new \ValueError("Parameter has no name");

		return substr($p, 1);
	}
}

?>