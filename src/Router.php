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
		$this->tree = $this->node(null, []);
		foreach($routes as $r => $val)
			$this->tree = $this->_add(
				$this->tree, route_split($r), $val
			);
	}

	/* Node constructor */
	private function node($val = null, array $children = []) {
		return (object)[
			"val" => $val,
			"children" => $children
		];
	}

	/* res :: URLTree -> [String] -> Maybe a */
	private function _resolve(object $tree, array $path) {
		/* res (N v ch) [] = Just v */
		if (count($path) === 0)
			return $tree->val;

		$p = $path[0];
		$ps = array_slice($path, 1);

		/* res (N v [!p]) (p:ps) = Nothing */
		if (isset( $tree->children[$p] ) === false)
			return null;

		/* res (N v ch) (p:ps) = res (ch!!p) ps */
		return $this->_resolve(
			$tree->children[$p], $ps
		);
	}

	public function resolve(string $url) {
		return $this->_resolve($this->tree, route_split($url));
	}
    
	/* _add :: URLTree -> [String] -> a -> URLTree */
	private function _add(object $tree, array $path, $val) {
		/* add (N v ch) [] a = N a [] */
		if (count($path) === 0)
			return $this->node($val);

		$p = $path[0];
		$ps = array_slice($path, 1);

		/* add (N v [!p]) (p:ps) a = N v [p => add (N Undefined []) ps a] */
		if (isset( $tree->children[$p] ) === false) {
			$tree->children[$p] = $this->_add(
				$this->node(), $ps, $val
			);
			return $tree;
		}

		/* add (N v ch) (p:ps) = N v ((ch!!p) <- (add (ch!!p) ps a)) */
		$tree->children[$p] = $this->_add(
			$tree->children[$p], $ps, $val
		);
        	return $tree;
	}
}

?>
