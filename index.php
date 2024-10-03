<?php

require __DIR__ . "\core\helpers.php";
require __DIR__ . "\core\Router.php";

spl_autoload_register(function($class) {

	$class = str_replace("\\", DIRECTORY_SEPARATOR, $class);

	require __DIR__ . "\\$class.php";
});

$router = Router::getRouter();
require path('routes.php');

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->route($method, $uri);