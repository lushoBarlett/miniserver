<?php

namespace Server;

use \Server\Controllers\Node;
use \Server\Routing\Router;

class Service {

	private $router;
	private $env;

	public function __construct(?Router $router = null, ?Environment $env = null) {
		$this->router = $router ?? new Router;
		$this->env = $env ?? new Environment;
	}

	// ======================================================================= //
	// NOTE: the way we work with a State and the directives that access it is //
	//                                                                         //
	// 1. Something happens                                                    //
	// 2. Service executes default behaviour                                   //
	// 3. Let directives do whatever                                           //
	//                                                                         //
	// The only exception is in report where we just fallback to               //
	// panic response, because directive calling has failed.                   //
	// After a directive fails, another report might happen where errors       //
	// may be read.                                                            //
	// ======================================================================= //

	private function report(string $event_name, State $s) : State {
		try {
			return $this->env->report($event_name, $s);
		}
		// TODO: better error reporting
		// add a special exception type
		// to tell that this is a directive error
		catch(\Exception $e) {
			$s->error_list[] = $e;
			$s->response = $this->panic();
		}
		catch(\Error $e) {
			$s->error_list[] = $e;
			$s->response = $this->panic();
		}

		return $s;
	}

	public function respond(?Request $r = null) : Response { 
		$s = new State($r ?? new Request);
		$s = $this->report("request", $s);

		// NOTE: null action is considered error 404
		if ($s->request->action === null) {
			$s->response = Response::notFound();
			return $this->report("response", $s);
		}

		$s->resolution = $this->router->resolve($s->request->action);
		$s = $this->report("resolution", $s);

		if (!$s->resolution) {
			$s->response = Response::notFound();
			$s = $this->report("response", $s);
			return $s->response;
		}

		// set request route arguments
		$s->request->args = $s->resolution->args;
		$s = $this->execute($s);
		$s = $this->report("response", $s);

		return $s->response;
	}

	private function execute(State $s) : State {
		$node = $s->resolution->value;
		$node->env = $this->env->extend($node->env);

		ob_start();
		try {
			$s->response = (new $node->cons($node->env))->__service_init($s->request);
		} catch (\Exception $e) {
			$s->error_list[] = $e;
			$s->response = $this->panic();

			$s = $this->report("exception", $s);
		} catch (\Error $e) {
			$s->error_list[] = $e;
			$s->response = $this->panic();

			$s = $this->report("error", $s);
		}
		$output = ob_get_clean();

		// NOTE: you are not supposed to output to stdout
		if(!empty($output)) {
			$s->error_list[] = new \Exception("Controller produced output: $output");
			$s = $this->report("exception", $s);
		}

		return $s;
	}

	private function panic() : Response {
		return Response::serverError();
	}
}

?>