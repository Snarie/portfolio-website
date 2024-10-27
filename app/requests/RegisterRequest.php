<?php

namespace App\Requests;

class RegisterRequest extends Request
{

	function rules(): array
	{
		return [
			'username' => 'required|string|min:3|max:50',
			'email' => 'required|string|max:255',
			'password' => 'required|string|min:8|max:255',
			'password_confirmation' => 'required|string|min:8|max:255',
		];
	}
}