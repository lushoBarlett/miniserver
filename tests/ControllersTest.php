<?php

namespace Server;

use PHPUnit\Framework\TestCase;

use Server\Directives\Directive;
use Server\Controllers\ConstController;
use Server\Controllers\SimpleController;
use Server\Controllers\Controller;
use Server\Routing\Router;

class ControllersTest extends TestCase {

	private function gen_response(array $routes, Request $request) {
		return (new Service(new Router($routes)))->respond($request);
	}

	private function ping(string $route, array $get = []) {
		return new Request(["action" => $route, "get" => $get]);
	}

	public function testSimpleController() {
		$r = $this->gen_response(
			["path" => SimpleController::Node(
				function($r) {
					return Response::withText($r->action . $r->get['v']);
				}
			)],
			$this->ping("path", ["v" => "1"])
		);
		$this->assertEquals("path1", $r->get_payload());
	}

	public function testConstController() {
		$routes = ["path" => ConstController::Node(Response::withText("const"))];

		$r1 = $this->gen_response($routes, $this->ping("path", ["v" => "1"]));
		$r2 = $this->gen_response($routes, $this->ping("path", ["v" => "2"]));

		$this->assertEquals("const", $r1->get_payload());
		$this->assertEquals($r2->get_payload(), $r1->get_payload());
	}
}

?>