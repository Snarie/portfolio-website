<?php

namespace App\Requests;

class UpdateProjectRequest extends Request
{
	function authorize(): bool
	{
		return true;
	}

	function rules(): array
	{
		return [
			'name' => 'required|string|max:255',
			'description' => 'required|string|max:65535',
			'github_link' => 'required|string|max:255',
			'cropped_image' => 'string',
			'start_date' => 'required|date',
			'end_date' => 'date|after:start_date',
			'tools' => 'array',
		];
	}
}