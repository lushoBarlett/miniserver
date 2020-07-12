<?php

namespace Server\Controllers;

interface IController {

	public function __construct(Environment $env);

	// Service calls this function when providing the resquest
	public function __service_init(Request $request) : Response;

	// Node constructor for routing purposes, takes arbitrary arguments
	// but must return an object as shown below. The first arguument
	// is the Controller class name that you wanna instantiate.
	// The second argument is an optional Environment that extends
	// the Service environments (and can override it)
	// 
	// (object)[
	//     "cons" => SomeClass::class,
	//     "env" => $environment_or_null
	// ]
	public static function Node(...$args) : object;
}
