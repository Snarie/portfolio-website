<?php

namespace App\Policies;

class ToolPolicy extends Policy
{
	function any(): bool
	{
		if (!$user = auth()) {
			return false;
		}
		return $user->admin;
	}

}