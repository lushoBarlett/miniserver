<?php

namespace Server\Routing;

class Route {

	const ARGUMENT = '@';

	public static function trim(string $route) : string {
		return trim($route, '/ ');
	}

	public static function split(string $route) : array {
		return explode('/', self::trim($route));
	}

	public static function arguments(string $route, string $action) : array {
		$route = self::split($route);
		$action = self::split($action);

		// TODO: error handling
		assert(!empty($route));
		assert(count($route) == count($action));

		$args = [];
		for($i = 0; $i < count($route); $i++) {
			if (self::is_argument($route[$i]))
				$args[self::argument_name($route[$i])] = $action[$i];
		}

		return $args;
	}

	public static function is_argument(string $p) : bool {
		return !strncmp($p, self::ARGUMENT, 1);
	}

	public static function argument_name(string $p) : string {
		// TODO: error handling
		assert(self::is_argument($p));
		assert(strlen($p) > 1);
		return substr($p, 1);
	}
}

?>