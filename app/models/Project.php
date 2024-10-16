<?php

namespace App\Models;

class Project extends Model
{
	public string $name;
	public ?string $image_link;
	public ?string $description;
	public ?string $start_date;
	public ?string $end_date;
	public ?string $created_at;
	public ?string $updated_at;

	public function __construct()
	{
		$this->table = 'projects';
	}

	protected function create(): bool
	{
		$query = "INSERT INTO $this->table (name, description, image_link, start_date, end_date) 
                  VALUES (:name, :description, :image_link, :start_date, :end_date)";
		$stmt = conn()->prepare($query);

		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':description', $this->description);
		$stmt->bindParam(':image_link', $this->image_link);
		$stmt->bindParam(':start_date', $this->start_date);
		$stmt->bindParam(':end_date', $this->end_date);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}

	protected function update(): bool
	{
		$query = "UPDATE $this->table SET name = :name, description = :description, image_link = :image_link, 
                  start_date = :start_date, end_date = :end_date WHERE id = :id";
		$stmt = conn()->prepare($query);

		$stmt->bindParam(':id', $this->id);
		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':description', $this->description);
		$stmt->bindParam(':image_link', $this->image_link);
		$stmt->bindParam(':start_date', $this->start_date);
		$stmt->bindParam(':end_date', $this->end_date);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}
	public static function find($id): Project {
		$instance = new Project();
		$instance-> id = $id;
		$result = $instance->readOne();
		$instance->name = $result['name'];
		$instance->description = $result['description'];
		$instance->image_link = $result['image_link'];
		$instance->start_date = $result['start_date'];
		$instance->end_date = $result['end_date'];
		$instance->created_at = $result['created_at'];
		$instance->updated_at = $result['updated_at'];

		return $instance;
	}
}