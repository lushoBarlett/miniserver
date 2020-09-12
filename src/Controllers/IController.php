<?php

namespace Server\Controllers;

interface IController {

	public function __construct(Environment $env);

	// Service calls this function when providing the resquest
	public function __service_init(Request $request) : Response;

	// Node constructor for routing purposes, takes arbitrary arguments
	// and returns a Node object
	public static function Node(...$args) : Node;
}
