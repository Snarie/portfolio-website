<?php

use JetBrains\PhpStorm\NoReturn;

/**
 * helpers.php
 *
 * This file contains basic utility functions that assist in handling common tasks.
 */

/// Sends an HTTP response with a given status code and message, then terminates the script.
/// @param string $message The message to display in the response.
/// @param int $code The HTTP status code to send (e.g. 404, 500).
/// Example usage:
///     abort("Page not found", 404);
#[NoReturn] function abort($message, $code): void
{
	http_response_code($code);
	echo $message;
	exit();
}
/// Constructs a file path relative to the project root by joining the provided path segments.
/// @param string ...$segments One or more path segments to join into a path.
/// @return string The resulting path.
/// Example usage:
///     path('assets');                   // Returns path to '../assets'
///     path('assets', 'images');         // Returns path to '../assets/images'
///     path('assets', 'images', 'logo'); // Returns path to '../assets/images/logo'
function path(...$segments): string
{
	return __DIR__ . '/../' . implode('/', $segments);
}
