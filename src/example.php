<?php

class TestController implements IController {

	public function __construct($stuff)
		$this->stuff = $stuff;

	public function process() {
		return Response::text($this->stuff);
	}
}

$n = TestController::class;

$routes = [
		"/" => Router::Controller( $n, ["/"] ),
		"/login" => Router::Controller( $n, ["/login"] ),
		"/information" => Router::Controller( $n, ["/information"] )
];

$request = new Request( ["stuff" => "stuff"] );

$app = new Service($routes);
$app->debug($request);

$response = $app->respond();

echo $response;

?>
