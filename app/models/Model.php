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
	 * @return static[] An array of model instances.
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
	public static function create(array $data): static {
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
	public static function find(mixed $id): ?static
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
	// Query Helper Methods
	// =====================================

	protected array $queryConditions = [];
	protected ?string $queryOrderBy = null;
	protected ?int $queryLimit = null;

	/**
	 * Adds a where condition to the query
	 *
	 * @param string $field The field to check.
	 * @param mixed $value The value to match.
	 * @return static The instance with the condition.
	 */
	public static function where(string $field, mixed $value): static
	{
		$instance = new static();
		$instance->queryConditions[] = [$field, '=', $value];
		return $instance;
	}

	/**
	 * Sets the order by the condition.
	 *
	 * @param string $field The field to order by.
	 * @param string $direction The direction of the order (ASC, DESC).
	 * @return $this The instance with the ordering.
	 */
	public function orderBy(string $field, string $direction = 'ASC'): static
	{
		$this->queryOrderBy = "$field $direction";
		return $this;
	}

	/**
	 * Sets a limit for the query.
	 *
	 * @param int $limit The amount of results to limit to.
	 * @return $this The instance with the limit.
	 */
	public function limit(int $limit): static
	{
		$this->queryLimit = $limit;
		return $this;
	}

	/**
	 * Fetches the first result that matches the conditions.
	 *
	 * @return static|null The first result or null if not found
	 */
	public function first(): ?static
	{
		if (empty($this->queryConditions)) {
			return null;
		}

		$sql = "SELECT * FROM $this->table WHERE " . $this->buildConditions();
		if ($this->queryOrderBy) {
			$sql .= " ORDER BY " . $this->queryOrderBy;
		}
		$sql .= " LIMIT 1";
		$stmt = conn()->prepare($sql);
		$stmt->execute($this->getConditionValues());
		$stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
		return $stmt->fetch() ?: null;
	}

	/**
	 * Fetches all results matching the conditions.
	 *
	 * @return static[] An array of matching model instances.
	 */
	public function get(): array
	{
		$sql = "SELECT * FROM $this->table";
		if (!empty($this->queryConditions)) {
			$sql .= " WHERE " . $this->buildConditions();
		}
		if ($this->queryOrderBy) {
			$sql .= " ORDER BY " . $this->queryOrderBy;
		}
		if ($this->queryLimit) {
			$sql .= " LIMIT " . $this->queryLimit;
		}
		$stmt = conn()->prepare($sql);
		$stmt->execute($this->getConditionValues());
		$stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
		return $stmt->fetchAll();
	}

	/**
	 * Check if any records exists for the conditions.
	 *
	 * @return bool True if records exists, otherwise false.
	 */
	public function exists(): bool
	{
		if (empty($this->queryConditions)) {
			return false;
		}

		$sql = "SELECT EXISTS (SELECT 1 FROM $this->table WHERE " . $this->buildConditions() . ")";
		$stmt = conn()->prepare($sql);
		$stmt->execute($this->getConditionValues());
		return (bool) $stmt->fetchColumn();
	}

	/**
	 * Builds the WHERE clause for conditions.
	 *
	 * @return string The WHERE clause.
	 */
	private function buildConditions(): string
	{
		return implode(' AND ', array_map(fn($cond) => "$cond[0] $cond[1] ?", $this->queryConditions));
	}

	/**
	 * Extracts values from the conditions.
	 *
	 * @return array Condition values.
	 */
	private function getConditionValues(): array
	{
		return array_map(fn($cond) => $cond[2], $this->queryConditions);
	}

	// =====================================
	// SQL Relationship Functions
	// =====================================

	/**
	 * Retrieves the related model for a belongs-to relationship.
	 *
	 * @template T of Model
	 * @param class-string<T> $relatedClass The related class name.
	 * @param string $foreignKey The foreign key pointing to the related model.
	 * @return T|null The related model instance or null if not found
	 */
	protected function belongsTo(string $relatedClass, string $foreignKey)
	{
		$relatedInstance = new $relatedClass();
		$sql = "SELECT * FROM $relatedInstance->table WHERE id = ?";
		$stmt = conn()->prepare($sql);
		$stmt->execute([$this->$foreignKey]);
		$stmt->setFetchMode(PDO::FETCH_CLASS, $relatedClass);
		return $stmt->fetch() ?: null;
	}

	/**
	 * Retrieves all related models for a one-to-many relationship.
	 *
	 * @template T of Model
	 * @param class-string<T> $relatedClass The related class name, must extend Model.
	 * @param string|null $foreignKey The foreign key in the related table.
	 * @return T[] An array of related model instances.
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