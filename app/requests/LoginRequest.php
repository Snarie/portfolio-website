<?php

namespace App\Requests;

class LoginRequest extends Request
{

	function rules(): array
	{
		return [
			'email' => 'required|string|max:255',
			'password' => 'required|string|min:8|max:255',
		];
	}
}