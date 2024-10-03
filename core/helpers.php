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

/**
 * Loads the specified template.
 *
 * @param string $templateName The name of the template file
 * @param string $viewPath The name of the view (used inside the template)
 * @return bool True if template was successfully loaded.
 */
function template(string $templateName, string $viewPath): bool {
	$filePath = path("templates", $templateName);

	if (file_exists($filePath)) {
		include $filePath;
		return true;
	}
	return false;
}

/**
 * Generates the path towards the provided view name
 * @param string $viewName The name of the view file
 * @return string The path to the specified view
 */
function view(string $viewName) {
	return __DIR__ . '/../views/' . $viewName;
}
