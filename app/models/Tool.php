<?php

namespace App\Models;

class Tool extends Model
{
	public string $name;

	/**
	 * Constructor for the Tool model.
	 * Can optionally initialize model properties.
	 * @param array|null $data Optional data to instantiate the model properties.
	 */
	public function __construct(?array $data = null)
	{
		$this->table = 'tools';
		if ($data) {
			$this->fill($data);
		}
	}

	/**
	 * Retrieves all ProjectTool instances associated with this project.
	 * @return array An array of ProjectTool instances.
	 */
	public function projectTools(): array
	{
		return $this->oneToMany(ProjectTool::class, 'tool_id');
	}

	/**
	 * Retrieves all Project instances associated with this tool through ProjectTools.
	 * @return array An array of Project instances.
	 */
	public function projects(): array {
		$projectTools = $this->projectTools();
		// Iterate over all projectTools and retrieve their relative Project.
		$projects = [];
		foreach ($projectTools as $projectTool) {
			$project = $projectTool->project();
			if ($project) {
				$projects[] = $project;
			}
		}
		return $projects;
	}

}