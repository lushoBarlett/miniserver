<?php

namespace Mini;

use Mini\Routing\Router;
use Mini\Routing\Route;
use Mini\Tools\Debug;
use Mini\Tools\HTTP;

class Service {

	private Router $router;
	private Environment $environment;

	public Debug $debug;

	public function __construct(Router $router = null, Environment $environment = null) {
		$this->router = $router ?? new Router;
		$this->environment = $environment ?? new Environment;
		$this->debug = new Debug;
	}

	private function panic() : Response {
		$this->debug->print("Service panicked.");
		return Response::serverError();
	}

	private function fail(Environment $first, Environment $second = null) : Response {
		$response = $first->pipe_fail(Response::serverError(), $this->debug);
		if ($first->failed)
			return $this->panic();

		if ($second) {
			$response = $second->pipe_fail($response, $this->debug);
			if ($second->failed)
				return $this->panic();
		}

		return $response;
	}

	public function respond(?Request $request = null) : Response { 
		$request = $request ?? new Request;

		// request
		$request = $this->environment->pipe_request($request, $this->debug);
		if ($this->environment->failed)
			return $this->fail($this->environment);

		// route resolution
		$route = $this->router->resolve($request->action);
		if (!$route || !HTTP::match($route->method, $request->method)) {
			$response = $this->environment->pipe_response(Response::notFound(), $this->debug);
			if ($this->environment->failed)
				return $this->fail($this->environment);

			return $response;
		}

		// controller
		$request = $route->environment->pipe_request($request, $this->debug);
		if ($route->environment->failed)
			return $this->fail($route->environment, $this->environment);

		$this->debug->start();
		try {
			$arguments = $route->arguments($request->action);
			/**
			 * @psalm-suppress PossiblyNullFunctionCall
			 * The Router has already validated both the name and controller parameters exist
			 */
			$response = $route->request
			            ? ($route->controller)($request, ...array_values($arguments))
			            : ($route->controller)(...array_values($arguments));
		} catch (\Throwable $throwable) {
			$this->debug->entry("During controller call")
			            ->newline()
			            ->throwed($throwable)
				    ->collect();
			return $this->fail($route->environment, $this->environment);
		}
		$this->debug->collect();

		$response = $route->environment->pipe_response($response, $this->debug);
		if ($route->environment->failed)
			return $this->fail($route->environment, $this->environment);

		// response
		$response = $this->environment->pipe_response($response, $this->debug);
		if ($this->environment->failed)
			return $this->fail($this->environment);
		
		return $response;
	}
}

?>