<?php

require __DIR__ . "\core\helpers.php";
require __DIR__ . "\core\Router.php";

/**
 * Autoload function to dynamically load required classes.
 * This function automatically loads classes used in the script without manually including them.
 */
spl_autoload_register(function($class) {

	$class = str_replace("\\", DIRECTORY_SEPARATOR, $class);

	require __DIR__ . "\\$class.php";
});

// Create Router instance.
$router = Router::getRouter();
// Include the file containing the registered routes.
require path('routes.php');

// Get the current method and URI
// The request method (e.g. GET, POST)
// The request URI (e.g. /about)
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Invoke the Router's route() method, which matches the request's method and URI
// to a registered route and calls the controller's function.
$router->route($method, $uri);