<?php

namespace App\Responses;

use JetBrains\PhpStorm\NoReturn;

class RedirectResponse extends Response
{
	/**
	 * @var string The URL for the redirect.
	 */
	private string $url;

	/**
	 * Constructs a new RedirectResponse object.
	 * Initializes the response with a specific status code, headers and URL.
	 *
	 * @param string $url The URL to redirect to.
	 * @param int $statusCode The HTTP status code for the redirect.
	 * @param array $headers Additional HTTP headers to send with the response.
	 */
	public function __construct(string $url, int $statusCode = 302, array $headers = []) {
		parent::__construct($statusCode, $headers);
		$this->url = $url;
		$this->addHeader('Location', $url);
	}

	/**
	 * Adds a session-based flash message that can be accessed after the redirect.
	 *
	 * @param string $key The session key under which the flash data will be stored.
	 * @param mixed $value The data to store in the session.
	 * @return self Returns the instance itself for method chaining.
	 */
	public function with(string $key, mixed $value): self
	{
		if (session_status() === PHP_SESSION_NONE) {
			// Start session if it hasn't started yet.
			session_start();
		}
		// Store the flash message in the session.
		$_SESSION['flash'][$key] = $value;
		return $this;
	}

	/**
	 * @return string The URL for the redirect.
	 */
	public function url(): string
	{
		return $this->url;
	}

	/**
	 * Sends the HTTP redirects headers to the user.
	 * Stops script after execution by calling exit().
	 * @return void
	 */
	#[NoReturn] public function send(): void
	{
		$this->sendHeaders();
		exit();
	}
}