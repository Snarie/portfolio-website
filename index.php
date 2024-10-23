<?php


require __DIR__ . "\core\helpers.php";
require __DIR__ . "\core\Router.php";


// Include the file containing the database connection.
//$conn = require __DIR__ . "\db.php";




/**
 * Autoload function to dynamically load required classes.
 * This function automatically loads classes used in the script without manually including them.
 */
spl_autoload_register(function($class) {
	$class = str_replace("\\", DIRECTORY_SEPARATOR, $class);
	$file = __DIR__ . DIRECTORY_SEPARATOR . $class . '.php';

	if (file_exists($file)) {
		require_once $file;
	} else {
		error_log("File not found: " . $file);
		throw new Exception("File for class $class not found at $file");
	}
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

if ($method === 'POST' && isset($_POST['_method'])) {
	$method = strtoupper($_POST['_method']);
}

try {
	// Invoke the Router's route() method, which matches the request's method and URI
	// to a registered route and calls the controller's function.
	$response = $router->route($method, $uri);
	$response->send();
} catch (Exception $e) {
	http_response_code(500);
	echo "500 Internal Server Error - " . $e->getMessage();
}