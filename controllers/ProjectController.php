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
		return template('default.php', view('projects/index.view.php'));
	}

	public function create(): bool
	{
		$stmt = $this->conn->query("SELECT id, name FROM tools ORDER BY name");
		$tools = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return template('formpage.php', view('projects/create.view.php'), ['tools' => $tools]);
	}

	public function store(): bool
	{
		$name = $_POST['name'] ?? null;

		$description = $_POST['description'] ?? null;

		$start_date = $_POST['start_date'] ?? null;

		$end_date = $_POST['end_date'] ?? null;
		if (isset($_POST['disable_end_date'])) $end_date = null;

		$tools = $_POST['tools'] ?? [];

		if (!$name || !$description || !$start_date) {
			// If required fields are missing, redirect back to the create form
			return $this->create();
		}

		$stmt = $this->conn->prepare("INSERT INTO projects (name, description, start_date, end_date) VALUES (:name, :description, :start_date, :end_date)");
		$stmt->execute([
			':name' => $name,
			':description' => $description,
			':start_date' => $start_date,
			':end_date' => $end_date
		]);

		// Get the ID of the newly created project
		$projectId = $this->conn->lastInsertId();

		$stmt= $this->conn->prepare("INSERT INTO project_tools (project_id, tool_id) VALUES (:project_id, :tool_id)");

		foreach ($tools as $toolId) {
			$stmt->execute([
				':project_id' => $projectId,
				'tool_id' => $toolId
			]);
		}

		header("Location: /projects/$projectId");
		exit();

		return true;
//		return $this->show($projectId);
	}

	public function show(int $id): bool
	{
		return template('default.php', view('projects/show.view.php'), ['id' => $id]);
	}

	public function edit(int $id): bool
	{
		return template('default.php', view('projects/edit.view.php'), ['id' => $id]);
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