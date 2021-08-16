<?php

namespace Mini;

use Mini\Tools\Debug;

class Environment {

	/** @var callable(Request):Request */
	private $request;
	/** @var callable(Response):Response */
	private $response;
	/** @var callable(Response):Response */
	private $fail;

	public bool $failed = false;

	public function __construct() {
		$this->request  = fn(Request $request):Request => $request;
		$this->response = fn(Response $response):Response => $response;
		$this->fail     = fn(Response $response):Response => $response;
	}

	/**
	 * @param callable(Request):Request $request
	 */
	public function request(callable $request) : self {
		$this->request = $request;
		return $this;
	}

	/**
	 * @param callable(Response):Response $response
	 */
	public function response(callable $response) : self {
		$this->response = $response;
		return $this;
	}

	/**
	 * @param callable(Response):Response $fail
	 */
	public function fail(callable $fail) : self {
		$this->fail = $fail;
		return $this;
	}

	public function pipe_request(Request $request, Debug $debug) : Request {
		$this->failed = false;
		$debug->start();
		try {
			$request = ($this->request)($request);
		} catch (\Throwable $throwable) {
			$this->failed = true;
			$debug->entry("During request pipeline")
			      ->newline()
			      ->throwed($throwable)
			      ->newline();
		}
		$debug->collect();
		return $request;
	}

	public function pipe_response(Response $response, Debug $debug) : Response {
		$this->failed = false;
		$debug->start();
		try {
			$response = ($this->response)($response);
		} catch (\Throwable $throwable) {
			$this->failed = true;
			$debug->entry("During response pipeline")
			      ->newline()
			      ->throwed($throwable)
			      ->newline();
		}
		$debug->collect();
		return $response;
	}

	public function pipe_fail(Response $response, Debug $debug) : Response {
		$this->failed = false;
		$debug->start();
		try {
			$response = ($this->fail)($response);
		} catch (\Throwable $throwable) {
			$this->failed = true;
			$debug->entry("During fail pipeline")
			      ->newline()
			      ->throwed($throwable)
			      ->newline();
		}
		$debug->collect();
		return $response;
	}
}

?>