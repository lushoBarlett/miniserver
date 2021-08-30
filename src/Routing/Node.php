<?php

namespace Mini\Routing;

use Mini\Tools\HTTP;

class Node {

	/** @var array<int, Route> */
	public array $routes = [];
	public array $children;

	public function __construct(Route $route = null, array $children = []) {
		$this->children = $children;

		if ($route)
			$this->override($route);
	}

	public function override(Route $route) : self {
		foreach ([
			HTTP::GET,
			HTTP::POST,
			HTTP::PUT,
			HTTP::PATCH,
			HTTP::DELETE,
			HTTP::HEAD,
			HTTP::OPTIONS,
			HTTP::TRACE,
			HTTP::CONNECT
		] as $method)
			if (HTTP::match($route->methods, $method))
				$this->routes[$method] = $route;
		
		return $this;
	}
}

?>