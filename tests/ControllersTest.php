<?php

namespace Server;

use PHPUnit\Framework\TestCase;

use Server\Request;
use Server\Response;

use Server\Controllers\ConstController;
use Server\Controllers\SimpleController;
use Server\Controllers\Controller;

use Server\Routing\Router;

class ChooseMethod {

	private $env;

	public function __construct(Environment $env) {
		$this->env = $env;
	}

	public function get(Request $r) {
		$r = Response::withText("get");
		if ($this->env->constant("status"))
			$r->status($this->env->constant("status"));
		return $r;
	}

	public function post(Request $r) {
		$r = Response::withText("post");
		if ($this->env->constant("status"))
			$r->status($this->env->constant("status"));
		return $r;
	}
}

class ControllersTest extends TestCase {

	private function gen_response(array $routes, Request $request) {
		$s = new Service(new Router($routes));
		return $s->respond($request);
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

	public function testControllerMethodSelection() {
		$routes = ["path" => Controller::Node(ChooseMethod::class)];
		
		$req = $this->ping("path");
		$req->method = Request::GET;

		$res = $this->gen_response($routes, $req);
		$this->assertEquals("get", $res->get_payload());

		$req->method = Request::POST;

		$res = $this->gen_response($routes, $req);
		$this->assertEquals("post", $res->get_payload());

		$req->method = Request::PUT;

		$res = $this->gen_response($routes, $req);
		$this->assertEquals(404, $res->get_status());
	}

	public function testControllerEnvironment() {
		$routes = ["path" => Controller::Node(ChooseMethod::class, ["status" => 201])];
		
		$req = $this->ping("path");
		$req->method = Request::GET;

		$res = $this->gen_response($routes, $req);
		$this->assertEquals(201, $res->get_status());

		$req->method = Request::POST;
		
		$res = $this->gen_response($routes, $req);
		$this->assertEquals(201, $res->get_status());
	}
}

?>