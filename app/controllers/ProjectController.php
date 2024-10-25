<?php

namespace App\Controllers;

use App\Requests\StoreProjectRequest;
use App\Responses\Response;
use App\Models\Project;
use App\Models\ProjectTool;
use App\Models\Tool;
use App\Requests\UpdateProjectRequest;

class ProjectController extends Controller
{
	public function index(): Response
	{
		$projects = Project::all();
		return view('projects.index', ['projects' => $projects]);
	}

	public function create(): Response
	{
		$tools = Tool::all();
		return view('formpage/projects.create', ['tools' => $tools]);
	}

	public function store(StoreProjectRequest $request): Response
	{
		if(!$request->validate()) {
			return redirect('projects.create')->with('errors', $request->getErrors());
		}

		$imagePath = saveImage($request->get('cropped_image'), 16/9);

		$project = Project::create([
			'name' => $request->get('name'),
			'description' => $request->get('description'),
			'start_date' => $request->get('start_date'),
			'end_date' => $request->get('end_date'),
			'image_link' => $imagePath
		]);

		foreach ($request->get('tools') as $toolId) {
			ProjectTool::create([
				'project_id' => $project->id,
				'tool_id' => $toolId
			]);
		}
		return redirect("projects.show", ['project' => $project->id])->with('success', 'Project created successfully.');
	}

	public function show(Project $project): Response
	{
		$tools = $project->tools();
		return view('projects.show', ['project' => $project, 'tools' => $tools]);
	}

	public function edit(Project $project): Response
	{
		$tools = Tool::all();
		return view('formpage/projects.edit', ['project' => $project, 'tools' => $tools]);
	}

	public function update(UpdateProjectRequest $request, Project $project): Response
	{
		$name = $_POST['name'] ?? null;

		$description = $_POST['description'] ?? null;

		$start_date = $_POST['start_date'] ?? null;

		$end_date = $_POST['end_date'] ?? null;

		$toolIds = $_POST['tools'] ?? [];

		$imagePath = null;

		if (isset($_POST['cropped_image'])) {
			$croppedImage = $_POST['cropped_image'];
			$imagePath = saveImage($croppedImage, 16/9);
		}

		foreach ($project->projectTools() as $projectTool) {
			$projectTool->delete();
		}

		$project->update([
			'name' => $name,
			'description' => $description,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'image_link' => $imagePath
		]);

		foreach ($toolIds as $toolId) {
			ProjectTool::create([
				'project_id' => $project->id,
				'tool_id' => $toolId
			]);
		}

		return redirect("projects.show", ['project' => $project->id])->with('update', 'Project updated successfully.');
	}

	public function destroy(Project $project): Response
	{
		return redirect('projects.index');
	}

}