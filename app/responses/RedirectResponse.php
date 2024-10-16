<?php
namespace App\Responses;

class RedirectResponse extends Response
{
	protected $url;
	protected $statusCode;
	protected $headers = [];

	public function __construct($url, $statusCode = 302, $headers = []) {
		$this->url = $url;
		$this->statusCode = $statusCode;
		$this->headers = $headers;
	}

	public function with($key, $value) {
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		$_SESSION['flash'][$key] = $value;
		return $this;
	}
	public function send() {
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		foreach ($this->headers as $header) {
			header($header);
		}
		header("Location: {$this->url}", true, $this->statusCode);
		exit();
	}
}