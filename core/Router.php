<?php

use app\controllers\Controller;
use App\Models\Model;
use App\Requests\Request;
use App\Responses\Response;
use App\Responses\ErrorResponse;

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
	 * This method generates a URI pattern from a string by replacing placeholders with regex patterns
	 * to capture parameters. It then registers this pattern under the specified route table.
	 * Optionally a different name can be specified for easier reference.
	 *
	 * @param string $method The HTTP method (e.g., GET, POST)
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method for handling the route
	 * @param string|null $requestName Optionally fully qualified class name of the request
	 * @param string|null $name The Optional name for route
	 */
	protected function register(string $method, string $uri, string $action, ?string $requestName = null, ?string $name = null): void {
		// Convert placeholders with regex pattern
		// Finds any parts withing "{...}", allowing default characters.
		// It matches routes like /use/123/posts/456 and capture 123 as userId and 456 as postId.
		$uriPattern = preg_replace('/\{([a-zA-Z0-9_]+)}/', '(?P<\1>[a-zA-Z0-9_]+)', $uri);
		$uriPattern = "#^" . $uriPattern . "$#";

		$this->routes[$method][$uriPattern] = ['action' => $action, 'request' => $requestName];

		if (!$name) {
			$name = $this->generateRouteName($action);
		}
		$this->namedRoutes[$name] = $uri;
	}

	/**
	 * Generates a default name for a route based on the action string.
	 *
	 * This function formats the controller and method into a standardized route name by converting
	 * the controller name into a more simple format and adding the method name at the end, split by a dot.
	 *
	 * @param string $action The "controller@method" string from which a route name will be generated.
	 * @return string A route name formatted as "controller.method".
	 */
	private function generateRouteName(string $action): string {
		list ($controller, $method) = explode('@', $action);
		// Converts a name like “ExampleController” to “examples”.
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
		$this->register("GET", $uri, $this->convertToAction($action),null, $name);
	}

	/**
	 * Registers a POST route
	 *
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method
	 */
	public function post(string $uri, string $action, string $requestName, $name = null): void {
		$this->register("POST", $uri, $this->convertToAction($action), $requestName, $name);
	}

	/**
	 * Registers a PUT route
	 *
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method
	 */
	public function put(string $uri, string $action, string $requestName,  ?string $name = null): void {
		$this->register("PUT", $uri, $this->convertToAction($action), $requestName, $name);
	}

	/**
	 * Registers a DELETE route
	 *
	 * @param string $uri The URI for the route
	 * @param string $action The controller and method
	 */
	public function delete(string $uri, string $action, ?string $name = null): void {
		$this->register("DELETE", $uri, $this->convertToAction($action), null, $name);
	}

	/**
	 * Converts the short action to its full counterpart.
	 *
	 * @param string $action The action as defined in routes.php
	 * @return string Returns the action route to the action
	 */
	private function convertToAction(string $action): string {
		return "App\\Controllers\\$action";
	}

	/**
	 * Generates a URL from the named route.
	 *
	 * This function returns the URL pattern associated with the named route and
	 * replaced placeholders with the provided parameters.
	 * If no route with the given name exists, it throws an exception.
	 * <br>
	 * Example of `params`:
	 *  * ['id' => $user] would make the var $user available as $id in the view.
	 *
	 * @param string $name The name of the route of which the URL is requested.
	 * @param array $params Parameters to be used to map variables in the route.
	 * @return string Returns the URL from the provided string version.
	 * @throws Exception If no named route matches the given name.
	 */
	public function routeUrl(string $name, array $params = []): string
	{
		if (!isset($this->namedRoutes[$name])) {
			throw new Exception("No route named $name");
		}
		$url = $this->namedRoutes[$name];
		foreach ($params as $key => $value) {
			// replace variable parts of route with the correct value.
			$url = str_replace("{" . $key . "}", urlencode($value), $url);
		}
		return $url;
	}

	/**
	 * Routes an HTTP request to the appropriate controller and method.
	 *
	 * This function examines matches the provided uri with the registered routes.
	 * If a match is found, it instantiates the controller and calls the specified method.
	 * If any part of the routing fails (e.g., controller not found) an ErrorResponse is returned.
	 *
	 * @param string $method The HTTP method (e.g., GET, POST)
	 * @param string $uri The URI for the route.
	 * @return Response Returns a Response from the controller if successful, otherwise returns a ErrorResponse.
	 */
	public function route(string $method, string $uri): Response {
		if (!isset($this->routes[$method])) {
			return new ErrorResponse("HTTP method $method not supported.", 405); // Method not Allowed
		}

		foreach ($this->routes[$method] as $uriPattern => $routeConfig) {
			$action = $routeConfig['action'];
			$requestClass = $routeConfig['request'] ?? null;
			// Pass if current route matches this defined routes pattern.
			if (!preg_match($uriPattern, $uri, $matches)) {
				continue;
			}

			// Filter out non-string keys to get named parameters from the URI.
			$params = array_filter($matches, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);

			// extract controller and function from action.
			/** var class-string<Controller> $controller*/
			list($controller, $function) = explode('@', $action);

			if (!class_exists($controller)) {
				return new ErrorResponse("Controller $controller not found", 404);
			}

			$controllerInstance = new $controller();

			// Convert parameters based on expected types
			foreach ($params as $key => $value) {
				/** var class-string<Model> $model*/
				$model = "App\\Models\\" . ucfirst($key);
				if (!class_exists($model)) {
					continue;
				}

				$modelInstance = new $model();

				if (!$modelInstance instanceof Model) {
					continue;
				}

				// Attempt to find the model instance from the ID param.
				$model = $modelInstance::find($value);
				if(!$model) {
					return new ErrorResponse("No project exists at this location", 404);
				}
				$params[$key] = $model;
			}

			if ($requestClass && class_exists($requestClass)) {
				$requestInstance = new $requestClass();
				if (!$requestInstance instanceof Request) {
					return new ErrorResponse("Invalid request class for $requestClass", 500);
				}
				array_unshift($params, $requestInstance);
			}


			// Execute the method from the controller
			$response = call_user_func_array([$controllerInstance, $function], $params);

			// Ensure the returned object is a Response instance.
			if (!$response instanceof Response) {
				return new ErrorResponse("Expected a Response object from the controller", 500);
			}
			return $response;
		}

		return new ErrorResponse("Route not found for URI $uri", 404);
	}
}