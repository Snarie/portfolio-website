<?php

namespace App\Controllers;

use App\Policies\Policy;
use App\Responses\ErrorResponse;
use App\Responses\Response;

class Controller
{
	public function __call($method, $arguments): Response
	{
		$policyClass = $this->getPolicyClass();

		// No policy found, allows method execution by default
		if (!$policyClass) {
			return $this->callControllerMethod($method, $arguments);
		}

		/** @var Policy $policyInstance */
		$policyInstance = new $policyClass();

		// Check if user is allowed to access the controller.
		if (method_exists($policyInstance, 'any') && !$policyInstance->any()) {
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

	protected function getPolicyClass(): ?string
	{
		$modelName = str_replace('Controller', '', (new \ReflectionClass($this))->getShortName());
		$policyClass = "App\\Policies\\{$modelName}Policy";
		return class_exists($policyClass) ? $policyClass : null;
	}

	protected function callControllerMethod(string $method, array $arguments): Response
	{
		if (method_exists($this, $method)) {
			return call_user_func_array([$this, $method], $arguments);
		}
		return new ErrorResponse("Method not found", 404);
	}
}