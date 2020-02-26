# PHP mini mvc

You start by having a Service object with some defined routes:

```php
$routes = [
	"/my/route" => Router::Controller(
		MyController::class, [/*constructor argument list*/]
	),
	...
];

$webServ = new Service($routes);
```

Optinally for debug:

```php
$request = new Request(
	/* passing arguments is Request's debug mode */
	[
		"property" => "value"
	]
);
$webServ->debug($request);
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
<?php

namespace Controllers;

use MVC\IController;

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
<?php

use MVC\Service;
use MVC\Router;
use Models\MyModel;
use Controllers\MyController;

$routes = [
	"/" => Router::Controller(
		MyController::class,
		[ new MyModel ]
	)
];

$webServ = new Service($routes);

echo $webServ->respond();

```