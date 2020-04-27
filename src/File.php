<?php

namespace Server;

class File {
	public $name;
	public $type;
	public $tmp_name;
	public $error;
	public $size;

	private $regex = [];

	public function __construct(string $name, string $type, string $tmp, int $err, int $size) {
		$this->name = $name;
		$this->type = $type;
		$this->tmp_name = $tmp;
		$this->error = $err;
		$this->size = $size;
	}

	public function complies() : bool {
		return array_reduce(
			$this->regex,
			function ($last, $curr) {
				return $last and preg_match($curr, $this->name);
			},
			true
		);
	}

	public function add_rule(string $regex) : void {
		$this->regex[] = $regex;
	}

	public function save(string $dir, string $as = "") : bool {
		if ($error != UPLOAD_ERR_OK)
			return false;

		if ($as === "")
			$as = basename($this->name);

		return move_uploaded_file($this->tmp_name, "$dir/$as");
	}
}	

?>
