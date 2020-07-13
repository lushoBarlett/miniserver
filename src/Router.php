<?php

namespace Server;

// TODO: optimize.
// 1. adapt implementation from functional to imperative
// 2. compile routes to a file tree to achieve O(1) instead of O(N + LogN)
//
// Is the latter really a good idea? The constant may be too big?
// Maybe this can be set as an option
class Router {

	const ARGUMENT = "@";

	/*
	 * # Abstraction
	 * data URLTree Val = Node Val * [URLTree Val]
	 * data Val a = Defined a | Undefined
	 *
	 * # Implementation
	 * $node = [ "val" => ?$val, "children" => $children ];
	 */
	private $tree;

	public function __construct(array $routes = []) {
		$this->tree = self::Node();
		foreach($routes as $r => $val)
			$this->tree = $this->_add($this->tree, self::route_split($r), $val);
	}

	private function partition(array $path) {
		return [$path[0], array_slice($path, 1)];
	}

	/* res :: URLTree -> [String] -> Maybe a */
	private function _resolve(object $tree, array $path) {
		if (!count($path)) {
			if ($tree->val !== null)
				return new Resolution($tree->val);

			return Resolution::failed();
		}

		list($p, $ps) = $this->partition($path);

		// specific route defined gets priority
		if (isset($tree->children[$p])) {
			return $this->_resolve($tree->children[$p], $ps);
		}

		// argument route defined
		if (isset($tree->children[self::ARGUMENT])) {
			$res = $this->_resolve($tree->children[self::ARGUMENT], $ps);
			$res->route_args[] = $p;

			return $res;
		}
		
		return Resolution::failed();
	}

	public function resolve(string $url) {
		$res = $this->_resolve($this->tree, self::route_split($url));
		
		// reverse the arguments, they were inserted backwards
		$res->route_args = array_reverse($res->route_args);
		
		return $res;
	}
    
	/* _add :: URLTree -> [String] -> a -> URLTree */
	private function _add(object $tree, array $path, $val) {
		if (!count($path))
			return self::Node($val, $tree->children);

		list($p, $ps) = $this->partition($path);

		if (!isset($tree->children[$p])) {
			$tree->children[$p] = $this->_add(self::Node(), $ps, $val);
			return $tree;
		}

		$tree->children[$p] = $this->_add($tree->children[$p], $ps, $val);
        	return $tree;
	}

	private static function Node($val = null, array $children = []) {
		return (object)[
			"val" => $val,
			"children" => $children
		];
	}

	public static function route_trim(string $route) : string {
		return trim($route, '/ ');
	}

	public static function route_split(string $route) : array {
		return explode('/', self::route_trim($route));
	}

	public static function route_arguments(string $route) : array {
		return array_map(
			function (string $r) : bool { return $r === self::ARGUMENT; },
			self::route_split($route)
		);
	}
}

?>
