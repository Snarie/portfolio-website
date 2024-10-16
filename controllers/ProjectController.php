<?php

namespace controllers;
use PDO;

class ProjectController
{
	private PDO $conn;

	public function __construct()
	{
		$this->conn = conn();
	}

	public function index(): bool
	{
		return route(view('projects/index'));
	}

	public function create(): bool
	{
		$stmt = $this->conn->query("SELECT id, name FROM tools ORDER BY name");
		$tools = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return route(view('projects/create', 'formpage'), ['tools' => $tools]);
	}

	public function store(): bool
	{
		$name = $_POST['name'] ?? null;

		$description = $_POST['description'] ?? null;

		$start_date = $_POST['start_date'] ?? null;

		$end_date = $_POST['end_date'] ?? null;

		$tools = $_POST['tools'] ?? [];

		if (isset($_POST['disable_end_date'])) $end_date = null;

		if (!$name || !$description || !$start_date) {
			// If required fields are missing, redirect back to the create form
			return $this->create();
		}

		$imagePath = null;

		if (isset($_POST['cropped_image'])) {
			$croppedImage = $_POST['cropped_image'];

			$croppedImage = str_replace('data:image/jpeg;base64,' , '', $croppedImage);
			$croppedImage = str_replace(' ', '+', $croppedImage);

			$decodedImage = base64_decode($croppedImage);

			$imageFileName = uniqid() . '.jpg';
			$imagePath = 'uploads/projects/' . $imageFileName;
			$fileFullPath = __DIR__ . '/../public/' . $imagePath;

			if (!file_exists(dirname($fileFullPath))) {
				mkdir(dirname($fileFullPath), 0777, true);
			}

			file_put_contents($fileFullPath, $decodedImage);
		}

		$stmt = $this->conn->prepare(
			"INSERT INTO projects (name, description, start_date, end_date, image_link) VALUES (:name, :description, :start_date, :end_date, :image_link)"
		);
		$stmt->execute([
			':name' => $name,
			':description' => $description,
			':start_date' => $start_date,
			':end_date' => $end_date,
			':image_link' => $imagePath
		]);

		// Get the ID of the newly created project
		$projectId = $this->conn->lastInsertId();

		$stmt= $this->conn->prepare("INSERT INTO project_tools (project_id, tool_id) VALUES (:project_id, :tool_id)");

		foreach ($tools as $toolId) {
			$stmt->execute([
				':project_id' => $projectId,
				':tool_id' => $toolId
			]);
		}

		header("Location: /projects/$projectId");
		exit();

		return true;
//		return $this->show($projectId);
	}

	public function show(int $id): bool
	{
		return route(view('projects/show'), ['id' => $id]);
	}

	public function edit(int $id): bool
	{
		return route(view('projects/edit'), ['id' => $id]);
	}

	public function update(int $id): bool
	{
		return $this->show($id);
	}

	public function destroy(int $id): bool
	{
		return $this->index();
	}

}