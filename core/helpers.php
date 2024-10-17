<?php

use App\Responses\RedirectResponse;
use App\Responses\HtmlResponse;
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



function conn(): PDO {
	static $pdo = null;

	if ($pdo === null) {
		$pdo = require __DIR__ . '/../db.php';
	}

	return $pdo;
}

function redirect($routeName, $params = []): RedirectResponse
{
	$router = Router::getRouter();
	$url = $router->routeUrl($routeName, $params);
	error_log($url);
	return new RedirectResponse($url);
}

function view(string $viewString, array $data = []) {
	$parts = explode('/', $viewString);
	$view = array_pop($parts); // Gets the last part of the array

	if (!empty($parts)) {
		$template = array_pop($parts);
	} else {
		$template = 'default';
	}

	$view = str_replace('.', '/', $view);

	$viewPath = path('app', 'views', $view . '.view.php');
	$templatePath = path('templates', $template . '.php');

	if (file_exists($templatePath) && file_exists($viewPath)) {
		extract ($data);
		ob_start(); // Start output buffering
		include $templatePath;
		$content = ob_get_clean();  // Capture clean buffer
		return new HtmlResponse($content);
	}
	return new HtmlResponse('Page not found', 404);
}

