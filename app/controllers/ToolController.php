<?php

namespace App\Controllers;

use App\Models\Tool;
use App\Requests\StoreToolRequest;
use App\Requests\UpdateToolRequest;
use App\Responses\Response;

class ToolController extends Controller
{
	protected function index(): Response
	{
		$tools = Tool::all();
		return view('formpage/tools.index', ['tools' => $tools]);
	}

	protected function store(StoreToolRequest $request): Response
	{
		if (!$request->validate()) {
			return redirect('tools.index')->with('errors', $request->getErrors());
		}

		Tool::create([
			'name' => $request->get('name')
		]);

		return redirect('tools.index')->with('success', 'Tool created successfully');
	}
	protected function update(UpdateToolRequest $request, Tool $tool): Response
	{
		if (!$request->validate()) {
			return redirect('tools.index')->with('fail', 'Update can\'t be made');
		}

		$tool->update([
			'name' => $request->get('name')
		]);

		return redirect('tools.index')->with('success', 'Updated tool successfully');
	}

	protected function delete(Tool $tool): Response
	{
		if ($tool->delete()) {
			return redirect('tools.index')->with('success', 'Tool deleted successfully');
		} else {
			return redirect('tools.index')->with('fail', 'Tool could not be deleted');
		}

	}
}