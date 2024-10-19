<?php

class MigrationManager {
	/**
	 * @var PDO Stores the pdo connection
	 */
	private PDO $conn;

	/**
	 * @param PDO $conn PDO connection for initializing
	 */
	public function __construct(PDO $conn) {
		$this->conn = $conn;
	}

	/**
	 * Get a list of all applied migrations from the 'migrations' table.
	 *
	 * @return array|false Array of applied migrations, otherwise false
	 */
	private function getAppliedMigrations(): ?array {
		$stmt = $this->conn->query("SELECT migration FROM migrations");
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * Save the migration record to the 'migrations' table.
	 *
	 * @param string $migration The migration file to save
	 * @return void
	 */
	private function saveMigration(string $migration): void {
		$stmt = $this->conn->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
		$stmt->execute(['migration' => $migration]);
	}

	/**
	 * Remove the migration record from the 'migrations' table.
	 *
	 * @param string $migration The migration file to remove
	 * @return void
	 */
	private function removeMigration(string $migration): void {
		$stmt = $this->conn->prepare("DELETE FROM migrations WHERE migration = :migration");
		$stmt->execute(['migration' => $migration]);
	}

	/**
	 * Extract the class name from the migration filename by removing the timestamp.
	 *
	 * @param string $migrationFile The migration file name
	 * @return string Returns the class name
	 */
	private function getMigrationClass(string $migrationFile): string {
		// Remove the timestamp (everything before the first word) from the filename.
		$migrationName = preg_replace('/^[0-9_]+/', '', $migrationFile);

		// Convert the remaining part (e.g., create_users_table) to PascalCase (e.g., CreateUsersTable)
		$className = str_replace('_', '', ucwords($migrationName, '_'));

		// Return the class name without the .php extension
		return (string) pathinfo($className, PATHINFO_FILENAME);
	}

	/**
	 * Run all pending migrations.
	 *
	 * @return void
	 */
	public function migrate(): void {
		$appliedMigrations = $this->getAppliedMigrations();

		// Scan the migrations directory for migration files
		$migrationFiles = array_diff(scandir('migrations'), ['.', '..']);
		$pendingMigrations = array_diff($migrationFiles, $appliedMigrations);

		if (empty($pendingMigrations)) {
			echo "No new migrations to apply.\n";
			return;
		}
		foreach ($pendingMigrations as $migrationFile) {
			require_once 'migrations/' . $migrationFile;

			$migrationClass = $this->getMigrationClass($migrationFile);
			$migration = new $migrationClass();

			if (class_exists($migrationClass)) {
				$migration = new $migrationClass();

				// Run the `up()` method of the migration
				$migration->up($this->conn);

				// Record the migration in the `migrations` table
				$this->saveMigration($migrationFile);
				echo "Migrated: " . $migrationFile . "\n";
			} else {
				echo "Migration class $migrationClass not found for file $migrationFile.\n";
			}
		}
	}

	/**
	 * Rollback the last migration.
	 *
	 * @return void
	 */
	public function rollback(): void {
		$appliedMigrations = $this->getAppliedMigrations();

		if (empty($appliedMigrations)) {
			echo "No migrations to rollback.\n";
			return;
		}

		// Rollback the last applied migration
		$lastMigration = end($appliedMigrations);
		require_once 'migrations/' . $lastMigration;

		$migrationClass = $this->getMigrationClass($lastMigration);

		if (class_exists($migrationClass)) {
			$migration = new $migrationClass();

			$migration->down($this->conn);

			$this->removeMigration($lastMigration);

			echo "Rolled back: $lastMigration \n";
		} else {
			echo "Migration class $migrationClass not found for file $lastMigration.\n";
		}
	}
}

$manager = new MigrationManager(require 'db.php');

// $argc is provided by the parameters in the terminal
if ($argc > 1) {
	switch ($argv[1]) {
		case 'migrate':
			$manager->migrate();
			break;
		case 'rollback':
			$manager->rollback();
			break;
		default:
			echo "Unknown command: $argv[1]\n";
			break;
	}
} else {
	echo "Please provide a command (e.g., 'migrate' or 'rollback').\n";
}



