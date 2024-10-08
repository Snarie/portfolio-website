<?php

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
	 * @param array $routes An optional array of routes
	 */
	private function __construct(private array $routes = []) {

	}

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
	 * @param string $method The HTTP method (e.g., GET, POST)
	 */
	protected function register(string $uri, string $action, string $method): void {

		$uriPattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([0-9]+)', $uri);
		if(!isset($this->routes[$method])) $this->routes[$method] = [];

		list($controller, $function) = $this->extractAction($action);

		$this->routes[$method][$uriPattern] = [
			'controller' => $controller,
			'method' => $function,
			'params' => [] // to store dynamic if needed
		];
		/*$this->routes[$method][$uri] = [
			'controller' => $controller,
			'method' => $function
		];*/
	}


	/**
	 * Extracts the controller and method from the action
	 *
	 * @param string $action The action string (e.g., "HomeController@index")
	 * @param string $separator The separator between controller and method (default is '@')
	 * @return array An array with in order the controller name, and the method name.
	 */
	protected function extractAction(string $action, string $separator = '@'): array {
		$sepPos = strpos($action, $separator);

		$controller = substr($action, 0, $sepPos);

		$function = substr($action, $sepPos + 1, strlen($action));

		return [$controller, $function];
	}

	/**
	 * @param array $arr
	 * @param string $method The HTTP method (e.g., GET, POST)
	 * @param string $uri The URI that must be found
	 * @return array|null
	 */
	public function getRoute(array $arr, string $method, string $uri): ?array {
		if (!isset($arr[$method])) {
			return null; // HTTP method not found
		}

		foreach ($arr[$method] as $routePattern => $route) {
			if (preg_match("#^$routePattern$#", $uri, $matches)) {
				array_shift($matches); // Remove full match
				$route['params'] = $matches; // Store match
				return $route;
			}
		}
		return null;
	}

	/**
	 * Dispatch the request to the appropriate controller and function
	 *
	 * @param string $method The HTTP method (e.g., GET, POST)
	 * @param string $uri The requested URI
	 * @return bool Returns true if route found, false otherwise
	 */
	public function route(string $method, string $uri): bool {
		$result = $this->getRoute($this->routes, $method, $uri);
		if(!$result) abort("Route not found", 404);

		$controller = $result['controller'];
		$function = $result['method'];
		$params = $result['params'] ?? [];

		if(class_exists($controller)) {

			$controllerInstance = new $controller();

			if(method_exists($controllerInstance, $function)) {

				call_user_func_array([$controllerInstance, $function], $params);
				return true;

			} else {

				abort("No method {$function} on class {$controller}", 500);
			}
		}

		return false;
	}
}