<?php

namespace Server;

use Cologger\Logger;

class Service {

	private $router;
	private $env;

	public function __construct(array $routes = [], ?Environment $env = null) {
		$this->router = new Router($routes);
		$this->env = $env ?? new Environment;
	}

	private function report_event(string $event_name) {
		return $this->env->report($event_name, array_slice(func_get_args(), 1));
	}

	public function respond(?Request $r) : Response {
		$r = $this->report_event("request", $r ?? new Request);

		if ($r->action) {
			$rsl = $this->router->resolve($r->action);
			if (!$rsl->failed)
				return $this->execute(
					$r,
					$rsl->value->cons,
					$this->env->extend($rsl->value->env),
					$rsl->route_args
				);
		}

		return $this->report_event("response", Response::notFound());
	}

	private function execute(Request $r, string $cons, Environment $env, array $route_args = []) : Response {
		ob_start();
		try {
			$response = (new $cons($env))->__service_init($r, ...$route_args);
			$this->check_response($response);
		}
		catch (\Exception $e) {
			$this->report("exception", $e);
			$response = $this->panic();
		}
		catch (\Error $e) {
			$this->report("error", $e);
			$response = $this->panic();
		}

		// FIXME: what's this for?
		echo ob_get_clean();

		return $response;
	}

	private function check_response($value) : void {
		if ($value instanceof Response)
			throw new \Exception("Controller responded with a non Response type value");
	}

	private function panic() : Response {
		return Response::serverError();
	}
}

?>
