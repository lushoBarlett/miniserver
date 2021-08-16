<?php

namespace Mini\Data;

class File {

	public string $name;
	public string $type;
	public string $tmp_name;
	public int $error;
	public int $size;

	private array $regex = [];

	public function __construct(string $name, string $type, string $tmp, int $err, int $size) {
		$this->name = $name;
		$this->type = $type;
		$this->tmp_name = $tmp;
		$this->error = $err;
		$this->size = $size;
	}

	public function complies() : bool {
		return array_reduce($this->regex, fn (bool $last, string $curr) =>
			$last && preg_match($curr, $this->name), true);
	}

	public function add_rule(string $regex) : void {
		$this->regex[] = $regex;
	}

	public function save(string $dir = "./", string $as = "") : bool {
		if ($this->error != UPLOAD_ERR_OK)
			return false;

		if ($as === "")
			$as = $this->name;

		$dir = rtrim($dir, "/");

		return move_uploaded_file($this->tmp_name, "$dir/$as");
	}
}	

?>