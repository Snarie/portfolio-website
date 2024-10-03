<?php

use JetBrains\PhpStorm\NoReturn;

/**
 * helpers.php
 *
 * This file contains basic utility functions that assist in handling common tasks.
 */

/**
 * Sends an HTTP response with a given status code and message, then terminates the script.
 *
 * @param string $message The message to display in the response
 * @param int $code The HTTP status code to send (e.g. 404)
 * @return void
 */
#[NoReturn] function abort(string $message, int $code): void
{
	http_response_code($code);
	echo $message;
	exit();
}

/**
 * Constructs a file path realtive to the project root by joining the provided path segments.
 *
 * @param string ...$segments One or more path segments to join into a path
 * @return string The resulting path.
 */
function path(string ...$segments): string
{
	return __DIR__ . '/../' . implode('/', $segments);
}
