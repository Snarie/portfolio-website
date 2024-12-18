<?php

namespace App\Policies;

class ProjectPolicy extends Policy
{
	function any(): bool
	{
		if (!$user = auth()) {
			return false;
		}
		return $user->admin;
	}

}