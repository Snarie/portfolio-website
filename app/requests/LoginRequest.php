<?php

namespace App\Requests;

class LoginRequest extends Request
{
	public function authorize(): bool
	{
		return true;
	}

	function rules(): array
	{
		return [
			'email' => 'required|string|max:255',
			'password' => 'required|string|min:8|max:255',
		];
	}
}