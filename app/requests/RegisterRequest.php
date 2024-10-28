<?php

namespace App\Requests;

class RegisterRequest extends Request
{

	function rules(): array
	{
		return [
			'name' => 'required|string|min:3|max:50|unique:App\Models\User',
			'email' => 'required|string|max:255|unique:App\Models\User',
			'password' => 'required|string|min:8|max:255',
			'password_confirmation' => 'required|string|min:8|max:255|matches:password',
		];
	}
}