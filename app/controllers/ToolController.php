<?php

namespace App\Controllers;

use App\Models\Tool;
use App\Requests\StoreToolRequest;
use App\Responses\Response;

class ToolController extends Controller
{
	protected function create(): Response
	{
		return view('formpage/tools.create');
	}

	protected function store(StoreToolRequest $request): Response
	{
		if(!$request->validate()) {
			return redirect('tools.create')->with('errors', $request->getErrors());
		}

		Tool::create([
			'name' => $request->get('name')
		]);

		return redirect('tools.create')->with('success', 'Tool created successfully');
	}
}