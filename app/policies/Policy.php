<?php

namespace App\Policies;

abstract class Policy
{
	/**
	 * Determine whether the user can access any route
	 * @return bool
	 */
	abstract function any(): bool;

	/**
	 * Determine whether the user can access routes without their own rules.
	 * @return bool
	 */
	public function default(): bool
	{
		return true;
	}
}