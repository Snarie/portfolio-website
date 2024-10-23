<?php

namespace App\Models;

use PDO;

abstract class Model
{
	protected string $table;

	public int $id;
	public ?string $created_at;
	public ?string $updated_at;

	protected array $guarded = ['created_at', 'updated_at']; // fields that can't be assigned

	// =====================================
	// CRUD Operations
	// =====================================

	/**
	 * Fetches all records from the model's table.
	 *
	 * @return array An array of model instances.
	 */
	public static function all(): array
	{
		$instance = new static();
		$query = "SELECT * FROM $instance->table";
		$stmt = conn()->prepare($query);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
		return $stmt->fetchAll();
	}

	/**
	 * Fills the model's properties that are not guarded.
	 *
	 * @param array $data Data to fill the model with.
	 * @return void
	 */
	protected function fill(array $data): void {
		foreach ($data as $key => $value) {
			if (!in_array($key, $this->guarded)) {
				$this->$key = $value;
			}
		}
	}

	/**
	 * Create a record in the database from the provided data.
	 *
	 * @param array $data Data to create a record.
	 * @return static The newly created model instance.
	 */
	public static function create(array $data): self {
		$instance = new static();
		$fields = array_keys($data);
		$placeholders = array_fill(0, count($fields), '?');
		$values = array_values($data);

		$sql = "INSERT INTO $instance->table (". implode(', ', $fields) . ") 
				VALUES (" . implode(', ', $placeholders) . ");";
		$stmt = conn()->prepare($sql);
		$stmt->execute($values);

		$data['id'] = conn()->lastInsertId();
		return $instance->find($data['id']);
	}

	/**
	 * Finds a record by its ID.
	 *
	 * @param mixed $id The primary key of the record to find.
	 * @return static|null The model instance or null if not found.
	 */
	public static function find(mixed $id): ?self
	{
		$instance = new static();
		$sql = "SELECT * FROM $instance->table WHERE id = ?";
		$stmt = conn()->prepare($sql);
		$stmt->execute([$id]);
		$stmt->setFetchMode(PDO::FETCH_INTO, $instance);
		return $stmt->fetch() ?: null;
	}

	/**
	 * Updates the record from the database to the provided data.
	 *
	 * @param array $data Data to create a record.
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function update(array $data): bool
	{
		$fields = array_keys($data);
		$placeholders = array_map(fn($field) => "$field = ?", $fields);
		$values = array_values($data);
		$values[] = $this->id; // add ID to the values array for the WHERE clause.

		$sql = "UPDATE $this->table SET " . implode(', ', $placeholders) . "WHERE id = ?";
		$stmt = conn()->prepare($sql);
		return $stmt->execute($values);
	}

	/**
	 * Deletes the record from the database.
	 *
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function delete(): bool
	{
		$sql = "DELETE FROM $this->table WHERE id = ?";
		$stmt = conn()->prepare($sql);
		return $stmt->execute([$this->id]);
	}

	// =====================================
	// SQL Relationship Functions
	// =====================================

	/**
	 * Retrieves the related model for a belongs-to relationship.
	 *
	 * @param string $relatedClass The related class name.
	 * @param string $foreignKey The foreign key pointing to the related model.
	 * @return mixed The related model instance or null if not found
	 */
	protected function belongsTo(string $relatedClass, string $foreignKey): mixed
	{
		$relatedInstance = new $relatedClass();
		$sql = "SELECT * FROM $relatedInstance->table WHERE id = ?";
		$stmt = conn()->prepare($sql);
		$stmt->execute([$this->$foreignKey]);
		$stmt->setFetchMode(PDO::FETCH_CLASS, $relatedClass);
		return $stmt->fetch();
	}

	/**
	 * Retrieves all related models for a one-to-many relationship.
	 *
	 * @param string $relatedClass The related class name.
	 * @param string|null $foreignKey The foreign key in the related table.
	 * @return array An array of related model instances.
	 */
	protected function oneToMany(string $relatedClass, ?string $foreignKey = null): array
	{
		$relatedInstance = new $relatedClass();
		if (!$foreignKey) {
			$shortClassName = (new \ReflectionClass($this))->getShortName();
			$foreignKey = strTolower($shortClassName) . '_id';
		}
		$sql = "SELECT * FROM $relatedInstance->table WHERE $foreignKey = ?";
		$stmt = conn()->prepare($sql);
		$stmt->execute([$this->id]);
		$stmt->setFetchMode(PDO::FETCH_CLASS, $relatedClass);
		return $stmt->fetchAll();
	}


}