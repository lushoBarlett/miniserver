<?php

namespace Server;

function route_trim(string $route) : string {
	return trim($route, '/ ');
}

function route_split(string $route) : array {
	return explode('/', route_trim($route));
}

?>
