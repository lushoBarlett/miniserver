<?php

namespace Mini;

use PHPUnit\Framework\TestCase;

use Mini\Tools\Debug;

class EnvironmentTest extends TestCase {

	public function testPipelines() {
		$root = function(Request $request) {
			$request->action = "/";
			return $request;
		};

		$ok = function(Response $response) {
			return Response::OK();
		};

		$env = (new Environment)
			->request($root)
			->response($ok)
			->fail($ok);
		
		$this->assertEquals("/", $env->pipe_request(new Request([]), new Debug)->action);
		$this->assertEquals(200, $env->pipe_response(Response::notFound(), new Debug)->status);
		$this->assertEquals(200, $env->pipe_fail(Response::serverError(), new Debug)->status);
	}

	public function testErrors() {
		$test = fn($debug) => function($x) use($debug) {
			echo "@$debug";
			throw new \Exception($debug);
		};

		$debug = new Debug;
		$env = (new Environment)
			->request($test("request"))
			->response($test("response"))
			->fail($test("fail"));
		
		$env->pipe_request(new Request([]), $debug);
		$env->pipe_response(new Response, $debug);
		$env->pipe_fail(new Response, $debug);

		$this->assertMatchesRegularExpression("/Exception: request/", (string)$debug);
		$this->assertMatchesRegularExpression("/@request/", (string)$debug);
		$this->assertMatchesRegularExpression("/Exception: response/", (string)$debug);
		$this->assertMatchesRegularExpression("/@response/", (string)$debug);
		$this->assertMatchesRegularExpression("/Exception: fail/", (string)$debug);
		$this->assertMatchesRegularExpression("/@fail/", (string)$debug);
	}
}

?>