<?php

namespace Mini\Tools;

class HTTP {

	public const ANY     = ~0;
	public const GET     = 1 << 0;
	public const POST    = 1 << 1;
	public const PUT     = 1 << 2;
	public const PATCH   = 1 << 3;
	public const DELETE  = 1 << 4;
	public const HEAD    = 1 << 5;
	public const OPTIONS = 1 << 6;
	public const TRACE   = 1 << 7;
	public const CONNECT = 1 << 8;

	public static function match(int $allowed, int $got) : bool {
		return (bool)($allowed & $got);
	}

	public static function from_string(string $method) : int {
		$class = self::class;
		return constant("$class::$method");
	}
}

?>