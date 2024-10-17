<?php

namespace App\Controllers;

use App\Responses\Response;
use App\Models\Project;
use App\Models\Tool;
use App\Models\ProjectTool;
use PDO;

class ProjectController extends Controller
{
	private PDO $conn;

	public function __construct()
	{
		$this->conn = conn();
	}

	public function index(): Response
	{
		return view('projects.index');
	}

	public function create(): Response
	{
		$stmt = $this->conn->query("SELECT id, name FROM tools ORDER BY name");
		$tools = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return view('formpage/projects.create', ['tools' => $tools]);
	}

	public function store(): Response
	{
		$name = $_POST['name'] ?? null;

		$description = $_POST['description'] ?? null;

		$start_date = $_POST['start_date'] ?? null;

		$end_date = $_POST['end_date'] ?? null;

		$tools = $_POST['tools'] ?? [];

		if (isset($_POST['disable_end_date'])) $end_date = null;

		if (!$name || !$description || !$start_date) {
			// Redirect back to the create form if required fields are missing
			return redirect('projects.create');
		}

		$imagePath = null;

		if (isset($_POST['cropped_image'])) {
			$croppedImage = $_POST['cropped_image'];

			$croppedImage = str_replace('data:image/jpeg;base64,' , '', $croppedImage);
			$croppedImage = str_replace(' ', '+', $croppedImage);

			$decodedImage = base64_decode($croppedImage);

			$imageFileName = uniqid() . '.jpg';
			$imagePath = 'public/uploads/projects/' . $imageFileName;
			$fileFullPath = path('public', 'uploads', 'projects', $imageFileName);

			//$fileFullPath = __DIR__ . '/../public/' . $imagePath;

			if (!file_exists(dirname($fileFullPath))) {
				mkdir(dirname($fileFullPath), 0777, true);
			}

			file_put_contents($fileFullPath, $decodedImage);
		}

		$project = Project::create([
			'name' => $name,
			'description' => $description,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'image_link' => $imagePath
		]);

		foreach ($tools as $toolId) {
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
		//echo print_r($tools) . "<br>";
		return view('projects.show', ['project' => $project, 'tools' => $tools]);
	}

	public function edit(Project $project): Response
	{
		return view('projects.edit', ['id' => $project->id]);
	}

	public function update(Project $project): Response
	{
		return redirect('projects.index');
	}

	public function destroy(Project $project): Response
	{
		return redirect('projects.index');
	}

}