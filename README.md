# Mini

## Service

You start by having a Service object with some defined routes
```php
use Server\Service;
use Server\Controllers\Controller;

$routes = [
	"/my/route" => Controller::Node(MyController::class)
		->args(...),
	...
];

$service = new Service($routes);
```

Service takes in an Environment
```php
use Server\Service;
use Server\Controllers\Controller;

$routes = [
	"/my/route" => Controller::Node(MyController::class)
		->args(...),
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
	["property" => "value"]
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

### Special routes

#### Arguments

With "\<argument\>" you can tell the router when a section of the route can match with anything, and you can also retrieve it by just placing extra arguments in the process function. Also, any aditional route that is equal, but has the "\<argument\>" sections defined instead, results in a specification of the route. Any request will resolve to the more specific route if possible.
```php
"/path/with/<argument>/and/<argument>" => Service::SimpleController(
	function($r, $arg1, $arg2) { ... }
);

// will work with the above
"/path/with/1/and/2" => Service::SimpleController(
	function($r) { ... }
);
```

#### Errors

Using "<404>" and "<500>", you can define special controllers for these errors. 404 will get called when the request matches no defined routes. 500 will be called when any errors or exceptions pop up when executing user defined code. So what happens when this handler also fails? Simple, a fallback to the default solution. Keep in mind that these special indicators are not actually routes, so they do not accept the '/' character before or after.
```php
"<404>" => Service::SimpleController(
	function($r) { ... }
);

// will work with the above
"<500>" => Service::SimpleController(
	function($r) { ... }
);
```

## TODO:
- Finish documentation
- Improve code coverage of tests. Specially the Response class
- Improve logging

