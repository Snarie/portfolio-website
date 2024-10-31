<?php

namespace App\Requests;

class FilterProjectRequest extends Request
{

	function authorize(): bool
	{
		return true;
	}

	function rules(): array
	{
		return [
			'tool' => 'int'
		];
	}
}