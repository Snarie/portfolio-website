<?php

class RenameUsernameColumnInUsersTable {
	public function up(PDO $conn): void {
		$sql = "ALTER TABLE users CHANGE COLUMN username name VARCHAR(255) NOT NULL";
		$conn->exec($sql);
	}

	public function down(PDO $conn): void {
		$sql = "ALTER TABLE users CHANGE COLUMN name username VARCHAR(255) NOT NULL";
		$conn->exec($sql);
	}
}