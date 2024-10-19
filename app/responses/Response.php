<?php

namespace App\Responses;

abstract class Response
{
	/**
	 * @var array Array of HTTP headers to send with the response.
	 */
	protected array $headers = [];
	/**
	 * @var int HTTP status code for the response.
	 */
	protected int $statusCode = 200;

	/**
	 * Constructs a new Response Object.
	 *
	 * @param int $statusCode The HTTP status code for the response.
	 * @param array $headers Additional HTTP headers to send with the response.
	 */
	public function __construct(int $statusCode = 200, array $headers = []) {
		$this->statusCode = $statusCode;
		$this->setHeaders($headers);
	}

	/**
	 * Set multiple headers at once from an associative array.
	 * Existing headers will be merged with the new headers.
	 *
	 * @param array $headers An associative array of headers.
	 * @return void
	 */
	public function setHeaders(array $headers): void
	{
		$this->headers = array_merge($this->headers, $headers);
	}

	/**
	 * Sets The HTTP status code for the response.
	 *
	 * @param int $statusCode The HTTP status code for the response.
	 * @return void
	 */
	public function setStatusCode(int $statusCode): void
	{
		$this->statusCode = $statusCode;
	}

	/**
	 * Adds a header to the response.
	 *
	 * @param string $key The header name.
	 * @param string $value The header value.
	 * @return void
	 */
	public function addHeader(string $key, string $value): void
	{
		$this->headers[$key] = $value;
	}

	/**
	 * Send all headers to the response.
	 *
	 * @return void
	 */
	public function sendHeaders(): void
	{
		// Ensure headers are only sent if they have not already been send
		// and sets the appropriate HTTp status code.
		if (!headers_sent()) {
			http_response_code($this->statusCode);
			foreach ($this->headers as $key => $value) {
				header("$key: $value");
			}
		}
	}

	abstract public function send(): void;
}