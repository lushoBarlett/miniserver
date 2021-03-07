<?php

namespace Server\Modules;

use Server\Routing\Route;
use Server\State;

class BaseUrlModule extends Module {

	private $base;

	public function __construct(string $base) {
		$this->base = Route::trim($base);
	}

	public function request(State $s) : State {
		$action = Route::trim($s->request->action);

		$s->request->action = substr($action, 0, strlen($this->base)) == $this->base
				      ? substr($action, strlen($this->base))
				      : null;

		return $s;
	}

	public function response(State $s) : State {
		if ($this->base != "") {
			$redirection = $s->response->get_redirect();
			$cookies = $s->response->get_cookies();

			// NOTE: works because we are changing objects,
			// which are references
			foreach ($cookies as $cookie)
				if ($cookie->path != '')
					$cookie->path = "/{$this->base}/" . Route::trim($cookie->path);

			if ($redirection !== null) {
				$redirection = "/{$this->base}/" . Route::trim($redirection);
				$s->response->redirect($redirection);
			}
		}

		return $s;
	}
}

?>
