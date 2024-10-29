<?php

namespace App\Controllers;

use App\Policies\Policy;
use App\Responses\ErrorResponse;
use App\Responses\Response;

class Controller
{
	/**
	 * Dynamically handles protected method calls on the controller.
	 *
	 * This function checks for a policy associated with the controller, first verifying whether the `any()`
	 * permission allows the user to access the controller. Then it checks for a specific policy method
	 * matching the method being called. If the policy denies access, an unauthorized `ErrorResponse` is returned.
	 *
	 * @param string $method The name of the method being called.
	 * @param array $arguments The arguments provided to the method.
	 * @return Response The result of the method call or an ErrorResponse if unauthorized or not found.
	 */
	public function __call(string $method, array $arguments): Response
	{
		$policyClass = $this->getPolicyClass();

		// No policy found, allows method execution by default
		if (!$policyClass) {
			return $this->callControllerMethod($method, $arguments);
		}

		/** @var Policy $policyInstance */
		$policyInstance = new $policyClass();

		// Check if user is allowed to access the controller.
		if (!$policyInstance->any()) {
			return new ErrorResponse("Unauthorized", 403);
		}

		// Policy method not found, allows method execution by default
		if (method_exists($policyInstance, $method)) {
			// Check if user is allowed to access specific action.
			if (!$policyInstance->$method()) {
				return new ErrorResponse("Unauthorized", 403);
			}
		}
		else {
			// Check if user is allowed to access actions without rules.
			if (!$policyInstance->default()){
				return new ErrorResponse("Unauthorized", 403);
			}
		}

		// Passed all Policy tests
		return $this->callControllerMethod($method, $arguments);
	}

	/**
	 * Retrieves the policy class name based on the controller name.
	 *
	 * This function identifies the matching policy class to the current controller. It assumes a strict naming
	 * convention (e.g., `ProjectPolicy` for `ProjectController`). If the Policy exists, the class name is returned.
	 *
	 * @return string|null The name of the policy class if it exists, otherwise null.
	 */
	protected function getPolicyClass(): ?string
	{
		$modelName = str_replace('Controller', '', (new \ReflectionClass($this))->getShortName());
		$policyClass = "App\\Policies\\{$modelName}Policy";
		return class_exists($policyClass) ? $policyClass : null;
	}

	/**
	 * Calls a method on the controller if it exists, or returns a 404 ErrorResponse.
	 *
	 * This function checks for the existence of the specified method on the controller. If it exists, the function
	 * is called with its arguments, otherwise a ErrorResponse will be returned.
	 *
	 * @param string $method The name of the method being called.
	 * @param array $arguments The arguments provided to the method.
	 * @return Response The result of the method call or an ErrorResponse if unauthorized or not found.
	 */
	protected function callControllerMethod(string $method, array $arguments): Response
	{
		if (method_exists($this, $method)) {
			return call_user_func_array([$this, $method], $arguments);
		}
		return new ErrorResponse("Method not found", 404);
	}
}