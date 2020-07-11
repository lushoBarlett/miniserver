<?php

namespace Server;

use Cologger\Logger;

class Service {

	private $router;

	// controllers for errors 404 and 500
	private $e404;
	private $e500;
       
	public $log_file;
	public $base_url;

	private function process_directives($conf) {
		$this->e404 = $conf["@404"] ?? ConstController::Node(Response::notFound());
		$this->e500 = $conf["@500"] ?? ConstController::Node(Response::serverError());

		$this->log_file = $conf["@log-file"] ?? null;
		$this->base_url = $conf["@base-url"] ?? null;
	}

	public function __construct(array $routes = [], array $conf = []) {
		$this->router = new Router($routes);
		$this->process_directives($conf);
	}

	// Returns true if the base_url matches the prefix of the request,
	// then removes it for validation. Else false
	private function strip_route_prefix(Request &$r) {
		$base = route_trim($this->base_url);
		$r->action = route_trim($r->action);

		$correct_prefix = $base == substr($req, 0, strlen($base));
		
		// strip base from request
		if ($correct_prefix)
			$this->request->action = substr($req, strlen($base));
		
		return $correct_prefix;
	}

	public function respond(?Request $r) : Response {
		$r = $r ?? new Request;

		if ($this->strip_route_prefix($r)) {
			$resolution = $this->router->resolve($r->action);
			if (!$resolution->failed)
				return $this->execute(
					$resolution->value->cons,
					$resolution->value->meta,
					$resolution->route_args
				);
		}

		return $this->execute($this->e404->cons, $this->e404->meta);
	}

	private function execute(string $cons, array $meta = [], array $route_args = []) : Response {
		ob_start();
		try {
			// TODO: change for provider logic
			$resolved = [];
			foreach($args as $a)
				$resolved[] = (is_object($a) and get_class($a) == Constructable::class) ?
					$a->construct() : $a;

			$response = (new $cons($this, $meta))->__service_init($this->request, ...$route_args);
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
		if ($this->log_file)
			(new Logger($this->log_file))->error((string)$e);
	}

	private function respondError(Request $r) : Response {
		$cons = $this->e500->cons;
		$meta = $this->e500->meta;
		
		try {
			$response = (new $cons($this, $meta))->__service_init($r);
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

	private function check_response($value) : void {
		if (get_class($value) !== Response::class)
			throw new \Exception("Controller responded with a non Response type value");
	}

	// default "everything failed" response
	private function panic() : Response {
		return Response::serverError();
	}
}

?>
