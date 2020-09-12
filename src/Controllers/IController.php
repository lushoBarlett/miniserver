<?php

namespace Server\Controllers;

interface IController {

	public function __construct(\Server\Environment $env);

	// Service calls this function when providing the resquest
	public function __service_init(\Server\Request $request) : \Server\Response;

	// Node constructor for routing purposes, takes arbitrary arguments
	// and returns a Node object
	public static function Node(...$args) : Node;
}

?>