<?php

namespace Server;

use \Server\Controllers\Node;

class Service {

	private $router;
	private $env;

	public function __construct(?Router $router = null, ?Environment $env = null) {
		$this->router = $router ?? new Router;
		$this->env = $env ?? new Environment;
	}

	private function report(string $event_name) {
		try {
			return $this->env->report(...func_get_args());
		}
		// NOTE: disposes silently of errors to hide them from clients,
		// perhaps a debug mode should be added for this
		catch(\Exception $e) {}
		catch(\Error $e) {}

		// NOTE: returns panic just in case a response was expected,
		// perhaps events should have a unified output type
		return $this->panic();
	}

	public function respond(?Request $r) : Response {
		$r = $this->report("request", $r ?? new Request);

		if ($r->action) {
			$rsl = $this->report("resolution", $this->router->resolve($r->action));
			if ($rsl->failed)
				$resp = Response::notFound();
			else
				$resp = $this->execute($r, $rsl->value, $rsl->route_args);
		}

		return $this->report("response", $resp);
	}

	private function execute(Request $r, Node $node, array $route_args = []) : Response {
		$node->env = $this->env->extend($node->env);

		ob_start();
		try {
			$response = (new $node->cons($node->env))->__service_init($r, ...$route_args);
			$this->check_response($response);
		} catch (\Exception $e) {
			$this->report("exception", $e);
			$response = $this->panic();
		} catch (\Error $e) {
			$this->report("error", $e);
			$response = $this->panic();
		}

		// NOTE: you are not supposed to output anything directly
		$output = ob_get_clean();
		if(!empty($output))
			$this->report("exception", new \Exception("Controller produced output: $output"));

		return $response;
	}

	private function check_response($response) : void {
		if (!($response instanceof Response))
			throw new \Exception("Controller responded with a non Response type value");
	}

	private function panic() : Response {
		return Response::serverError();
	}
}

?>
