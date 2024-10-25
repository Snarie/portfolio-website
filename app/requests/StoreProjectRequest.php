<?php

namespace App\Requests;

class StoreProjectRequest extends Request
{

	function rules(): array
	{
		return [
			'name' => 'required|int|max:255',
			'github_link' => 'string|max:255',
			'description' => 'string|max:65535',
			'start_date' => 'date',
			'end_date' => 'date|after:start_date',
			'tools' => 'array',
			'cropped_image' => 'required|string'
		];
	}
}