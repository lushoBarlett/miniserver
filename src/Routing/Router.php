<?php

namespace Server\Routing;

// TODO: optimize.
// 1. adapt implementation from functional to imperative
// 2. compile routes to a file tree to achieve O(1) instead of O(N + LogN)
//
// Is the latter really a good idea? The constant may be too big?
// Maybe this can be set as an option
class Router {

	private $tree;

	public function __construct(array $routes = []) {
		$this->tree = new Node;
		foreach($routes as $r => $cons)
			$this->tree = $this->_add($this->tree, Route::split($r), $cons, $r);
	}

	private static function partition(array $path) {
		return [$path[0], array_slice($path, 1)];
	}

	private function _resolve(Node $tree, array $path) {
		if (empty($path)) {
			if ($tree->value !== null)
				return new Resolution($tree->value, $tree->route);

			return null;
		}

		list($p, $ps) = self::partition($path);

		// specific route defined gets priority
		if (isset($tree->children[$p]))
			return $this->_resolve($tree->children[$p], $ps);

		// argument route defined
		if (isset($tree->children[Route::ARGUMENT]))
			return $this->_resolve($tree->children[Route::ARGUMENT], $ps);
		
		return null;
	}

	public function resolve(string $url) {
		$res = $this->_resolve($this->tree, Route::split($url));
		$res->args = Route::arguments($res->route, $url);
		return $res;
	}
    
	private function _add(Node $tree, array $path, $value, string $route) {
		if (empty($path)) {
			$tree->value = $value;
			$tree->route = $route;
			return $tree;
		}

		list($p, $ps) = self::partition($path);

		// strip argument name
		$p = !Route::is_argument($p) ? $p : Route::ARGUMENT;

		$tree->children[$p] = $this->_add(
		    $tree->children[$p] ?? new Node, $ps, $value, $route);

        	return $tree;
	}
}

?>