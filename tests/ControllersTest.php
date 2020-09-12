<?php

namespace Server;

use PHPUnit\Framework\TestCase;

use Server\Directives\Directive;
use Server\Controllers\ConstController;
use Server\Controllers\SimpleController;
use Server\Controllers\Controller;
use Server\Routing\Router;

class TestDirective extends Directive {
	public function exception_event(State $s) : State {
		echo $s->error_list[count($s->error_list) - 1];
		return $s;
	}
	public function error_event(State $s) : State {
		echo $s->error_list[count($s->error_list) - 1];
		return $s;
	}
}

class ControllersTest extends TestCase {

	private function gen_response(array $routes, Request $request) {
		return (new Service(
			new Router($routes), new Environment(["@test" => new TestDirective])
		))->respond($request);
	}

	private function ping(string $route) {
		return new Request(["action" => $route]);
	}

	public function testSimpleController() {
		$r = $this->gen_response(
			["path" => SimpleController::Node(function($r) {
				return Response::withText($r->action . $r->get['v'])
					->status(200);
			})],
			$this->ping("path?v=1")
		);
		$this->assertEquals("path1", $r->get_payload());
		$this->assertEquals(200, $r->get_status());
	}

	public function testConstController() {
		$routes = ["path" => ConstController::Node(
			Response::withText("const")->status(200)
		)];

		$r1 = $this->gen_response($routes, $this->ping("path?v=1"));
		$r2 = $this->gen_response($routes, $this->ping("path?v=2"));

		$this->assertSame("const", $r1->get_payload());
		$this->assertSame(200, $r1->get_status());
		$this->assertSame($r2->get_payload(), $r1->get_payload());
	}

}

?>