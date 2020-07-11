<?php

namespace Server\Controllers;

interface IController {

	// constructor takes a service reference
	// and an associative array of directives
	// and other unspecified data you might wanna use.
	// The data complies with no rules whatsoever,
	// but the style used in the provided Controllers followes rules
	// for consistency purposes.
	// This array corresponds to the `meta` Node field further down.
	public function __construct(Service $service, array $metadata = []);

	// Service calls this function when providing the resquest
	// and expects a response, that it then returns
	public function __service_init(Request $request) : Response;

	// Node constructor for routing purposes, takes arbitrary arguments
	// but must return an object as shown below. The arguments
	// The functionality of a Controller lies in what metadata
	// it takes and how it behaves with it.
	// 
	// (object)[
	//     "cons" => SomeClass::class,
	//     "meta" => $metadata_array
	// ]
	public static function Node(...$args) : object;
}
