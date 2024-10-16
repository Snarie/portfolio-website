<?php

use models\Model;

/**
 * This class handles routing in the application.
 * It uses singleton pattern to ensure only 1 instance exist at one moment.
 * The router registers different HTTP methods (GET POST PUT DELETE) and connects them with their controller methods.
 */
class Router
{
	/**
	 * @var Router|null Stores the Singleton instance
	 */
	private static ?Router $router;

	/**
	 * @var array Stores the routes divided by HTTP methods
	 */
	private array $routes = [];

	/**
	 * @var array Stores named routes for easy URL generation.
	 */
	private array $namedRoutes = [];

	/**
	 * Private constructor to prevent direct instantiation.
	 */
	private function __construct() { }

	/**
	 * Retrieve single instance of Router
	 *
	 * @return self The single instance of the Router class
	 */
	public static function getRouter(): self {
		// If no instance of teh router exists, create a new instance.
		if(!isset(self::$router)) {
			self::$router = new self();
		}
		return self::$router;
	}

	/**
	 * Registers a route for a given HTTP method
	 *
	 * @param string $method The HTTP method (e.g., GET, POST)
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method for handling the route
	 * @param string|null $name Optional name for route
	 */
	protected function register(string $method, string $uri, string $action, ?string $name = null): void {

		$uriPattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_]+)', $uri);
		$uriPattern = "#^" . $uriPattern . "$#";

		$this->routes[$method][$uriPattern] = $action;

		if ($name) {
			$this->namedRoutes[$name] = $uri;
		} else {
			$name = $this->generateRouteName($action);
			$this->namedRoutes[$name] = $uri;
		}
	}
	private function generateRouteName(string $action): string {
		list ($controller, $method) = explode('@', $action);
		$controllerName = strtolower(str_replace('Controller', 's', basename($controller)));
		return $controllerName . '.' . $method;
	}

	/**
	 * Registers a GET route
	 *
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method
	 */
	public function get(string $uri, string $action, ?string $name = null): void {
		$this->register("GET", $uri, $this->convertToAction($action), $name);
	}

	/**
	 * Registers a POST route
	 *
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method
	 */
	public function post(string $uri, string $action, ?string $name = null): void {
		$this->register("POST", $uri, $this->convertToAction($action), $name);
	}

	/**
	 * Registers a PUT route
	 *
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method
	 */
	public function put(string $uri, string $action, ?string $name = null): void {
		$this->register("PUT", $uri, $this->convertToAction($action), $name);
	}

	/**
	 * Registers a DELETE route
	 *
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method
	 */
	public function delete(string $uri, string $action, ?string $name = null): void {
		$this->register("DELETE", $uri, $this->convertToAction($action), $name);
	}

	/**
	 * Converts the short action to it's full counterpart.
	 *
	 * @param string $action The action as defined in routes.php
	 * @return string Returns the action route to the action
	 */
	private function convertToAction(string $action): string {
		return "controllers\\$action";
	}

	public function routeUrl(string $name, array $params = []): string {
		if (!isset($this->namedRoutes[$name])) {
			throw new Exception("No route named $name");
			exit();
		}
		$url = $this->namedRoutes[$name];
		foreach ($params as $key => $value) {
			$url = str_replace("{" . $key . "}", urlencode($value), $url);
		}
		return $url;
	}

	public function route(string $method, string $uri): bool {
		if (!isset($this->routes[$method])) {
			throw new Exception("HTTP method $method not supported.");
		}

		foreach ($this->routes[$method] as $uriPattern => $action) {
			if (!preg_match($uriPattern, $uri, $matches)) {
				continue;
			}
			$params = array_filter($matches, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);

			list($controller, $function) = explode('@', $action);

			if (!class_exists($controller)) {
				throw new \Exception("Controller $controller not found");
			}

			$controllerInstance = new $controller();
			if (!method_exists($controllerInstance, $function)) {
				throw new \Exception("Method $function not found in controller $controller");
			}

			// Convert parameters based on expected types
			foreach ($params as $key => $value) {
				$model = "models\\" . ucfirst($key);

				if (!class_exists($model)) {
					continue;
				}

				$modelInstance = new $model();

				if (!$modelInstance instanceof Model) {
					throw new \Exception("Class {$model} does not extend Model");
				}

				$model = $modelInstance::find($value);
				$params[$key] = $model;
			}
			//print_r($params);
			call_user_func_array([$controllerInstance, $function], $params);
			return true;
		}
		throw new \Exception("Route not found");
	}
}