<?php

/**
 * This class is responsible for handling routes in the application
 * It uses singeleton pattern to ensure only 1 instance exist at one moment
 * The router registers different HTTP methods (GET POST PUT DELETE) and connects them with their controller methods
 */
class Router
{
	/**
	 * @var Router|null Stores the Singleton instance
	 */
	private static $router;

	/**
	 * @param array $routes Optional array of routes
	 */
	private function __construct(private array $routes = []) {

	}

	/**
	 * Retrieve single instance of Router
	 *
	 * @return self The single instance of the Router class
	 */
	public static function getRouter(): self {
		// If the router is not yet instantiated, create a new instance
		if(!isset(self::$router)) {
			self::$router = new self();
		}
		return self::$router;
	}

	/**
	 * Registers a GET route
	 *
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method
	 */
	public function get(string $uri, string $action): void {
		$this->register($uri, $action, "GET");
	}

	/**
	 * Registers a POST route
	 *
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method
	 */
	public function post(string $uri, string $action): void {
		$this->register($uri, $action, "POST");
	}

	/**
	 * Registers a PUT route
	 *
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method
	 */
	public function put(string $uri, string $action): void {
		$this->register($uri, $action, "PUT");
	}

	/**
	 * Registers a DELETE route
	 *
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method
	 */
	public function delete(string $uri, string $action): void{
		$this->register($uri, $action, "DELETE");
	}

	/**
	 * Registers a route for a given HTTP method
	 *
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method for handling the route
	 * @param string $method The HTTP method (GET, POST, PUT, DELETE)
	 */
	protected function register(string $uri, string $action, string $method): void {
		if(!isset($this->routes[$method])) $this->routes[$method] = [];

		list($controller, $function) = $this->extractAction($action);

		$this->routes[$method][$uri] = [
			'controller' => $controller,
			'method' => $function
		];
	}


	/**
	 * Extracts the controller and method from the action
	 *
	 * @param string $action The action string (e.g. "HomeController@index")
	 * @param string $separator The separator between controller and method (default is '@')
	 * @return array An array with in order the controller name, and the method name.
	 */
	protected function extractAction(string $action, string $separator = '@'): array {
		// Find the position of the separator in the action string.
		$sepPos = strpos($action, $separator);

		$controller = substr($action, 0, $sepPos);

		$function = substr($action, $sepPos + 1, strlen($action));

		return [$controller, $function];
	}

	/**
	 * @param array $arr
	 * @param string $method The method
	 * @param string $uri The URI that must be found
	 * @return array|null
	 */
	public function getRoute(array $arr, string $method, string $uri): ?array {
		if (!isset($arr[$method])) {
			return null; // method not foundf
		}
		if (!isset($arr[$method][$uri])) {
			return null; // URI not found
		}

		return $arr[$method][$uri];
	}
	public function route(string $method, string $uri): bool {
		$result = $this->getRoute($this->routes, $method, $uri);
		if(!$result) abort("Route not found", 404);

		$controller = $result['controller'];
		$function = $result['method'];

		if(class_exists($controller)) {

			$controllerInstance = new $controller();

			if(method_exists($controllerInstance, $function)) {

				$controllerInstance->$function();
				return true;

			} else {

				abort("No method {$function} on class {$controller}", 500);
			}
		}

		return false;
	}
}