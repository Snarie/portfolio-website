<?php

namespace App\Models;

use PDO;
use PDOStatement;

abstract class Model
{
	protected string $table;
	public int $id;

	public function readAll(): PDOStatement
	{
		$query = "SELECT * FROM " . $this->table;
		$stmt = conn()->prepare($query);
		$stmt->execute();
		return $stmt;
	}

	public function readOne(): ?array
	{
		$query = "SELECT * FROM {$this->table} WHERE id = {$this->id};";
		$stmt = conn()->prepare($query);
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	abstract protected function create(): bool;

	abstract protected function update(): bool;

	public function delete(): bool
	{
		$query = "DELETE FROM " . $this->table . "WHERE id = :id";
		$stmt = conn()->prepare($query);
		$stmt->bindParam(':id', $this->id);

		if ($stmt->execute()) {
			return true;
		}
		return false;
	}

	abstract public static function find($id): self;
}