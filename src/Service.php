<?php

namespace Server;

use Cologger\Logger;

class Service {

	private $router;
	private $request;

	/* controllers for errors 404 and 500 */
	private $e_404;
	private $e_500;
       
	public $log = "";
	public $base_url = "";
	public static $template_path = "";

	public function __construct(array $routes = [], ?Request $debug = null) {
		$this->error404($routes);
		$this->error500($routes);

		$this->router = new Router($routes);
		$this->request = $debug ? $debug : new Request;
	}

	/* Sets up special 404 handler, if present. Otherwise use default */
	private function error404(array $routes) : void {
		if (isset($routes["<404>"]))
			$this->e_404 = $routes["<404>"];
		else
			$this->e_404 = self::SimpleController(
				function($r) { return Response::notFound(); }
			);
	}
	
	/* Sets up special 500 handler, if present. Otherwise use default */
	private function error500(array $routes) : void {
		if (isset($routes["<500>"]))
			$this->e_500 = $routes["<500>"];
		else
			$this->e_500 = self::SimpleController(
				function($r) { return Response::serverError(); }
			);
	}

	/* returns true if the base_url matches the prexix of the request and removes it, else false */
	private function strip_route_prefix() {
		$base = route_trim($this->base_url);
		$req = route_trim($this->request->action);

		$correct_prefix = $base == substr($req, 0, strlen($base));
		
		// strip base from request
		if ($correct_prefix)
			$this->request->action = substr($req, strlen($base));
		
		return $correct_prefix;
	}

	/* attempt to resolve and execute that resolution */
	public function respond() : Response {
		if ($this->strip_route_prefix()) {
			$resolution = $this->router->resolve($this->request->action);
			if (!$resolution->failed)
				return $this->execute(
					$resolution->value->name,
					$resolution->value->args,
					$resolution->route_args
				);
		}

		return $this->execute(
			$this->e_404->name,
			$this->e_404->args
		);
	}

	/* attempt to call controller */
	private function execute(string $name, array $args = [], array $route_args = []) : Response {
		ob_start();
		try {
			$resolved=[];
			foreach($args as $a)
				$resolved[] = (is_object($a) and get_class($a) == Constructable::class) ?
					$a->construct() : $a;

			$response = (new $name(...$resolved))->process($this->request, ...$route_args);
			$this->checkResponse($response);
		}
		catch (\Exception $e) {
			$this->logError($e);
			$response = $this->respondError();
		}
		catch (\Error $e) {
			$this->logError($e);
			$response = $this->respondError();
		}
		echo ob_get_clean();
		return $response;
	}

	private function logError($e) {
		if ($this->log)
			(new Logger($this->log))->error((string)$e);
	}

	/* attempt to use error 500 controller */
	private function respondError() : Response {
		$controller = $this->e_500->name;
		$args = $this->e_500->args;
		
		try {
			$response = (new $controller(...$args))->process($this->request);
			$this->checkResponse($response);
		}
		catch(\Exception $e) {
			$this->logError($e);
			$response = $this->panic();
		}
		catch(\Error $e) {
			$this->logError($e);
			$response = $this->panic();
		}
		return $response;
	}

	private function checkResponse($value) : void {
		assert(
			__NAMESPACE__ . "\\Response" ===  get_class($value),
			"Controller responded with a non Response type value"
		);
	}

	/* default "everything failed" response */
	private function panic() : Response {
		return Response::serverError();
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
