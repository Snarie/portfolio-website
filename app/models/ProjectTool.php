<?php

namespace App\Models;

class ProjectTool extends Model
{
	public ?int $project_id;
	public ?int $tool_id;

	/**
	 * Constructor for the ProjectTool model.
	 * Can optionally initialize model properties.
	 * @param array|null $data Optional data to instantiate the model properties.
	 */
	public function __construct(?array $data = null)
	{
		$this->table = 'project_tools';
		if ($data) {
			$this->fill($data);
		}
	}

	/**
	 * Retrieves the Project associated with the ProjectTool.
	 * @return Project|null The associated Project instance or null if not found.
	 */
	public function project(): ?Project
	{
		return $this->belongsTo(Project::class, 'project_id');
	}

	/**
	 * Retrieves the Tool associated with the ProjectTool.
	 * @return Tool|null The associated Tool instance or null if not found.
	 */
	public function tool(): ?Tool
	{
		return $this->belongsTo(Tool::class, 'tool_id');
	}
}