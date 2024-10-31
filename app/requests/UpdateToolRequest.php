<?php

namespace App\Requests;

class UpdateToolRequest extends Request
{
	function authorize(): bool
	{
		if (!$user = auth()) {
			return false;
		}
		return $user->admin;
	}

	function rules(): array
	{
		return [
			'name' => 'required|string|max:50|unique:App\Models\Tool',
		];
	}
}