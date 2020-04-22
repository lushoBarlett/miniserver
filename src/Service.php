<?php

namespace Server;

use Cologger\Logger;

class Service {

	private $router;
	private $request;
       
	public $log = null;

	public function __construct(array $routes = [], ?Request $debug = null) {
		$this->router = new Router($routes);
		$this->request = $debug ? $debug : new Request;
	}

	public function respond() : Response {
		$resolution = $this->router->resolve($this->request->action);

		if ($resolution->failed)
			return Response::notFound();

		ob_start();
		try {
			$class = $resolution->value->name;
			$controller = new $class(...$resolution->value->args);

			$response = $controller->process($this->request, ...$resolution->route_args);

			assert(
				__NAMESPACE__ . "\\Response",  get_class($response),
				"Controller responded with a non Response type value"
			);
		}
		catch (\Exception $e) {
			if ($this->log !== null)
				(new Logger($this->log))->error((string)$e);

			$response = Response::serverError();
		}
		catch (\Error $e) {
			if ($this->log !== null)
				(new Logger($this->log))->error((string)$e);

			$response = Response::serverError();
		}
		echo ob_get_clean();
		return $response;
	}

	public static function Controller(string $class, array $params = []){
		return (object)[
			"name" => $class,
			"args" => $params
		];
	}

	public static function SimpleController(callable $processor) {
		return (object)[
			"name" => SimpleController::class,
			"args" => [$processor]
		];
	}
}

?>
