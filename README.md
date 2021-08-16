# Mini

Mini is a lightweight server module with no dependencies, and is easily extendable.

## Example

**Disclaimer:** I'm currently writing this documentation, so this is just to show how things look.

```php
<?php

namespace MyApp;

require 'vendor/autoload.php';

use Mini\Service;
use Mini\Environment;

use Mini\Request;
use Mini\Response;

use Mini\Tools\HTTP;

use Mini\Data\Cookie;

$env = (new Environment)
	->response(function(Response $response) {
		$response->cookie(new Cookie("name", "value"));
		return $response;
	});

class NameWrapper {
	public string $prefix;
	public string $suffix;

	public function __construct(string $prefix, string $suffix = "!") {
		$this->prefix = $prefix;
		$this->suffix = $suffix;
	}

	public function __invoke(string $name) {
		return (new Response)
			->status(200)
			->payload($this->prefix . $this->name . $this->suffix);
	}
}

$router = new Router(
	Route::define("/my/route", HTTP::GET, fn(Request $r) => Response::OK()),
	
	Route::define("/hey/@name", HTTP::GET | HTTP::POST, new NameWrapper("Hey, "))
		->omit_request()
		->environment($env),
	...
);

$debug_request = new Request([
	"action" => "/my/robert",
	"method" => HTTP::GET,
	...
]);

$service = new Service($routes);

$response = $service->respond($debug_request);

$service->debug->append("file.log");

/**
 * HTTP code: 200
 * body: "Hey, robert!"
 * cookie: "name=value"
 */
echo $response;

?>
```