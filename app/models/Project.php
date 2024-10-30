<?php

namespace App\Models;

class Project extends Model
{
	public string $name;
	public ?string $image_link;
	public ?string $github_link;
	public ?string $description;
	public ?string $start_date;
	public ?string $end_date;


	/**
	 * Constructor for the Project model.
	 * Can optionally initialize model properties.
	 * @param array|null $data Optional data to instantiate the model properties.
	 */
	public function __construct(?array $data = null)
	{
		$this->table = 'projects';
		if ($data) {
			$this->fill($data);
		}
	}

	/**
	 * Retrieves all ProjectTool instances associated with this project.
	 * @return ProjectTool[] An array of ProjectTool instances.
	 */
	public function projectTools(): array
	{
		return $this->oneToMany(ProjectTool::class, 'project_id');
	}

	/**
	 * Retrieves all Tool instances associated with this project through ProjectTools.
	 * @return Tool[] An array of Tool instances.
	 */
	public function tools(): array {
		$projectTools = $this->projectTools();

		/** @var Tool[] $tools */
		$tools = [];
		foreach ($projectTools as $projectTool) {
			$tool = $projectTool->tool();
			if ($tool) {
				$tools[] = $tool;
			}
		}
		return $tools;
	}

	/**
	 * Retrieves the latest projects based on the creation date.
	 * @param int $limit The amount the latest projects to retrieve.
	 * @return static[] An array of the latest Project instances.
	 */
	public static function latestProjects(int $limit = 3): array
	{
		$instance = new static();
		return $instance->orderBy('created_at', 'DESC')->limit($limit)->get();
	}

}