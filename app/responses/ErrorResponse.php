<?php

namespace App\Responses;

	class ErrorResponse extends Response
	{
		/**
		 * @var string The error message that will be sent in the response body.
		 */
		private string $message;

		/**
		 * Constructs a new ErrorResponse object.
		 * Initializes the response with a specific status code, headers and error message.
		 *
		 * @param string $message The error message;
		 * @param int $statusCode The HTTP status code for the error.
		 * @param array $headers Additional HTTP headers to send with the response.
		 */
		public function __construct(string $message, int $statusCode = 200, array $headers = [])
		{
			parent::__construct($statusCode, $headers);
			$this->message = $message;
			$this->addHeader('Content-Type', 'text/plain');
		}

		/**
		 * Sends the error response to the user.
		 * @return void
		 */
		public function send(): void
		{
			parent::sendHeaders();
			echo $this->message;
		}
	}