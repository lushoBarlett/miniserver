<?php

namespace Server;

function route_trim(string $route) : string {
	return trim($route, '/ ');
}

function route_split(string $route) : array {
	return explode('/', route_trim($route));
}

function route_arguments(string $route) : array {
	return array_map(
		function (string $r) : bool { return $r === "<argument>"; },
		route_split($route)
	);
}

function template_path(string $filename) : string {
	return
		Service::$template_path ?
		rtrim(Service::$template_path, '/') . "/$filename" :
		rtrim($_SERVER['DOCUMENT_ROOT'], '/') . "/templates/$filename";
}

function load_template(string $filename, array $vars = []) {
	$template = new Template(
		file_get_contents(template_path($filename))
	);

	$template->addVars($vars);
	echo $template->render();
}

?>
