<?php

namespace App\Responses;

class HtmlResponse extends Response
{
	/**
	 * @var string The HTML content, that will be sent as the response body.
	 */
	private string $content;

	/**
	 * Constructs a new HtmlResponse object.
	 * Initializes the response with a specific status code, headers and HTML content.
	 *
	 * @param string $content The HTML content to send to the user.
	 * @param int $statusCode The HTTP status code for the response.
	 * @param array $headers Additional HTTP headers to send with the response.
	 */
	public function __construct(string $content, int $statusCode = 200, array $headers = [])
	{
		// Merge the default header for HTML content type with already existing headers.
		parent::__construct($statusCode, array_merge(['Content-Type' => 'text/html'], $headers));
		$this->content = $content;
	}

	/**
	 * Sends the HTML response to the user.
	 * @return void
	 */
	public function send(): void
	{
		$this->sendHeaders();
		echo $this->content;
	}

}