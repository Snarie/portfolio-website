<?php

use App\Responses\RedirectResponse;
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
 * Generates the path towards the provided view and template
 *
 * @param string $viewPath The name of the view
 * @param string $templateName The name of the template
 * @return array In order the vie path and template path.
 */
function view(string $viewPath, string $templateName = "default"): array {
	$viewPath = path('app', 'views', $viewPath . '.view.php');
	$templatePath = path('templates', $templateName . '.php');
	return [$viewPath, $templatePath];

}

/**
 * Loads the specified view with it's template.
 *
 * @param array $paths The view and template paths
 * @param array $data The data passed through (e.g., userid)
 * @return bool True if template successfully loaded.
 */
function layout(array $paths, array $data = []): bool {
	$viewPath = $paths[0];
	$filePath = $paths[1];
	if (file_exists($filePath)) {
		extract($data);

		include $filePath;
		return true;
	}
	return false;
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