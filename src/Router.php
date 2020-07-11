<?php

namespace Server;

use function Server\route_split;

class Router {

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
			$this->tree = $this->_add($this->tree, route_split($r), $val);
	}

	/* res :: URLTree -> [String] -> Maybe a */
	private function _resolve(object $tree, array $path) {
		if (!count($path)) {
			if ($tree->val !== null)
				return self::Resolution($tree->val);

			return self::not_resolved();
		}

		$p = $path[0];
		$ps = array_slice($path, 1);

		// specific route defined gets priority
		if (isset($tree->children[$p])) {
			return $this->_resolve($tree->children[$p], $ps);
		}

		// argument route defined
		// FIXME: change argument sintax to have '@' prefix
		if (isset($tree->children["<argument>"])) {
			$res = $this->_resolve($tree->children["<argument>"], $ps);
			$res->route_args[] = $p;

			return $res;
		}
		
		return self::not_resolved();
	}

	public function resolve(string $url) {
		$res = $this->_resolve($this->tree, route_split($url));
		
		// reverse the arguments, they were inserted backwards
		$res->route_args = array_reverse($res->route_args);
		
		return $res;
	}
    
	/* _add :: URLTree -> [String] -> a -> URLTree */
	private function _add(object $tree, array $path, $val) {
		if (!count($path))
			return self::Node($val, $tree->children);

		$p = $path[0];
		$ps = array_slice($path, 1);

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

	private static function Resolution($controller, array $args = [], bool $failed = false) {
		return (object)[
			"value" => $controller,
			"route_args" => $args,
			"failed" => $failed
		];
	}

	private static function not_resolved() {
		return self::Resolution("", [], true);
	}
}

?>
