<?php

namespace Server;

use Cologger\Logger;

class Service {

	private $router;
	private $request;

	public function __construct(array $routes = [], ?Request $debug = null) {
		$this->router = new Router($routes);
		$this->request = $debug ? $debug : new Request;
	}

	public function respond() : Response {
		$c = $this->router->resolve(
			$this->request->action
		);

		if ($c === null)
			return Response::notFound();

		ob_start();
		$r;
		try {
			$class = $c->name;
			$con = new $class(...$c->args);
			
			$r = $con->process( $this->request );
			
			assert(
				__NAMESPACE__ . "\\Response",  get_class($r),
				"Controller responded with a non Response type value"
			);
		}
		catch (Exception $e) {
			Logger::error( (string)$e );
			$r = Response::serverError();
		}
		echo ob_get_clean();
		return $r;
	}

	public static function Controller(string $class, array $params = []){
		return (object)[
			"name" => $class,
			"args" => $params
		];
	}
}

?>
