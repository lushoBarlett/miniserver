# Mini

Mini is a lightweight server module with no dependencies, and is easily extendable.

## Example

**Disclaimer:** I'm currently writing this documentation, so this is just to show how things look.

```php
<?php

namespace MyApp;

require 'vendor/autoload.php';

use Server\Service;
use Server\Environment;

use Server\Request;
use Server\Response;

use Server\Controllers\Controller;
use Server\Controllers\ConstController;

class Argument {

	private $constant;
	private $suffix;
	public function __construct(Enviroment $env) {
		$this->constant = $env->constant("constant");
		$this->suffix = $env->provider("suffixer");
	}

	function get(Request $r) {
		$name = ($this->suffix)($r->args["name"]);
		return (new Response)
			->status(200)
			->payload("Hey, {$this->name} Constant is {$this->constant}.");
	}
}

$routes = [
	"/my/route" => ConstController::Node(Response::withStatus(200)),
	"/my/@name" => Controller::Node(Argument::class, ["constant" => 2]),
	...
];

$suffix = "!";

$env = new Environment([
	"constant" => 1,
	"#suffixer" => function(string $arg) use($suffix) {
		return "{$arg}{$suffix}";
	},
	"@module" => new SomeModule(...),
	...
]);

$debug_request = new Request([
	"action" => "/my/robert",
	"method" => Request::GET,
	...
]);

$service = new Service($routes, $env);

$response_object = $service->respond($debug_request);

echo $response_object; // Hey, robert! Constant is 2.

?>
```