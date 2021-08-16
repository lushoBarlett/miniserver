<?php

namespace Mini\Tools;

class Debug {

	public array $throwables = [];
	public string $body = "";
	private bool $stream_open = false;

	public function entry(string $name) : self {
		if (!empty($this->body))
			$this->newline(2);
		$this->print("=== $name ===")->newline();
		return $this;
	}

	public function newline(int $count = 1) : self {
		$this->print(\str_repeat("\n", $count));
		return $this;
	}

	public function print(string $body) : self {
		$this->body .= "$body";
		return $this;
	}

	public function throwed(\Throwable $throwable) : self {
		$this->throwables[] = $throwable;
		$this->print((string)$throwable);
		return $this;
	}

	public function start() : self {
		$this->stream_open = true;
		\ob_start();
		return $this;
	}

	public function collect() : self {
		$this->stream_open = false;
		$output = \ob_get_clean();
		if (!empty($output))
			$this->print("Debug output:")->newline()->print($output);
		return $this;
	}

	public function output(string $file) : self {
		\file_put_contents($file, $this->body);
		return $this;
	}

	public function append(string $file) : self {
		\file_put_contents($file, $this->body, FILE_APPEND);
		return $this;
	}

	public function __toString() : string {
		return $this->body;
	}

	public function __destruct() {
		if ($this->stream_open)
			\ob_end_clean();
	}
}

?>