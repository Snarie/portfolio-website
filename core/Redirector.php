<?php

use JetBrains\PhpStorm\NoReturn;

class Redirector
{
	private string $url;
	private int $statusCode = 302; // Default to temporary

	public function __construct($url = null) {
		if ($url == null) {
			$this->url = $url;
		}
	}

	public function to($url): self {
		$this->url = $url;
		return $this;
	}

	public function permanent(): self {
		$this->statusCode = 301;
		return $this;
	}

	#[NoReturn] public function go(): void
	{
		if (!headers_sent()) {
			header('Location: ', $this->url, true, $this->statusCode);
		}
		exit();
	}
	#[NoReturn] public function goto($url): void
	{
		$this->to($url);
		$this->go();
	}

	#[NoReturn] public function with($key, $value): void
	{
		//TODO: Implement method to pass information to next route.
		$this->go();
	}
}