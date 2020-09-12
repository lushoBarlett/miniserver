<?php

namespace Server;

class State {

	public $request;
	public $response;
	public $resolution;
	public $error_list;

	public function __construct(
		?Request $rqst = null,
		?Response $rsp = null,
		?Resolution $rsl = null,
		array $errl = []
	) {
		$this->request = $rqst;
		$this->response = $rsp;
		$this->resolution = $rsl;
		$this->error_list = $errl;
	}

}

?>