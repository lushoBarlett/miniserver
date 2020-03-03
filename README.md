# PHP server

You start by having a Service object with some defined routes:

```php
use Server\Service;

$routes = [
	"/my/route" => Service::Controller(
		MyController::class, [/*constructor argument list*/]
	),
	...
];

$webServ = new Service($routes);
```

Optinally for debug:

```php
use Server\Request;
use Server\Service;

$request = new Request(
	/* passing arguments is Request's debug mode */
	[
		"property" => "value"
	]
);
$webServ = new Service($routes, $request);
```

Then you tell the service to respond based on the Request:

```php
$response = $webServ->respond();

/* optionally add or modify the Response object */

echo $response; /* string casting method takes care of the rest */
```

Simply ensure that your Controllers get loaded to the file where the Service objects gets instantiated, where your put your controllers and what they do does not matter. It is recommended that they exist in a folder named `controllers`, each with its own file, so that you can autoload them easily.

Example Controller:

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

Example index.php:

```php
use Server\Service;
use Models\MyModel;
use Controllers\MyController;

$routes = [
	"/" => Service::Controller(
		MyController::class,
		[ new MyModel ]
	)
];

$webServ = new Service($routes);

echo $webServ->respond();
```

You can use `Service::SimpleController` and pass a closure as a short-hand for short controllers.

```php
$routes = [
	"/" => Service::SimpleController(
		function($r) { return Response::withText("go back"); }
	)
];

```

## TODO:
*Some things I want to add soon but I'm in a hurry so I didn't yet*
- Improve code coverage of tests. Right now I know it should work and that's fine. Response is a bother to test
- Router options. Such as *native* redirection -instead of achieving this through a trivial controller-, route parameters, use route as is (not formatting it or internally changing it)
- Special route for 404, and probably 500 as well
- Template rendering utility with directives and model data (ain't nobody got time for that)
- Maybe a Service Selection System (SSSounds nice) where you can pick and choose which logic -aka Service- to use, given some conditions. Just maybe...

## Cool ideas
- Using a REST-full style app -because horizontal scalability-, make a game where the different routes are like rooms to explore and navigating through it can unlock information to further explore the server's routes. Use JWTs as the keys to prevent cheating. The story and the goal are up to you.
