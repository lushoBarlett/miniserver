<?php

namespace Mini\Routing;

// TODO: optimize.
// 1. adapt implementation from functional to imperative
// 2. compile routes to a file tree to achieve O(1) instead of O(N + LogN)
//
// Is the latter really a good idea? The constant may be too big?
// Maybe this can be set as an option
class Router {

	private Node $tree;

	/**
	 * @param array<Route> $routes
	 */
	public function __construct(...$routes) {
		$this->tree = new Node;
		array_walk($routes, function(Route $route) {

			if ($route->name === null)
				throw new \ValueError("Cannot use route with no name");

			if ($route->controller === null)
				throw new \ValueError("Cannot use route with no controller");

			$this->tree = $this->_add($this->tree, Route::split($route->name), $route);
		});
	}

	/**
	 * @param array<string> $path
	 */
	private static function partition(array $path) : array {
		return [$path[0], array_slice($path, 1)];
	}

	/**
	 * @param array<string> $path
	 * @return array<int, Route>
	 */
	private function _resolve(Node $tree, array $path) : array {
		if (empty($path))
			return $tree->routes;

		list($p, $ps) = self::partition($path);

		// specific route defined gets priority
		if (isset($tree->children[$p]))
			return $this->_resolve($tree->children[$p], $ps);

		// argument route defined
		if (isset($tree->children[Route::Argument]))
			return $this->_resolve($tree->children[Route::Argument], $ps);
		
		return [];
	}

	/**
	 * @return array<int, Route>
	 */
	public function resolve(string $url) : array {
		return $this->_resolve($this->tree, Route::split($url));
	}
    
	/**
	 * @param array<string> $path
	 */
	private function _add(Node $tree, array $path, Route $route) : Node {
		if (empty($path))
			return $tree->override($route);

		list($p, $ps) = self::partition($path);

		// strip argument name
		if (Route::is_argument($p))
			$p = Route::Argument;

		$tree->children[$p] = $this->_add($tree->children[$p] ?? new Node, $ps, $route);

        	return $tree;
	}
}

?>