# PHP server

You start by having a Service object with some defined routes
```php
use Server\Service;

$routes = [
	"/my/route" => Service::Controller(
		MyController::class, [/*constructor argument list*/]
	),
	...
];

$service = new Service($routes);
```

Optinally for debug
```php
use Server\Request;
use Server\Service;

$request = new Request(
	/* passing arguments is Request's debug mode */
	[
		"property" => "value"
	]
);
$service = new Service($routes, $request);
```

Then you tell the service to respond based on the Request
```php
$response = $service->respond();

/* optionally add or modify the Response object */

echo $response; /* string casting method takes care of the rest */
```

Simply ensure that your Controllers get loaded to the file where the Service objects gets instantiated, where your put your controllers and what they do does not matter. It is recommended that they exist in a folder named `controllers`, each with its own file, so that you can autoload them easily.

Example Controller
```php
namespace Controllers;

use Server\IController;

class MyController implements IController {
	
	private $model;
	
	public function __construct(MyModel $model) {
		$this->model = $model;
	}
	
	public function process(Request $request) : Response {
		$id = $this->sanitizeId(
			$request->payload->id
		);
		$name = $this->model->name($id);
		return Response::text("Hello $name!");
	}
	
	private function sanitizeId($id) {...}
}
```

Example index.php
```php
use Server\Service;
use Models\MyModel;
use Controllers\MyController;

$routes = [
	"/" => Service::Controller(
		MyController::class, [new MyModel]
	)
];

$service = new Service($routes);

echo $service->respond();
```

You can use `Service::SimpleController` and pass a closure as a short-hand for short controllers
```php
$routes = [
	"/" => Service::SimpleController(
		function($r) { return Response::withText("go back"); }
	)
];

```

### Logging

The Service class logs errors only (for now), and by default it doesn't log anything. To change that write something like this
```php
$service = new Service($routes);
$service->log = "myLogFile.log";
```

### Templates

The Response object knows how to read HTML code and evaluate it with a specified scope and render it _before_ printing it. It takes in a string and a list of identifier and value pairs.
```php
Response::withTemplate(
	"<h1><php? echo $t; ?></h1>",
	["t" => "My Title"]
);
```

## TODO:
- Finish documentation
- Improve code coverage of tests. Specially the Response class
- Special route for errors like 404 and 500
- Improve logging

## Cool idea
- Using a REST-full style app, make a game where the different routes are rooms, or places. Navigating them can unlock information to further explore the server's routes. Try using JWTs as the keys.
